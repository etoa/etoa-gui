<?PHP

	class TechList
	{
		private $userId;
		private $techs;
		
		
		function TechList($userId)
		{
			$this->userId = $userId;
			$this->techs = array();
		}
	
		private function load($bid)
		{
			if (!isset($this->techs[$bid]))
			{
				$res = dbquery("
				SELECT				
					techlist_current_level
				FROM 
					techlist 
				WHERE 
					techlist_tech_id=".$bid." 
					AND techlist_user_id='".$this->userId."';");
				if (mysql_num_rows($res)>0)
				{		
					$arr = mysql_fetch_row($res);
					$this->techs[$bid] = array(
						'level' => $arr[0]					);
				}			
				else
				{
					$this->techs[$bid] = false;
				}			
			}		
		}
								
			
		function getLevel($bid)
		{
			$level = 0;
			$this->load($bid);
			if ($this->techs[$bid])
			{
				$level = $this->techs[$bid]['level'];
			}
			return $level;	
		}
		
		function isBuildingSomething($onCurrentPlanet=false)
		{
			global $cu;
			if($onCurrentPlanet)
			{
				global $cp;
			}
			$tres = dbquery("
			SELECT 
				COUNT(techlist_id)
			FROM 
				techlist 
			WHERE 
				techlist_user_id='".$cu->id."'".
				(($onCurrentPlanet)?" AND techlist_entity_id='".$cp->id."'":'').
				" AND techlist_build_type > '2'
			;");
			$tarr=mysql_fetch_row($tres);
			if($tarr[0] >0)
			{
				return true;
			}
			return false;
		}
		
    function isBuilding($bid)   
    {
    	global $cu;
      $tres = dbquery("
			SELECT 
				COUNT(techlist_id)
			FROM 
				techlist 
			WHERE 
				techlist_user_id='".$cu->id."' AND techlist_tech_id= ".$bid." AND techlist_build_type > '2'
			;");
			$tarr=mysql_fetch_row($tres);	
			if($tarr[0] >0)
			{
				return true;
			}
			return false;
    } 

		/* IMPORTANT:
		 * Check available workers
		 * before calling this function!
		 */
		function setPeopleWorking($people,$bid)
		{
			global $cp;
            dbquery("
            	UPDATE
                	buildlist
            	SET
                	buildlist_people_working='".$people."'
            	WHERE
                	buildlist_building_id='".$bid."'
                AND
                	buildlist_entity_id=".$cp->id.'');
            return true;
		}

		function setPeopleWorkingGen($people,$bid)
		{
			global $cp;
            dbquery("
            	UPDATE
                	buildlist
            	SET
                	buildlist_gen_people_working='".$people."'
            	WHERE
                	buildlist_building_id='".$bid."'
                AND
                	buildlist_entity_id=".$cp->id.'');
            return true;
		}

	}

?>