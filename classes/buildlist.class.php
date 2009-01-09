<?PHP

	class BuildList
	{
		private $entityId;
		private $buildings;
		private $ownerId;
		
		function BuildList($entityId,$ownerId=0)
		{
			$this->entityId = $entityId;
			$this->ownerId = $ownerId;
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
		
		function startConstruction($buildingId,$endTime);
		{
			dbquery("
			REPLACE INTO 
			buildlist 
			(
				buildlist_build_type,
				buildlist_build_start_time,
				buildlist_build_end_time,
				buildlist_building_id,
				buildlist_user_id,
				buildlist_entity_id
			) 
			VALUES 
			( 
				3,
				'".time()."',
				'".$endTime."',
				'".$buildingId."',
				'".$this->ownerId."',
				'".$this->entityId."'
			);");
			
			//Log schreiben
			$log_text = "
			<b>Gebäude Ausbau</b><br><br>
			<b>User:</b> [USER_ID=".$cu->id.";USER_NICK=".$cu->nick."]<br>
			<b>Planeten:</b> [PLANET_ID=".$cp->id().";PLANET_NAME=".$cp->name."]<br>
			<b>Gebäude:</b> ".$arr['building_name']."<br>
			<b>Gebäude Level:</b> ".$b_level." (vor Ausbau)<br>
			<b>Bau dauer:</b> ".tf($btime)."<br>
			<b>Ende:</b> ".date("Y-m-d H:i:s",$end_time)."<br>
			<b>Eingesetzte Bewohner:</b> ".nf($peopleWorking)."<br>
			<b>Gen-Tech Level:</b> ".GEN_TECH_LEVEL."<br><br>
			<b>Kosten</b><br>
			<b>".RES_METAL.":</b> ".nf($bc['metal'])."<br>
			<b>".RES_CRYSTAL.":</b> ".nf($bc['crystal'])."<br>
			<b>".RES_PLASTIC.":</b> ".nf($bc['plastic'])."<br>
			<b>".RES_FUEL.":</b> ".nf($bc['fuel'])."<br>
			<b>".RES_FOOD.":</b> ".nf($bc['food'])."<br><br>
			<b>Restliche Rohstoffe auf dem Planeten</b><br><br>
			<b>".RES_METAL.":</b> ".nf($cp->resMetal)."<br>
			<b>".RES_CRYSTAL.":</b> ".nf($cp->resCrystal)."<br>
			<b>".RES_PLASTIC.":</b> ".nf($cp->resPlastic)."<br>
			<b>".RES_FUEL.":</b> ".nf($cp->resFuel)."<br>
			<b>".RES_FOOD.":</b> ".nf($cp->resFood)."<br><br>
			";
			
			// Log Speichern
			add_log_game_building($log_text,$cu->id,$cu->allianceId,$cp->id(),$arr['building_id'],$b_status,time());			
			
			return true;
		}
	}

?>