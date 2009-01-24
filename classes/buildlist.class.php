<?PHP

	class BuildList implements IteratorAggregate
	{
		private $entityId;
		private $ownerId;

		private $items = null;
		private $itemStatus = null;	
		private $count = null;
		
		private $totalPeopleWorking = null;
		
		private $errorMsg;
		
		function BuildList($entityId,$ownerId,$load=0)
		{
			$this->entityId = $entityId;
			$this->ownerId = $ownerId;
			if ($load==1)
				$this->load();
		}
	
	  public function getIterator() 
  	{
  		if ($this->items == null)
  			$this->load();
    	return new ArrayIterator($this->items);
  	}
	
		private function load()
		{
			$this->items = array();
			$this->itemStatus = array();
			$this->count = 0;
			
			$res = dbquery("
			SELECT	
				l.buildlist_id,
				l.buildlist_current_level,
				l.buildlist_deactivated,
				l.buildlist_cooldown,
				l.buildlist_people_working,
				l.buildlist_prod_percent,
				i.*
			FROM 
				buildlist l
			INNER JOIN
				buildings i
			ON
				l.buildlist_building_id = i.building_id
				AND l.buildlist_entity_id='".$this->entityId."'
				AND l.buildlist_current_level>0;");
			if (mysql_num_rows($res)>0)
			{		
				while ($arr = mysql_fetch_assoc($res))
				{
					$this->items[$arr['building_id']] = new Building($arr);
					$this->itemStatus[$arr['building_id']] = array(
						'listid' => $arr['buildlist_id'],
						'level' => $arr['buildlist_current_level'],
						'deactivated' => $arr['buildlist_deactivated'],
						'cooldown' => $arr['buildlist_cooldown'],
						'people_working' => $arr['buildlist_people_working'],
						'prod_percent' => $arr['buildlist_prod_percent']						
					);
					$this->count++;
				}
			}			
		}			
		
		function count()
		{
			if ($this->count != null)
				return $this->count;
			if ($this->items != null)
			{
				$this->count = count($this->items);
				return $this->count;
			}
			$res = dbquery("
			SELECT
				COUNT(buildlist_id)
			FROM
				buildlist
			WHERE
				buildlist_user_id=".$this->userId ."
				AND buildlist_entity_id=".$this->entityId."
			;");
			$arr = mysql_fetch_row($res);
			$this->count = $arr[0];
			return $this->count;
		}							
			
		function item($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
				return $this->items[$bid];
			return false;			
		}
			
		function getLevel($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
				return $this->itemStatus[$bid]['level'];
			return 0;
		}	

		function getDeactivated($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
			{
				if ($this->itemStatus[$bid]['deactivated'] > time())
					return $this->itemStatus[$bid]['deactivated'];
			}
			return false;	
		}	
		
		function getPeopleWorking($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
				return $this->itemStatus[$bid]['people_working'];
			return 0;
		}				
		
		function getCooldown($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
			{
				if ($this->itemStatus[$bid]['cooldown'] > time())
					return $this->itemStatus[$bid]['cooldown'];
			}
			return false;	
		}		
		
		function setCooldown($bid,$cd)			
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
			{
				$this->itemStatus[$bid]['cooldown'] = $cd;
				$res = dbquery("
				UPDATE
					buildlist 
				SET
					buildlist_cooldown=".$cd."
				WHERE 
					buildlist_id=".$this->itemStatus[$bid]['listid'].";");				
			}
		}		
		
		function totalPeopleWorking()
		{
			if ($this->totalPeopleWorking!=null)
				return $this->totalPeopleWorking;
			if ($this->items!=null)
			{
				$this->totalPeopleWorking = 0;
				foreach ($this->itemStatus as $k=>&$v)
				{
					$this->totalPeopleWorking+=$v['people_working'];
				}
				unset($v);
				return $this->totalPeopleWorking;
			}
			$res = dbquery("
			SELECT 
				SUM(buildlist_people_working) 
			FROM 
				buildlist
			WHERE 
				buildlist_entity_id='".$this->entityId."';");
			$pbarr = mysql_fetch_row($res);
			$this->totalPeopleWorking = $pbarr[0];
			return $this->totalPeopleWorking;			
		}
		
		function getBuildTime($itemId)
		{
			global $cu,$cp;
			if ($cu->id != $this->ownerId)
				$u = new User($this->ownerId);
			else
				$u = &$cu;
			if ($cp->id != $this->entityId)
				$e = Entity::createFactoryById($this->entityId);
			else
				$e = &$cp;

			// Calc bonus			
			$bonus = $u->race->buildTime + $e->typeBuildtime + $e->starBuildtime - 2;
			
			if (isset($this->items[$itemId]))
			{
				$itm = &$this->items[$itemId];
				$cst = $itm->getCosts($this->itemStatus[$itemId]['level']+1);
			}
			else
			{
				$itm = new Building($itemId);
				$cst = $itm->getCosts(1);
			}
			$btime = ($cst[1]+$cst[2]+$cst[3]+$cst[4]+$cst[5]) / GLOBAL_TIME * BUILD_BUILD_TIME;
			$btime *= $bonus;		
			
			unset($itm);
			unset($cst);
			unset($e);
			unset($u);
			return $btime;
		}
		
		function checkBuildable($itemId)
		{
			global $cu,$cp;
			if ($cp->id != $this->entityId)
				$e = Entity::createFactoryById($this->entityId);
			else
				$e = &$cp;
			if (isset($this->items[$itemId]))
			{
				$itm = &$this->items[$itemId];
				$cst = $itm->getCosts($this->itemStatus[$itemId]['level']+1);
				$lvl = $this->itemStatus[$itemId]['level'];
			}
			else
			{
				$itm = new Building($itemId);
				$cst = $itm->getCosts(1);
				$lvl = 0;
			}
			
			// Check level
			if ($lvl < $itm->maxLevel)
			{
				// Check costs
				if ($cst[1] <= $e->getRes(1) 
				&& $cst[2] <= $e->getRes(2) 
				&& $cst[3] <= $e->getRes(3) 
				&& $cst[4] <= $e->getRes(4) 
				&& $cst[5] <= $e->getRes(5))
				{
					$req = $itm->getBuildingRequirements();
					foreach ($req as $rk=>$rv)
					{
						if ($rv > $this->getLevel($rk))
						{
							$this->errorMsg = "Voraussetzungen nicht erfüllt!";
							return false;
						}
					}
					return true;					
				}
				else
					$this->errorMsg = "Zuwenig Rohstoffe vorhanden!";
			}				
			else
				$this->errorMsg = "Maximalstufe erreicht!";
			return false;				
		}

		function getLastError()
		{
			return $this->errorMsg;
		}
		

	}

?>