<?PHP

	class BuildList
	{
		private $entityId;
		private $buildings;
		
		
		function BuildList($entityId)
		{
			$this->entityId = $entityId;
			$this->buildings = array();
		}
	
		private function load($bid)
		{
			if (!isset($this->buildings[$bid]))
			{
				$res = dbquery("
				SELECT				
					buildlist_current_level,
					buildlist_deactivated,
					buildlist_cooldown,
					buildlist_people_working,
					buildlist_prod_percent 
				FROM 
					buildlist 
				WHERE 
					buildlist_building_id=".$bid." 
					AND buildlist_entity_id='".$this->entityId."';");
				if (mysql_num_rows($res)>0)
				{		
					$arr = mysql_fetch_row($res);
					$this->buildings[$bid] = array(
						'level' => $arr[0],
						'deactivated' => $arr[1],
						'cooldown' => $arr[2],
						'people_working' => $arr[3],
						'prod_percent' => $arr[4]						
					);
				}			
				else
				{
					$this->buildings[$bid] = false;
				}			
			}		
		}
								
			
		function getLevel($bid)
		{
			$level = 0;
			$this->load($bid);
			if ($this->buildings[$bid])
			{
				$level = $this->buildings[$bid]['level'];
			}
			return $level;	
		}	

		function getDeactivated($bid)
		{
			$this->load($bid);
			if ($this->buildings[$bid])
			{
				if ($this->buildings[$bid]['deactivated'] > time())
					return $this->buildings[$bid]['deactivated'];
			}
			return false;	
		}	
		
		function getCooldown($bid)
		{
			$this->load($bid);
			if ($this->buildings[$bid])
			{
				if ($this->buildings[$bid]['cooldown'] > time())
					return $this->buildings[$bid]['cooldown'];
			}
			return false;	
		}		
		
		function setCooldown($bid,$cd)			
		{
			$this->load($bid);
			if ($this->buildings[$bid])
			{
				$this->buildings[$bid]['cooldown'] = $cd;
				$res = dbquery("
				UPDATE
					buildlist 
				SET
					buildlist_cooldown=".$cd."
				WHERE 
					buildlist_building_id=".$bid." 
					AND buildlist_entity_id='".$this->entityId."';");				

			}
		}		
		
		function totalPeopleWorking()
		{
			$res = dbquery("
			SELECT 
				SUM(buildlist_people_working) 
			FROM 
				buildlist
			WHERE 
				buildlist_entity_id='".$this->entityId."';");
			$pbarr = mysql_fetch_row($res);
			return $pbarr[0];			
		}
		
		function getPeopleWorking($bid)
		{
			$res = dbquery("
			SELECT 
				buildlist_people_working
			FROM 
				buildlist
			WHERE 
				buildlist_entity_id='".$this->entityId."'
				AND buildlist_building_id=".$bid.";");
			$pbarr = mysql_fetch_row($res);
			return $pbarr[0];			
		}		
		
	}

?>