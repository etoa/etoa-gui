<?PHP

	class FleetManager
	{
		private $userId;
		private $allianceId;
		private $userSpyTechLevel;
		private $count;
		private $aggressivCount;
		private $fleet;
		
		function FleetManager($userId,$allianceId=0)
		{
			$this->userId = $userId;
			$this->allianceId = $allianceId;
			$this->count = 0;
			$this->fleet = array();
		}
	
		function countControlledByEntity($entId)
		{
			$res = dbquery("
			SELECT 
				COUNT(id) 
			FROM 
				fleet 
			WHERE 
				user_id='".$this->userId."' 
			AND 
				(
					entity_from='".$entId."' 
					AND status=0
				) 
				OR 
				(
					entity_to='".$entId."' 
					AND status>0  
				);");
			$arr = mysql_fetch_row($res);
			return $arr[0];		
		}
	
		function loadOwn()
		{
			$this->count = 0;
			$this->fleet = array();
			
			//Lädt Flottendaten
			$fres = dbquery("
			SELECT
				id
			FROM
				fleet
			WHERE
				user_id='".$this->userId."'
			ORDER BY
				landtime DESC;");
			if (mysql_num_rows($fres)>0)
			{	
				while ($farr = mysql_fetch_row($fres))
				{
					$this->fleet[$farr[0]] = new Fleet($farr[0]);
					$this->count++;
				}		
			}
		}
		
		function loadForeign()
		{
			$this->count = 0;
			$this->aggressivCount = 0;
			$this->fleet = array();
			
			//User Spytech
			$tl = new TechList($this->userId);
			$this->userSpyTechLevel = $tl->getLevel(SPY_TECH_ID);
			
			$specialist = new Specialist(0,0,$this->userId);
			$this->userSpyTechLevel += $specialist->spyLevel;
			
			if (SPY_TECH_SHOW_ATTITUDE<=$this->userSpyTechLevel) {
				//Lädt Flottendaten
				// TODO: This is not good query because it needs to know the planet table structure
				$fres = dbquery("
					SELECT
						f.id,
						f.leader_id
					FROM
						fleet f
					INNER JOIN
						planets p
					ON p.id=f.entity_to
						AND p.planet_user_id=".$this->userId."
						AND f.user_id!='".$this->userId."'
						AND !(f.action='alliance' AND f.leader_id!=f.id)
					ORDER BY
						landtime DESC;");
				if (mysql_num_rows($fres)>0)
				{	
					while ($farr = mysql_fetch_row($fres))
					{
						$cFleet = new Fleet($farr[0],-1,$farr[1]);			
					
						if ($cFleet->getAction()->visible()) {
							if ($cFleet->getAction()->attitude()==3) {
								$otl = new TechList($cFleet->ownerId());
								$opTarnTech = $otl->getLevel(TARN_TECH_ID);
								$specialist = new Specialist(0,0,$cFleet->ownerId());
								$opTarnTech += $specialist->tarnLevel;
							
								$diffTimeFactor = max($opTarnTech-$this->userSpyTechLevel,0);
							
								$specialShipBonusTarn = 0;
								$specialBoniRes = dbquery("
									SELECT
										s.special_ship_bonus_tarn,
										fs.fs_special_ship_bonus_tarn
									FROM
										ships s
									INNER JOIN
										fleet_ships fs
									ON s.ship_id = fs.fs_ship_id
										AND fs.fs_fleet_id='".$farr[0]."'
										AND s.special_ship='1';");
								
								if(mysql_num_rows($specialBoniRes)>0)
    	    					{
            						while ($specialBoniArr = mysql_fetch_assoc($specialBoniRes))
									{
										$specialShipBonusTarn += $specialBoniArr['special_ship_bonus_tarn'] * $specialBoniArr['fs_special_ship_bonus_tarn'];
            						}
        						}
								$diffTimeFactor = 0.1 * min(9,$diffTimeFactor + 10 * $specialShipBonusTarn);
							
								if ($cFleet->remainingTime() <  ($cFleet->landTime() - $cFleet->launchTime())*(1 - $diffTimeFactor)) {
									$this->fleet[$farr[0]] = $cFleet;
									$this->count++;
									$this->aggressivCount++;
								}
							} else {						
								$this->fleet[$farr[0]] = $cFleet;
								$this->count++;
							}
						}
					}
				}		
			}
		}	
		
		function loadAllianceSupport()
		{
			$this->count = 0;
			$this->fleet = array();
			//Lädt Flottendaten
			// TODO: This is not good query because it needs to know the planet table structure
			$fres = dbquery("
			SELECT
				f.id
			FROM
				fleet f
			INNER JOIN
				users u
				ON u.user_alliance_id='".$this->allianceId."'
				AND u.user_id=f.user_id
				AND action='support'
			ORDER BY
				landtime DESC;");
			if (mysql_num_rows($fres)>0)
			{	
				while ($farr = mysql_fetch_row($fres))
				{
					$this->fleet[$farr[0]] = new Fleet($farr[0]);
					$this->count++;
				}		
			}
		}
		
		function loadAllianceAttacks()
		{
			$this->count = 0;
			$this->fleet = array();
			//Lädt Flottendaten
			// TODO: This is not good query because it needs to know the planet table structure
			$fres = dbquery("
			SELECT
				id
			FROM
				fleet
			WHERE
				next_id='".$this->allianceId."'
				AND leader_id=id
				AND action='alliance'
			ORDER BY
				landtime DESC;");
			if (mysql_num_rows($fres)>0)
			{	
				while ($farr = mysql_fetch_row($fres))
				{
					$this->fleet[$farr[0]] = new Fleet($farr[0]);
					$this->count++;
				}		
			}
		}
		
		


			
		
		function count()
		{
			return $this->count;
		}
	
		function getAll()
		{
			return $this->fleet;
		}
		
		function spyTech()
		{
			return $this->userSpyTechLevel;
		}
		
		function attitude()
		{
			if ($this->aggressivCount==$this->count) return "color:#f00";
			elseif ($this->aggressivCount==0) return "color:#0f0";
			else return "color:orange";
		}
		
		function loadAggressiv()
		{
			$this->loadForeign();
			return $this->aggressivCount;
		}
	
	}

?>