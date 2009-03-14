<?PHP

	class BuildList implements IteratorAggregate
	{
		private $entityId;
		private $ownerId;

		private $entity;
		private $owner;

		private $items = null;
		private $itemStatus = null;	
		private $count = null;
		
		private $tmpItems = array();
		
		private $jobs = null;
		
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
				buildlist_user_id=".$this->ownerId ."
				AND buildlist_entity_id=".$this->entityId."
			;");
			$arr = mysql_fetch_row($res);
			$this->count = $arr[0];
			return $this->count;
		}							
			
		function & item($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
				return $this->items[$bid];
			if (isset($this->tmpItems[$bid]))
				return $this->tmpItems[$bid];
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
		
		function getBuildTime($itemId,$targetLevel)
		{
			global $cu,$cp;
			
			$targetLevel = max(1,$targetLevel);
			
			if ($this->owner == null)
			{			
				if ($cu->id != $this->ownerId)
					$this->owner = new User($this->ownerId);
				else
					$this->owner = &$cu;
			}
			if ($this->entity == null)
			{
				if ($cp->id != $this->entityId)
					$this->entity = Entity::createFactoryById($this->entityId);
				else
					$this->entity = &$cp;
			}

			// Calc bonus			
			$bonus = $this->owner->race->buildTime + $this->entity->typeBuildtime + $this->entity->starBuildtime - 2;
			
			if (isset($this->items[$itemId]))
			{
				$itm = &$this->items[$itemId];
			}
			else
			{
				$itm = new Building($itemId);
			}

			$cst = $itm->getCosts($targetLevel);
			$btime = ($cst[1]+$cst[2]+$cst[3]+$cst[4]+$cst[5]) / GLOBAL_TIME * BUILD_BUILD_TIME;
			$btime *= $bonus;		
			
			unset($itm);
			unset($cst);
			return $btime;
		}
		
		/**
		* Check wether an item is buildable. Conditions are
		* enough resources, not maxed out level, enough fields,
		* and satisfied prerequisites.
		*/
		function checkBuildable($itemId)
		{
			global $cu,$cp;
			if ($this->entity == null)
			{
				if ($cp->id != $this->entityId)
					$this->entity = Entity::createFactoryById($this->entityId);
				else
					$this->entity = &$cp;
			}
			
			if (isset($this->items[$itemId]))
			{
				$itm = &$this->items[$itemId];
				$cst = $itm->getCosts($this->itemStatus[$itemId]['level']+1);
				$lvl = $this->itemStatus[$itemId]['level'];
			}
			else
			{
				$itm = new Building($itemId);
				if (!$itm->isValid())
					return false;
				$cst = $itm->getCosts(1);
				$lvl = 0;
			}			

			
			// Check level
			if ($lvl < $itm->maxLevel)
			{
				// Check costs
				if ($cst[1] <= $this->entity->getRes(1) 
				&& $cst[2] <= $this->entity->getRes(2) 
				&& $cst[3] <= $this->entity->getRes(3) 
				&& $cst[4] <= $this->entity->getRes(4) 
				&& $cst[5] <= $this->entity->getRes(5))
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

		/**
		* Returns a message of the last error produced by this instance
		*/
		function getLastError()
		{
			return $this->errorMsg;
		}
		
		/**
		* Adds a construction job for the given item
		*
		* @param int Item-ID
		*/
		function addJob($itemId)
		{
			global $cp,$cu;
			if ($this->owner == null)
			{			
				if ($cu->id != $this->ownerId)
					$this->owner = new User($this->ownerId);
				else
					$this->owner = &$cu;
			}
			if ($this->entity == null)
			{
				if ($cp->id != $this->entityId)
					$this->entity = Entity::createFactoryById($this->entityId);
				else
					$this->entity = &$cp;
			}			
			
			if ($this->checkBuildable($itemId))
			{				
				if (isset($this->items[$itemId]))
				{
					$itm = &$this->items[$itemId];
					$lvl = $this->itemStatus[$itemId]['level']+1;
				}
				else
				{
					$itm = new Building($itemId);
					if (!$itm->isValid())
						return false;
					$lvl = 1;
				}
				if ($this->jobs==null)
					$this->loadJobs();
				$jobs = $this->getJobs($itemId);
				$jobcount = count($jobs);
				$lvl += $jobcount;

				$cst = $itm->getCosts($lvl);

				$this->entity->changeRes(-$cst[1],-$cst[2],-$cst[3],-$cst[4],-$cst[5]);
				
				$t = time();
				$startTime = $t;
				$jobs = $this->getJobs();
				$jobcount = count($jobs);				
				if ($jobcount>0)
				{
					foreach ($jobs as $jv)
					{
						$startTime = max($t,$jv['timeend']);
					}
					$startTime += BUILDING_QUEUE_DELAY;
				}					
				$endTime = $startTime + $this->getBuildTime($itemId,$lvl);
				
				dbquery("
				INSERT INTO
					building_queue
				(
					user_id,
					entity_id,
					item_id,
					time_start,
					time_end,
					res_metal,
					res_crystal,
					res_plastic,
					res_fuel,
					res_food,
					targetlevel
				)
				VALUES
				(
					".$this->ownerId.",
					".$this->entityId.",
					".$itemId.",
					".$startTime.",
					".$endTime.",
					".$cst[1].",
					".$cst[2].",
					".$cst[3].",
					".$cst[4].",
					".$cst[5].",
					".$lvl."				
				);");
				$this->jobs[mysql_insert_id()] = array(
				"item"=>$itemId,
				"timestart"=>$startTime,
				"timeend"=>$endTime,
				"targetlevel"=>$lvl);
								
				return true;
			}			
			return false;
		}

		/**
		* Loads all job items from the database
		* and stores them in the $jobs array
		*/
		private function loadJobs()
		{
			$this->jobs = array();
			$res = dbquery("
			SELECT
				id,
				item_id,
				time_start,
				time_end
			FROM
				building_queue
			WHERE
				entity_id=".$this->entityId."
				AND user_id=".$this->ownerId."	
			ORDER BY
				time_start	
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr = mysql_fetch_assoc($res))
				{
					$lvlp = 1 + $this->getLevel($arr['item_id']);
					// Count what the target level of this job is
					foreach ($this->jobs as &$v)
					{
						if ($v['item']==$arr['item_id'])
							$lvlp++;
					}					
					$this->jobs[$arr['id']] = array(
						"item"=> $arr['item_id'],
						"timestart"=> $arr['time_start'],
						"timeend"=> $arr['time_end'],
						"targetlevel"=> $lvlp
					);
				}
			}
		}
		
		/**
		* Returns all jobs (of a given item)
		*
		* @param int Item-ID
		*/
		function & getJobs($itemId=0)
		{
			if ($this->jobs==null)
				$this->loadJobs();
			if ($itemId>0)
			{
				$rtn = array();
				foreach ($this->jobs as $k=>$v)
				{
					if ($v['item']==$itemId)
						$rtn[$k] = $v;
				}
				return $rtn;
			}
			return $this->jobs;
		}
		
		/**
		* Returns the currently active job item
		* 
		* @param int Item-ID (Optional)
		*/
		function activeJob($itemId = 0)
		{
			$jb = $this->getJobs($itemId);
			if (count($jb)>0 )
			{
				foreach ($jb as $jv)
				{
					if ($jv['timestart'] < time() && $jv['timeend'] > time())
					{			
						return $jv;
					}
				}
			}
			return false;
		}

		/**
		* Cancel all pending jobs of a given 
		* item.
		*
		* @param int Item-ID
		*/
		function cancelJobsByItemId($itemId)
		{
			$itemJobs = $this->getJobs($itemId);
			if (count($itemJobs)>0)
			{
				$t = time();
				$kc = 0;
				foreach ($itemJobs as $jid => &$jb)
				{
					// Make sure we kill only jobs who haven't finished
					if ($jb['timeend'] > $t)
					{
						$this->cancelJobById($jid);
						$kc++;
					}
				}
				if ($kc>0)
					return $kc;
			}			
			return false;
		}
		
		/**
		* Cancels a given job and returns 
		* a part of resources if time is not up
		*
		* @param int Job-ID
		*/
		function cancelJobById($jid)
		{
			global $cp;
			if (isset($this->jobs[$jid]))
			{
				$res = dbquery("
				SELECT
					res_metal,
					res_crystal,
					res_plastic,
					res_fuel,
					res_food
				FROM
					building_queue
				WHERE
					id=".$jid.";					
				");		
				$arr = mysql_fetch_row($res);
				$t = time();
				if ($this->jobs[$jid]['timeend'] <= $t)
					$cashBack = 0;
				elseif ($this->jobs[$jid]['timestart'] >= $t)
					$cashBack = 1;
				else
					$cashBack = ($t - $this->jobs[$jid]['timestart']) / ($this->jobs[$jid]['timeend']-$this->jobs[$jid]['timestart']);
				
				if ($this->entity == null)
				{
					if ($cp->id != $this->entityId)
						$this->entity = Entity::createFactoryById($this->entityId);
					else
						$this->entity = &$cp;
				}				
				if ($cashBack > 0)
					$this->entity->changeRes($arr[0]*$cashBack,$arr[1]*$cashBack,$arr[2]*$cashBack,$arr[3]*$cashBack,$arr[4]*$cashBack);
						
				dbquery("
				DELETE FROM	
					building_queue
				WHERE
					id=".$jid.";					
				");
				unset($this->jobs[$jid]);
				return true;			
			}
			return false;
		}
	}

?>