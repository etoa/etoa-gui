<?PHP

	class BuildListItem
	{
		// item data
		private $id = 0;
		private $ownerId = 0;
		private $entityId = 0;
		private $buildingId = 0;
		private $buildType = 0;
		private $level = 0;
		private $startTime = 0;
		private $endTime = 0;
		private $prodPercent = 1;
		private $peopleWorking = 0;
		private $peopleWorkingStatus = 0;
		private $deactivated = 0;
		private $cooldown = 0;
		
		// building data
		private $building = null;
		
		// changed data
		private $changedFields = array();
		
		// calculations
		private $buildableStatus = null;
		private $costs = array();
		private $demolishCosts = array();
		private $nextCosts = array();

		/**
		 * Constructor
		 * @param <type> $arr
		 * @param <type> $load
		 */
		function BuildListItem($id,$load=0)
		{
			if (is_array($id))
			{
				$arr = $id;
			}
			else
			{
				if ($id>0 && $load==1)
				{
					$this->load();
				}
				else
					return;
			}
			
			if (intval($arr['buildlist_id'])>0)
			{
				$this->id = $arr['buildlist_id'];
				$this->ownerId = $arr['buildlist_user_id'];
				$this->entityId = $arr['buildlist_entity_id'];
				$this->buildingId = $arr['buildlist_building_id'];
				$this->buildType = $arr['buildlist_build_type'];
				$this->level = $arr['buildlist_current_level'];
				$this->startTime = $arr['buildlist_build_start_time'];
				$this->endTime = $arr['buildlist_build_end_time'];
				$this->prodPercent = $arr['buildlist_prod_precent'];
				$this->peopleWorking = $arr['buildlist_people_working'];
				$this->peopleWorkingStatus = $arr['buildlist_people_working_status'];
				$this->deactivated = $arr['buildlist_deactivated'];
				$this->cooldown = $arr['buildlist_cooldown'];
			}
			else
			{
				global $cp, $cu;
				$this->ownerId = $cu->id;
				$this->entityId = $cp->id;
				$this->buildingId = $arr['building_id'];
			}

			if (isset($arr['building_id']))
			{
				$this->building = new Building($arr);
			}
			
			$this->entityId = $entityId;
			$this->ownerId = $ownerId;
			if ($load==1)
				$this->load();
		}
		
		public function __toString()
		{
			if ($this->id>0)
			{
				$title = $this->building.' <span id="buildlevel">';
				$title.= $this->level > 0 ? $this->level : '';
				$title.= '</span>';
				return $title;
			}
			return $this->id;
		}
		
		public function __set($key, $val)
		{
			try
			{
				
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
				
				if ($key=="cooldown")
				{
					$this->changedFields[$key] = "buildlist_cooldown";
					$this->$key = $val;
				}
				elseif ($key == 'buildableStatus')
				{
					$this->$key = $val;
				}
				else
				{
					throw new EException("Property $key hat keine UPDATE-Instruktion in der Klasse ".__CLASS__);
				}
			}
			catch (EException $e)
			{
				echo $e;
			}
		}
		
		public function __get($key)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in ".__CLASS__);
				if ($key == "building" && $this->building==null)
				{
					if (!$this->id>0)
					{
						throw new EException("Property $key existiert nicht in ".__CLASS__.". Daten nicht vorhanden");
					}
					else
					{
						$this->load();
					}
				}
				elseif ($key == "bunkerRes")
				{
					return $this->building->bunkerRes*intpow($this->building->storeFactor,$this->level-1);
				}
				elseif ($key == "bunkerFleetCount")
				{
					return $this->building->bunkerFleetCount*intpow($this->building->storeFactor,$this->level-1);
				}
				elseif ($key == "bunkerFleetSpace")
				{
					return $this->building->bunkerFleetSpace*intpow($this->building->storeFactor,$this->level-1);
				}
					
				return $this->$key;
			}
			catch (EException $e)
			{
				echo $e;
				return null;
			}
		}
	
	
		private function load()
		{
			$sql = dbquery("SELECT	
				l.*,
				i.*
			FROM 
				buildlist l
			INNER JOIN
				buildings i
			ON
				l.buildlist_building_id = i.building_id
				AND l.buildlist_id='".$id."'
			LIMIT 1;");
			
			if (mysql_num_rows($res)>0)
				$arr = mysql_fetch_assoc($res);
			else
			{
				throw new EException("Buildlisteintrag $id existiert nicht!");
			}			
		}
		
		public function resetCalculation()
		{
			$this->buildableStatus = null;
			$this->costs = array();
			$this->nextCosts = array();
			$this->demolishCosts = array();
		}
		
		public function setPeopleWorking($people)
		{
			if ($this->buildType==0)
			{
				$this->peopleWorking = $people;
				dbquery("UPDATE buildlist SET buildlist_people_working='".$people."' WHERE buildlist_id='".$this->id."' LIMIT 1;");
				return true;
			}
			return false;
		}
		
		public function isMaxLevel()
		{
			return $this->level>=$this->building->maxLevel ? true : false;
		}
		
		public function getBuildTime()
		{
			if (!(count($this->costs)))
			{
				$this->getBuildCosts();
			}
			return $this->costs['time'];
		}
		
		public function getBuildCosts($levelUp=0)
		{
			if (!(count($this->costs)  && !$levelUp) || !(count($this->nextCosts)  && $levelUp))
			{
				$cfg = Config::getInstance();
				global $resNames, $cp, $cu, $bl;
				$bc = array();
				foreach ($resNames as $rk => $rn)
				{
					$bc['costs'.$rk] = $cu->specialist->costsBuilding * $this->building->costs[$rk] * pow($this->building->costsFactor,$this->level+$levelUp);
				}
				$bc['costs5'] = $cu->specialist->costsBuilding * $this->building->costs[5] * pow($this->building->costsFactor,$this->level+$levelUp);

				$bonus = $cu->race->buildTime + $cp->typeBuildtime + $cp->starBuildtime + $cu->specialist->buildTime - 3;

				$bc['time'] = (array_sum($bc)) / GLOBAL_TIME * BUILD_BUILD_TIME;
				$bc['time'] *= $bonus;

				if ($bl->getPeopleWorking(BUILD_BUILDING_ID) > 0)
				{
					$bc['min_time'] = $bc['time'] * $this->minBuildTimeFactor();
					$bc['time'] -= ($bl->getPeopleWorking(BUILD_BUILDING_ID) * $cfg->value('people_work_done'));
					if ($bc['time'] < $bc['min_time']) 
						$bc['time'] = $bc['min_time'];
					$bc['costs4']+= $bl->getPeopleWorking(BUILD_BUILDING_ID) * $cfg->value('people_food_require');
				}
				
				if ($levelUp)
					$this->nextCosts = $bc;
				else
					$this->costs = $bc;
				unset($bc);
			}
			if ($levelUp)
				return $this->nextCosts;
			else
				return $this->costs;
		}
		
		public function getDemolishCosts($levelUp=0)
		{
			if (!count($this->demolishCosts))
			{
				$this->demolishCosts = $this->getBuildCosts($levelUp);
				
				foreach($this->demolishCosts as $id=>$element)
					$this->demolishCosts[$id] = $element * $this->building->demolishCostsFactor;
			}
			return $this->demolishCosts;
		}
		
		public function build()
		{
			global $cp;
			$costs = $this->getBuildCosts();
			$this->changedFields['startTime'] = "buildlist_build_start_time";
			$this->changedFields['endTime'] = "buildlist_build_end_time";
			$this->changedFields['buildType'] = "buildlist_build_type";
			
			$this->startTime = time();
			$this->endTime = $this->startTime + $costs['time'];
			$this->buildType = 3;
			
			if ($this->id>0)
			{
				dbquery("UPDATE buildlist SET buildlist_build_type='3', buildlist_build_start_time='".$this->startTime."', buildlist_build_end_time='".$this->endTime."' WHERE buildlist_id='".$this->id."' LIMIT 1;");
			}
			else
			{
				dbquery("INSERT INTO
							buildlist
						(
							buildlist_user_id,
							buildlist_entity_id,
							buildlist_building_id,
							buildlist_build_type,
							buildlist_current_level,
							buildlist_build_start_time,
							buildlist_build_end_time
						)
						VALUES
						(
							'".$this->ownerId."',
							'".$this->entityId."',
							'".$this->buildingId."',
							'".$this->buildType."',
							'".$this->level."',
							'".$this->startTime."',
							'".$this->endTime."'
						);");
			}
			Buildlist::$underConstruction = true; 
			$cp->changeRes(-$costs['costs0'],-$costs['costs1'],-$costs['costs2'],-$costs['costs3'],-$costs['costs4']);
			
			//Log schreiben
			$log_text = "[b]Gebäudebau[/b]

			[b]Baudauer:[/b] ".tf($btime)."
			[b]Ende:[/b] ".date("d.m.Y H:i:s",$end_time)."
			[b]Eingesetzte Bewohner:[/b] ".nf($peopleWorking)."
			[b]Gen-Tech Level:[/b] ".GEN_TECH_LEVEL."
			[b]Eingesetzter Spezialist:[/b] ".$cu->specialist->name."

			[b]Kosten[/b]
			[b]".RES_METAL.":[/b] ".nf($bc['metal'])."
			[b]".RES_CRYSTAL.":[/b] ".nf($bc['crystal'])."
			[b]".RES_PLASTIC.":[/b] ".nf($bc['plastic'])."
			[b]".RES_FUEL.":[/b] ".nf($bc['fuel'])."
			[b]".RES_FOOD.":[/b] ".nf($bc['food'])."

			[b]Restliche Rohstoffe auf dem Planeten[/b]
			[b]".RES_METAL.":[/b] ".nf($cp->resMetal)."
			[b]".RES_CRYSTAL.":[/b] ".nf($cp->resCrystal)."
			[b]".RES_PLASTIC.":[/b] ".nf($cp->resPlastic)."
			[b]".RES_FUEL.":[/b] ".nf($cp->resFuel)."
			[b]".RES_FOOD.":[/b] ".nf($cp->resFood)."";

			//Log Speichern
			GameLog::add(GameLog::F_BUILD, GameLog::INFO, $log_text, $cu->id, $cu->allianceId, $cp->id, $arr['building_id'], 3, $b_level);
			
			return true;
		}
		
		public function getPeopleOptimized()
		{
			$cfg = Config::getInstance();
			global $resNames, $cp, $cu;
			$bc = array();
			foreach ($resNames as $rk => $rn)
			{
				$bc['costs'.$rk] = $cu->specialist->costsBuilding * $this->building->costs[$rk] * pow($this->building->costsFactor,$this->level+$levelUp);
			}
			$bc['costs5'] = $cu->specialist->costsBuilding * $this->building->costs[5] * pow($this->building->costsFactor,$this->level+$levelUp);

			$bonus = $cu->race->buildTime + $cp->typeBuildtime + $cp->starBuildtime + $cu->specialist->buildTime - 3;

			$bc['time'] = (array_sum($bc)) / GLOBAL_TIME * BUILD_BUILD_TIME;
			$bc['time'] *= $bonus;
			$maxReduction = $bc['time'] - $bc['time'] * $this->minBuildTimeFactor();
			
			return ceil($maxReduction / $cfg->value('people_work_done'));
			
		}
		
		public function minBuildTimeFactor()
		{
			return (0.1-(Buildlist::$GENTECH/100));
		}
		
		public function demolish()
		{
			global $cp;
			$costs = $this->getDemolishCosts();
			$this->changedFields['startTime'] = "buildlist_build_start_time";
			$this->changedFields['endTime'] = "buildlist_build_end_time";
			$this->changedFields['buildType'] = "buildlist_build_type";
			
			$this->startTime = time();
			$this->endTime = $this->startTime + $costs['time'];
			$this->buildType = 4;
			
			dbquery("UPDATE buildlist SET buildlist_build_type='4', buildlist_build_start_time='".$this->startTime."', buildlist_build_end_time='".$this->endTime."' WHERE buildlist_id='".$this->id."' LIMIT 1;");
			Buildlist::$underConstruction = true;
			$cp->changeRes(-$costs['costs1'],-$costs['costs2'],-$costs['costs3'],-$costs['costs4'],-$costs['costs5']);
			
			//Log schreiben
			$log_text = "[b]Gebäudeabriss[/b]

			[b]Abrissdauer:[/b] ".tf($dtime)."
			[b]Ende:[/b] ".date("d.m.Y H:i:s",$end_time)."

			[b]Kosten[/b]
			[b]".RES_METAL.":[/b] ".nf($dc['metal'])."
			[b]".RES_CRYSTAL.":[/b] ".nf($dc['crystal'])."
			[b]".RES_PLASTIC.":[/b] ".nf($dc['plastic'])."
			[b]".RES_FUEL.":[/b] ".nf($dc['fuel'])."
			[b]".RES_FOOD.":[/b] ".nf($dc['food'])."

			[b]Restliche Rohstoffe auf dem Planeten[/b]
			[b]".RES_METAL.":[/b] ".nf($cp->resMetal)."
			[b]".RES_CRYSTAL.":[/b] ".nf($cp->resCrystal)."
			[b]".RES_PLASTIC.":[/b] ".nf($cp->resPlastic)."
			[b]".RES_FUEL.":[/b] ".nf($cp->resFuel)."
			[b]".RES_FOOD.":[/b] ".nf($cp->resFood)."";
			
			//Log Speichern
			GameLog::add(GameLog::F_BUILD, GameLog::INFO, $log_text, $cu->id, $cu->allianceId, $cp->id, $arr['building_id'], 4, $b_level);
		}
		
		public function cancelBuild()
		{
			if ($this->endTime > time())
			{
				global $cp;
				$costs = $this->getBuildCosts();
				$fac = ($this->endTime-time()) / ($this->endTime - $this->startTime);
				$this->endTime = 0;
				$this->startTime = 0;
				$this->buildType = 0;
				
				dbquery("UPDATE buildlist SET buildlist_build_type='0', buildlist_build_start_time='0', buildlist_build_end_time='0' WHERE buildlist_id='".$this->id."' LIMIT 1;");
				Buildlist::$underConstruction = false;
				//Rohstoffe vom Planeten abziehen und aktualisieren
				$cp->changeRes($costs['costs1']*$fac,$costs['costs2']*$fac,$costs['costs3']*$fac,$costs['costs4']*$fac,$costs['costs5']*$fac);
				
				//Log schreiben
				$log_text = "[b]Gebäudebau Abbruch[/b]

[b]Start des Gebädes:[/b] ".date("d.m.Y H:i:s",$start_time)."
[b]Ende des Gebädes:[/b] ".date("d.m.Y H:i:s",$end_time)."

[b]Erhaltene Rohstoffe[/b]
[b]Faktor:[/b] ".$fac."
[b]".RES_METAL.":[/b] ".nf($bc['metal']*$fac)."
[b]".RES_CRYSTAL.":[/b] ".nf($bc['crystal']*$fac)."
[b]".RES_PLASTIC.":[/b] ".nf($bc['plastic']*$fac)."
[b]".RES_FUEL.":[/b] ".nf($bc['fuel']*$fac)."
[b]".RES_FOOD.":[/b] ".nf($bc['food']*$fac)."

[b]Rohstoffe auf dem Planeten[/b]
[b]".RES_METAL.":[/b] ".nf($cp->resMetal)."
[b]".RES_CRYSTAL.":[/b] ".nf($cp->resCrystal)."
[b]".RES_PLASTIC.":[/b] ".nf($cp->resPlastic)."
[b]".RES_FUEL.":[/b] ".nf($cp->resFuel)."
[b]".RES_FOOD.":[/b] ".nf($cp->resFood)."";
				
				//Log Speichern
				GameLog::add(GameLog::F_BUILD, GameLog::INFO, $log_text, $cu->id, $cu->allianceId, $cp->id, $arr['building_id'], 1, $b_level);
				
				return;
			}
			else
				return "Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!";
		}
		
		public function cancelDemolish()
		{
			if ($this->endTime > time())
			{
				global $cp;
				$costs = $this->getDemolishCosts();
				$fac = ($this->endTime-time()) / ($this->endTime - $this->startTime);
				$this->endTime = 0;
				$this->startTime = 0;
				$this->buildType = 0;
				
				dbquery("UPDATE buildlist SET buildlist_build_type='0', buildlist_build_start_time='0', buildlist_build_end_time='0' WHERE buildlist_id='".$this->id."' LIMIT 1;");
				Buildlist::$underConstruction = false;
				//Rohstoffe vom Planeten abziehen und aktualisieren
				$cp->changeRes($costs['costs1']*$fac,$costs['costs2']*$fac,$costs['costs3']*$fac,$costs['costs4']*$fac,$costs['costs5']*$fac);
				
				//Log schreiben
				$log_text = "[b]Gebäudeabriss Abbruch[/b]

				[b]Start des Gebädes:[/b] ".date("d.m.Y H:i:s",$start_time)."
				[b]Ende des Gebädes:[/b] ".date("d.m.Y H:i:s",$end_time)."

				[b]Erhaltene Rohstoffe[/b]
				[b]Faktor:[/b] ".$fac."
				[b]".RES_METAL.":[/b] ".nf($dc['metal']*$fac)."
				[b]".RES_CRYSTAL.":[/b] ".nf($dc['crystal']*$fac)."
				[b]".RES_PLASTIC.":[/b] ".nf($dc['plastic']*$fac)."
				[b]".RES_FUEL.":[/b] ".nf($dc['fuel']*$fac)."
				[b]".RES_FOOD.":[/b] ".nf($dc['food']*$fac)."

				[b]Rohstoffe auf dem Planeten[/b]
				[b]".RES_METAL.":[/b] ".nf($cp->resMetal)."
				[b]".RES_CRYSTAL.":[/b] ".nf($cp->resCrystal)."
				[b]".RES_PLASTIC.":[/b] ".nf($cp->resPlastic)."
				[b]".RES_FUEL.":[/b] ".nf($cp->resFuel)."
				[b]".RES_FOOD.":[/b] ".nf($cp->resFood)."";

				//Log Speichern
				GameLog::add(GameLog::F_BUILD, GameLog::INFO, $log_text, $cu->id, $cu->allianceId, $cp->id, $arr['building_id'], 2, $b_level);
				
				return;
			}
			else
				return "Abbruchauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!";
		}
		
		public function waitingTime($type='build')
		{
			global $cp, $resNames;
			$notAvStyle=" style=\"color:red;\"";
			if ($type == 'build')
				$costs = $this->getBuildCosts(0,0);
			else
				$costs = $this->getDemolishCosts(0,0);
			
			$wTime = array();
			// Wartezeiten auf Ressourcen berechnen
			foreach ($resNames as $rk => $rn)
			{
				if ($cp->getProd($rk))
				{
					$wTime[$rk] = ceil(($costs['costs'.$rk] - $cp->getRes1($rk)) / $cp->getProd($rk) * 3600);
				}
				else
					$wTime[$rk] = 0;
			}
			$wTime['max'] = max($wTime);
			
			$wTime['string'] = "";
			foreach ($resNames as $rk => $rn)
			{
				$wTime['string'] .= '<td ';
				if ($costs['costs'.$rk] > $cp->getRes1($rk))
				{
					$wTime['string'] .= $notAvStyle.' '.tm('Fehlender Rohstoff','<strong>'.nf($costs['costs'.$rk]-$cp->getRes1($rk)).'</strong> '.$rn.'<br />Bereit in <strong>'.tf($wTime[$rk]).'</strong>');
				}
				$wTime['string'] .= '>'.nf($costs['costs'.$rk]).'</td>';
			}
			return $wTime;
		}
							
	}

?>