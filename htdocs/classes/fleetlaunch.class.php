<?PHP

	/**
	* Fleet launch class, provides the full workflow for starting a fleet
	* and thus creating a fleet object and record in the database
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/
	class FleetLaunch
	{
		//
		// Variable definitions
 		//
		public $sourceEntity;
		public $targetEntity;
		public $wormholeEntryEntity;
		public $wormholeExitEntity;
		var $ownerId;

		var $ships;
		var $shipCount;
		var $shipsFixed;

		var $speed;
		var $speed1;
		var $speedPercent;
		var $speedPercent1;
		var $duration;
		var $duration1;
		var $costsPerHundredAE;
		var $costsPerHundredAE1;
		var $timeLaunchLand;
		var $costsLaunchLand;
		var $pilots;
		var $capacityTotal;
		var $capacityResUses;
		var $capacityFuelUsed;
		var $capacityPeopleTotal;
		var $capacityPeopleLoaded;

		var $distance;
		var $distance1;

		private $action;
		private $resources;
		private $error;
		private $fleetLog;

		/**
		* The constructor
		*
		* >> Step 1 <<
		*/
        public function __construct(&$sourceEnt,&$ownerEnt)
		{

			$this->sourceEntity = $sourceEnt;
			$this->owner = $ownerEnt;
			$this->ownerId = $ownerEnt->id;
			$this->ownerRaceName = $ownerEnt->race->name;
			$this->raceSpeedFactor = $ownerEnt->race->fleetSpeedFactor;
			$this->specialist = $ownerEnt->specialist;
			$this->possibleFleetStarts = 0;
			$this->fleetSlotsUsed = 0;
			$this->fleetControlLevel =0;

			$this->ships = array();
			$this->speedPercent=100;
			$this->speed = 0;
			$this->speed1 = 0;
			$this->sBonusSpeed=1;
			$this->sBonusReadiness=1;
			$this->duration=0;
			$this->action='';
			$this->costsPerHundredAE=0;
			$this->costsPerHundredAE1=0;
			$this->timeLaunchLand=0;
			$this->costsLaunchLand=0;
			$this->pilots=0;
			$this->pilotsAvailable = 0;
			$this->sBonusPilots=1;
			$this->capacityTotal=0;
			$this->capacityResLoaded=0;
			$this->capacityFuelUsed=0;
			$this->capacityPeopleTotal=0;
			$this->capacityPeopleLoaded=0;
			$this->sBonusCapacity=1;
			$this->shipCount=0;
			$this->distance=0;
			$this->res = array(0,0,0,0,0,0);
			$this->fetch = array(0,0,0,0,0,0,0);
			$this->costs = 0;
			$this->costsFood = 0;
			$this->costsPower = 0;
			$this->supportTime = 0;
			$this->supportCostsFood = 0;
			$this->supportCostsFuel = 0;
			$this->supportCostsFuelPerSec = 0;
			$this->supportCostsFoodPerSec = 0;
			$this->leaderId = 0;
			$this->fakeId = 0;
			$this->sFleets = NULL;
			$this->aFleets = NULL;

			$this->shipActions = array();

			$this->havenOk=false;
			$this->shipsFixed=false;
			$this->targetOk=false;
			$this->actionOk=false;

			$this->error="";

			$this->fleetLog = new FleetLog($this->ownerId, $this->sourceEntity->id, $sourceEnt);

			//Create targetentity
			if (isset($_SESSION['haven']['targetId'])) {
				$this->targetEntity = Entity::createFactoryById($_SESSION['haven']['targetId']);
			}
			elseif (isset($_SESSION['haven']['cellTargetId'])) {
				$this->targetEntity = Entity::createFactoryUnkownCell($_SESSION['haven']['cellTargetId']);
			}

			//Wormhole enable?
			$this->wormholeEnable=false;
			$res = dbquery("SELECT techlist_current_level FROM techlist WHERE techlist_tech_id=".TECH_WORMHOLE." AND techlist_user_id='".$this->ownerId."';");
			if (mysql_num_rows($res))
			{
				$arr = mysql_fetch_row($res);
				$this->wormholeEnable=$arr[0];
			}

		}


		//
		// Main workflow
		//

		/**
		* Checks main conditions on source planet and
		* returns true if they are ok.
		* The conditions are: Disabled flightban, enabled fleetcontrol, a free fleet slot
		*
		* >> Step 2 <<
		*/
		function checkHaven()
		{
			$cfg = Config::getInstance();

			// Check if flights are possible
			if ($cfg->value('flightban')==0
			|| $cfg->p1('flightban_time')>time()
			|| $cfg->p2('flightban_time')<time() )
			{

				if($cfg->value('emp_action') == 1) {
                    $action = 3;
				}
				else {
					$action = 2;
				}

				$bl = new BuildList($this->sourceEntity->id(),$this->ownerId,$action);

				// Check if haven is out of order
				if ($dt = $bl->getDeactivated(FLEET_CONTROL_ID))
		 		{
					$this->error = "Dieser Raumschiffhafen ist bis ".df($dt)." deaktiviert.";
				}
				else
				{
					$fm = new FleetManager($this->ownerId);
					$this->fleetSlotsUsed = $fm->countControlledByEntity($this->sourceEntity->id());
					unset($fm);

					$this->fleetControlLevel = $bl->getLevel(FLEET_CONTROL_ID);
					$totalSlots = FLEET_NOCONTROL_NUM + $this->fleetControlLevel + $this->specialist->fleetMax;
					$this->possibleFleetStarts = $totalSlots - $this->fleetSlotsUsed;

					if ($this->possibleFleetStarts > 0)
					{
						// Piloten
						$this->pilotsAvailable = floor($this->sourceEntity->people() - $bl->totalPeopleWorking());

						$this->havenOk = true;
					}
					else
					{
						$this->error = "Von hier können keine weiteren Flotten starten, alle Slots (".$totalSlots.") sind belegt!";
					}
				}
			}
			else
			{
				$this->error = "Wegen einer Flottensperre können bis ".df($cfg->p2('flightban_time'))." keine Flotten gestartet werden! ".$cfg->p1('flightban');
			}
			return $this->havenOk;
		}

		/**
		* Adds $cnt items of ship $sid to the fleet.
		* Returns the effective number of added ships or false if no ship
		* of that type was on the source entity
		*
		* >> Step 3 <<
		*/
		function addShip($sid,$cnt)
		{
			if ($this->havenOk)
			{
				if (!$this->shipsFixed)
				{
					$res = dbquery("
					SELECT
						*
					FROM
		        ships
		      INNER JOIN
		        shiplist
					ON
		        ship_id=shiplist_ship_id
						AND shiplist_user_id='".$this->ownerId()."'
						AND shiplist_entity_id='".$this->sourceEntity->id()."'
		        AND ship_id=".$sid."
		        AND shiplist_count>0;");
					if (mysql_num_rows($res)>0)
					{
						$arr = mysql_fetch_array($res);
						$vres=dbquery("
	     					SELECT
								l.techlist_current_level,
								r.req_level
							FROM
								ship_requirements r
							INNER JOIN
								techlist l
							ON r.req_tech_id = l.techlist_tech_id
								AND l.techlist_user_id=".$this->ownerId()."
							INNER JOIN
								technologies t
							ON r.req_tech_id = t.tech_id
								AND t.tech_type_id = '1'
							WHERE
								r.obj_id=".$sid."
							GROUP BY
								r.id");

							$timefactor=$this->raceSpeedFactor() + $this->specialist->fleetSpeedFactor-1;
							if (mysql_num_rows($vres)>0)
							{
								while ($varr=mysql_fetch_array($vres))
								{
									if($varr['techlist_current_level']-$varr['req_level']<=0)
									{
										$timefactor+=0;
									}
									else
									{
										$timefactor+=max(0,($varr['techlist_current_level']-$varr['req_level'])*0.1);
									}
								}
							}
						$cnt = min(nf_back($cnt),$arr['shiplist_count']);

						$this->ships[$sid] = array(
						"count" => $cnt,
						"speed" => ($arr['ship_speed']/FLEET_FACTOR_F)*$timefactor,
						"fuel_use" => $arr['ship_fuel_use'] * $cnt,
						"fake" => strpos($arr['ship_actions'],"fakeattack"),
						"name" => $arr['ship_name'],
						"pilots" => $arr['ship_pilots'] * $cnt,
						"special" => $arr['special_ship'],
						"actions" => explode(",",$arr['ship_actions']),
						"sLevel" => $arr['shiplist_special_ship_level'],
						"sExp" => $arr['shiplist_special_ship_exp'],
						"sBonusWeapon" => $arr['shiplist_special_ship_bonus_weapon'],
						"sBonusStructure" => $arr['shiplist_special_ship_bonus_structure'],
						"sBonusShield" => $arr['shiplist_special_ship_bonus_shield'],
						"sBonusHeal" => $arr['shiplist_special_ship_bonus_heal'],
						"sBonusCapacity" => $arr['shiplist_special_ship_bonus_capacity'],
						"sBonusSpeed" => $arr['shiplist_special_ship_bonus_speed'],
						"sBonusReadiness" => $arr['shiplist_special_ship_bonus_readiness'],
						"sBonusPilots" => $arr['shiplist_special_ship_bonus_pilots'],
						"sBonusTarn" => $arr['shiplist_special_ship_bonus_tarn'],
						"sBonusAntrax" => $arr['shiplist_special_ship_bonus_antrax'],
						"sBonusForsteal" => $arr['shiplist_special_ship_bonus_forsteal'],
						"sBonusBuildDestroy" => $arr['shiplist_special_ship_bonus_build_destroy'],
						"sBonusAntraxFood" => $arr['shiplist_special_ship_bonus_antrax_food'],
						"sBonusDeactivade" => $arr['shiplist_special_ship_bonus_deactivade']
						);

						if ($arr['special_ship']) {
							$this->sBonusSpeed += $arr['shiplist_special_ship_bonus_speed']*$arr['special_ship_bonus_speed'];
							$this->sBonusReadiness += $arr['shiplist_special_ship_bonus_readiness']*$arr['special_ship_bonus_readiness'];
							$this->sBonusPilots = max(0,$this->sBonusPilots-$arr['shiplist_special_ship_bonus_pilots']*$arr['special_ship_bonus_pilots']);
							$this->sBonusCapacity += $arr['shiplist_special_ship_bonus_capacity']*$arr['special_ship_bonus_capacity'];
						}

						$this->shipActions = array_merge($this->shipActions,explode(",",$arr['ship_actions']));
						$this->shipActions = array_unique($this->shipActions);

						// Set global speed
						if ($this->speed <= 0)
						{
							$this->speed = ($arr['ship_speed']/FLEET_FACTOR_F)*$timefactor;
						}
						else
						{
							$this->speed = min($this->speed, ($arr['ship_speed']/FLEET_FACTOR_F)*$timefactor);
						}

						$this->timeLaunchLand = max($this->timeLaunchLand, $arr['ship_time2land']/FLEET_FACTOR_S + $arr['ship_time2start']/FLEET_FACTOR_L);
						$this->costsLaunchLand += 2 * ($arr['ship_fuel_use_launch'] + $arr['ship_fuel_use_landing']) * $cnt;
						$this->pilots += $arr['ship_pilots'] * $cnt;
						$this->capacityTotal += $arr['ship_capacity'] * $cnt;
						$this->capacityPeopleTotal += $arr['ship_people_capacity'] * $cnt;
						$this->shipCount += $cnt;

						return $cnt;
					}
					else
						$this->error = "Dieses Schiff ist hier nicht vorhanden!";
				}
				else
					$this->error = "Kann kein Schiff hinzufügen, die Flotte wurde bereits fertig zusammengestellt!";
			}
			else
				$this->error = "Kann kein Schiff hinzufügen, es liegt noch ein Problem mit der Flottenkontrolle vor.";
			return false;
		}

		/**
		* Fix ships, prevents the user from adding more ships
		* and calculates the final costs per ae
		*
		* >> Step 4 <<
		*/
		function fixShips()
		{
			if ($this->shipsFixed)
			{
				$this->costsPerHundredAE=0;
				$this->shipsFixed=false;
			}

			if ($this->shipCount > 0)
			{
				if ($this->pilotsAvailable() >= $this->getPilots())
				{
					// Calc Costs for all ships, based on regulated speed
					foreach ($this->ships as $sid => $sd)
					{
						$cpae = $sd['fuel_use'] * $this->speed / $sd['speed'];
						$this->ships[$sid]['costs_per_ae'] = $cpae;
						$this->costsPerHundredAE += $cpae;
					}
					$this->shipsFixed=true;
					$this->error == "";
					return $this->shipsFixed;
				}
				else
					$this->error = "Es sind zuwenig Piloten für diese Flotte vorhanden.(".$this->pilotsAvailable()." verfügbar, ".$this->getPilots()." benötigt)";
			}
			else
				$this->error = "Kann Schiffauswahl nicht fertigstellen, es wurde keine Schiffe zur Flotte hinzugefügt.";
			/*}
			else
				$this->error = "Kann Schiffauswahl nicht fertigstellen, die Flotte wurde bereits fertig zusammengestellt!";*/
			return false;
		}

		/**
		* Set the wormhole entity
		*
		* >> Step 5.1
		*/
		function setWormhole(&$ent,$speedPercent=100)
		{
			if ($this->wormholeEnable)
			{
				if (is_array($ent->getFleetTargetForwarder()))
				{
					$this->wormholeEntryEntity=$ent;
					$this->wormholeExitEntity=Entity::createFactoryById($this->wormholeEntryEntity->targetId());
					$this->costsPerHundredAE1=$this->costsPerHundredAE;
					$this->speed1=$this->speed;
					$this->duration1=$this->duration - $this->getTimeLaunchLand();
					$this->speedPercent1=$this->speedPercent;
					return true;
				}
				else
					$this->error = "Ungültiges Zielobjekt";
			}
			else
				$this->error = "Wurmlochforschung noch nicht erforscht";
			return false;
		}

		/**
		* Sets the target entity
		*
		* >> Step 5 <<
		*/
		function setTarget(&$ent,$speedPercent=100)
		{
			if ($this->shipsFixed)
			{
				if ($ent->isValid())
				{
					$this->targetEntity=$ent;
					if ($this->wormholeEntryEntity!=NULL)
					{
						$this->distance = $this->wormholeExitEntity->distance($this->targetEntity);
						$this->distance1 = $this->sourceEntity->distance($this->wormholeEntryEntity);
					}
					else
					{
						$this->distance = $this->sourceEntity->distance($this->targetEntity);
						$this->distance1 = 0;
					}

					$this->setSpeedPercent($speedPercent);

					return true;
				}
				else
					$this->error = "Ungültiges Zielobjekt";
			}
			else
				$this->error = "Flotte nicht fertig zusammengestellt";
			return false;
		}


		/**
		* Check if fleet can fly to this target
		*
		* >> Step 6 <<
		*/
		function checkTarget()
		{
			if ($this->sourceEntity->resFuel() >= $this->getCosts())
			{
				if ($this->sourceEntity->resFood() >= $this->getCostsFood())
				{
					if ($this->getCapacity()>=0)
					{
						$this->targetOk = true;
						return $this->targetOk;
					}
					else
						$this->error = "Zu wenig Laderaum für soviel Treibstoff und Nahrung (".nf(abs($this->getCapacity()))." zuviel)!";
				}
				else
					$this->error = "Zuwenig Nahrung! ".nf($this->sourceEntity->resFood())." t ".RES_FOOD." vorhanden, ".nf($this->getCostsFood())." t benötigt.";
			}
			else
				$this->error = "Zuwenig Treibstoff! ".nf($this->sourceEntity->resFuel())." t ".RES_FUEL." vorhanden, ".nf($this->getCosts())." t benötigt.";
			return false;
		}

		/**
		* Set the desired action
		*
		* >> Step 7 <<
		*/
		function setAction($actionCode)
		{
			if ($this->targetOk)
			{
				$actions = $this->getAllowedActions();
				if (isset($actions[$actionCode]))
				{
					$this->action = $actionCode;

					$this->actionOk = true;
					return true;
				}
			}
			$this->error = "Es befindet sich kein Schiff in der Flotte, welches die Aktion ausführen kann.";
			return false;
		}


		function launch()
		{
			if ($this->actionOk)
			{
				// recheck because of browser-tabbing
				$fm = new FleetManager($this->ownerId);
				$this->fleetSlotsUsed = $fm->countControlledByEntity($this->sourceEntity->id());
				unset($fm);

				$totalSlots = FLEET_NOCONTROL_NUM + $this->fleetControlLevel + $this->specialist->fleetMax;
				$this->possibleFleetStarts = $totalSlots - $this->fleetSlotsUsed;

				if ($this->possibleFleetStarts > 0)
				{
					$time = time();
					$this->landTime = ($time+$this->getDuration());

					// Subtract ships from source
					$sl = new ShipList($this->sourceEntity->id(),$this->ownerId);
					$addcnt = 0;
					foreach ($this->ships as $sid => $sda)
					{
						$this->ships[$sid]['count'] = $sl->remove($sid,$sda['count']);
						$addcnt+=$this->ships[$sid]['count'];
					}

					if ($addcnt > 0)
					{

						// Load resource (is needed because of the xajax use)
						// subtracts payload ressources from source
						$this->finalLoadResource();

						// Subtract flight and support costs from source
						$this->sourceEntity->chgRes(4,-$this->getCosts()-$this->getSupportFuel());
						$this->sourceEntity->chgRes(5,-$this->getCostsFood()-$this->getSupportFood());
						$this->sourceEntity->chgPeople(-($this->getPilots()+$this->capacityPeopleLoaded));

						if ($this->action=="alliance" && $this->leaderId!=0) {
							$status=3;
							$nextId = $this->sourceEntity->ownerAlliance();
						}
						elseif ($this->action=="support")
						{
							$status = 0;
							$nextId = $this->sourceEntity->id();
						}
						else {
							$status = 0;
							$nextId = 0;
						}

						// Create fleet record
						$sql = "
						INSERT INTO
							fleet
						(
							user_id,
							leader_id,
							entity_from,
							entity_to,
							next_id,
							launchtime,
							landtime,
							nextactiontime,
							action,
							status,
							pilots,
							usage_fuel,
							usage_food,
							usage_power,
							support_usage_food,
							support_usage_fuel,				
							res_metal,
							res_crystal,
							res_plastic,
							res_fuel,
							res_food,
							res_people,
							fetch_metal,
							fetch_crystal,
							fetch_plastic,
							fetch_fuel,
							fetch_food,
							fetch_people
						)
						VALUES
						(
							".$this->ownerId.",
							".$this->leaderId.",
							".$this->sourceEntity->id().",
							".$this->targetEntity->id().",
							".$nextId.",
							".$time.",
							".$this->landTime.",
							".$this->supportTime.",
							'".$this->action."',
							'".$status."',
							".$this->getPilots().",
							".$this->getCosts().",
							".$this->getCostsFood().",
							".$this->getCostsPower().",
							".$this->supportCostsFood.",
							".$this->supportCostsFuel.",
							".$this->res[1].",
							".$this->res[2].",
							".$this->res[3].",
							".$this->res[4].",
							".$this->res[5].",
							".$this->capacityPeopleLoaded.",
							".$this->fetch[1].",
							".$this->fetch[2].",
							".$this->fetch[3].",
							".$this->fetch[4].",
							".$this->fetch[5].",
							".$this->fetch[6]."
						)
						";
						dbquery($sql);
						$fid = mysql_insert_id();

						$shipLog = "";
						foreach ($this->ships as $sid => $sda)
						{
							$shipLog .= $sid.":".$sda['count'].",";
							if ($sda['special'])
							{
								dbquery("INSERT INTO
								fleet_ships
								(
									fs_fleet_id,
									fs_ship_id,
									fs_ship_cnt,
									fs_special_ship,
									fs_special_ship_level,
									fs_special_ship_exp,
									fs_special_ship_bonus_weapon,
									fs_special_ship_bonus_structure,
									fs_special_ship_bonus_shield,
									fs_special_ship_bonus_heal,
									fs_special_ship_bonus_capacity,
									fs_special_ship_bonus_speed,
									fs_special_ship_bonus_readiness,
									fs_special_ship_bonus_pilots,
									fs_special_ship_bonus_tarn,
									fs_special_ship_bonus_antrax,
									fs_special_ship_bonus_forsteal,
									fs_special_ship_bonus_build_destroy,
									fs_special_ship_bonus_antrax_food,
									fs_special_ship_bonus_deactivade
								)
								VALUES
								(
									".$fid.",
									".$sid.",
									".$sda['count'].",
									'1',
									".$sda['sLevel'].",
									".$sda['sExp'].",
									".$sda['sBonusWeapon'].",
									".$sda['sBonusStructure'].",
									".$sda['sBonusShield'].",
									".$sda['sBonusHeal'].",
									".$sda['sBonusCapacity'].",
									".$sda['sBonusSpeed'].",
									".$sda['sBonusReadiness'].",
									".$sda['sBonusPilots'].",
									".$sda['sBonusTarn'].",
									".$sda['sBonusAntrax'].",
									".$sda['sBonusForsteal'].",
									".$sda['sBonusBuildDestroy'].",
									".$sda['sBonusAntraxFood'].",
									".$sda['sBonusDeactivade']."
								);");
							}
							elseif ($sda['fake']!==false)
							{
								dbquery("INSERT INTO
									fleet_ships
									(
										fs_fleet_id,
										fs_ship_id,
										fs_ship_cnt,
										fs_ship_faked
									)
									VALUES
									(
										".$fid.",
										".$sid.",
										".$sda['count'].",
										".$this->fakeId."
									);");
							}
							else
							{
								dbquery("INSERT INTO
									fleet_ships
									(
										fs_fleet_id,
										fs_ship_id,
										fs_ship_cnt
									)
									VALUES
									(
										".$fid.",
										".$sid.",
										".$sda['count']."
									);");
							}
						}

						//add all the cool stuff to the fleetLog
						$this->fleetLog->fleetId = $fid;
						$this->fleetLog->targetId = $this->targetEntity->id();
						$this->fleetLog->launchtime = $time;
						$this->fleetLog->landtime = $this->landTime;
						$this->fleetLog->fuel = $this->getCosts() + $this->supportCostsFuel;
						$this->fleetLog->food = $this->getCostsFood() + $this->supportCostsFood;
						$this->fleetLog->pilots = $this->getPilots();
						$this->fleetLog->action = $this->action;
						$this->fleetLog->addFleetRes($this->res,$this->capacityPeopleLoaded,$this->fetch);
						$this->fleetLog->fleetShipEnd = $shipLog;
						$this->fleetLog->launch();


						if ($this->action=="alliance" && $this->leaderId==0) {
							dbquery("
									UPDATE
										fleet
									SET
										leader_id='".$fid."',
										next_id='".$this->sourceEntity->ownerAlliance()."'
									WHERE
										id='".$fid."';");
						}
						return $fid;
					}
					else
						$this->error = "Konnte keine Schiffe zur Flotte hinzufügen da keine vorhanden sind!";
				}
				else
					$this->error = "Von hier können keine weiteren Flotten starten, alle Slots (".$totalSlots.") sind belegt!";
			}
			else
			{
				$this->error = "Aktion nocht nicht festgelegt!";
			}
			return false;
		}



		//
		// Helpers
		//

		/**
		* Unfixes ships and resets the ships array
		* This can be used in the haven when revising
		* the ship selection
		*/
		function resetShips()
		{
			$this->ships = array();
			$this->shipActions = array();
			$this->res = array(0,0,0,0,0,0);
			$this->fetch = array(0,0,0,0,0,0,0);
			$this->shipsFixed=false;
			$this->speed = 0;
			$this->duration=0;
			$this->costsPerHundredAE=0;
			$this->timeLaunchLand=0;
			$this->costsLaunchLand=0;
			$this->pilots=0;
			$this->capacityTotal=0;
			$this->capacityResLoaded=0;
			$this->capacityFuelUsed=0;
			$this->capacityPeopleTotal=0;
			$this->capacityPeopleLoaded=0;
			$this->shipCount=0;
			$this->distance=0;
			$this->shipsFixed=false;
			$this->sBonusCapacity=1;
			$this->sBonusPilots=1;
			$this->sBonusSpeed=1;
			$this->sBonusReadiness=1;
		}

		function unsetWormhole()
		{
			$this->wormholeEntryEntity=NULL;
			$this->wormholeExitEntity=NULL;
			$this->costsPerHundredAE1=0;
			$this->speed1=0;
			$this->duration1=0;
			$this->speedPercent1=0;
		}



		/**
		*
		*/
		function getAllowedActions()
		{
			$cfg = Config::getInstance();
			$this->error = '';

			//$allowed =  ($this->sFleets && count($this->sFleets) && ( $this->leaderId>0 || in_array($this->targetEntity->id,$this->sFleets))) ? true : false;
			$allowed = true;
			// Get possible actions by intersecting ship actions and allowed target actions
			$actions = array_intersect($this->shipActions,$this->targetEntity->allowedFleetActions());
			$actionObjs = array();

			$battleban = false;
			if ($cfg->value("battleban")!=0 && $cfg->p1("battleban_time")<=time() && $cfg->p2("battleban_time") > time())
			{
				$this->error = "Kampfsperre von ".df($cfg->p1("battleban_time"))." bis ".df($cfg->p2("battleban_time")).". ".$cfg->p1("battleban");
				$battleban = true;
			}

			if ($cfg->value("flightban")!=0 && $cfg->p1("flightban_time")<=time() && $cfg->p2("flightban_time") > time())
			{
				$this->error = "Flottensperre von ".df($cfg->p1("flightban_time"))." bis ".df($cfg->p2("flightban_time")).". ".$cfg->p1("flightban");
			}
			else
			{
                $noobProtectionErrorAdded = false;

				// Test each possible action
				foreach ($actions as $i)
				{
					// variable to check whether a support overflow error message should be printed
					$supportPossible = true;

					$ai = FleetAction::createFactory($i);

					// Skip this action if it is an alliance action and ABS is disabled
                    // and if the owner of the target planet is not the same user (support)
                    // or if alliance battle system is only allowed for alliances at war
                    // and the source's and target's alliances aren't at war against each other
					if (
                        $this->sourceEntity->ownerId() != $this->targetEntity->ownerId() &&
                        $ai->allianceAction && (
                            // alliance battle system is disabled
                            $cfg->value("abs_enabled") != 1 || (
                                // or abs is enabled for alliances at war only
                                $cfg->p1("abs_enabled") == 1 && (
                                    (
                                        // and it is an agressive action
                                        $ai->attitude() == 3 &&
                                        // and the two alliances are not at war against each other
                                        ! $this->sourceEntity->owner->alliance->checkWar($this->targetEntity->ownerAlliance())
                                    ) || (
                                        // or it is a defensive action
                                        $ai->attitude() == 1 &&
                                        // and the user's alliance is not at war
                                        ! $this->owner->alliance->isAtWar()
                                    )
                                )
                            )
                        )
                    )
                    {
						continue;
					}

					// Permission checks
					if (
						// Action is allowed if:
						(
							// * Source and target are the same and the action allows that
							($this->sourceEntity->id() == $this->targetEntity->id() && $ai->allowSourceEntity()) ||
							// * source and target are different but belong to the same user and the action is possible for the same user (e.g. ok for transport, not ok for attack)
							($this->sourceEntity->ownerId() == $this->targetEntity->ownerId() && $this->sourceEntity->id() != $this->targetEntity->id() && $ai->allowOwnEntities()) ||
							// * source and target are from different users and target belongs to an user (so it's not a nebula for example) and the action allows any other player's planet as target
							($this->sourceEntity->ownerId() != $this->targetEntity->ownerId() && $this->targetEntity->ownerId()>0 && $ai->allowPlayerEntities()) ||
							// * target doesn't belong to an user and action allows that (e.g. crystal collection from nebulas)
							($this->targetEntity->ownerId() == 0 && $ai->allowNpcEntities()) ||
							// * action allows only same-alliance users and source and target user belong to the same alliance (alliance >0 -> they have an alliance) OR same user for no alliance
							//   this is used only for support, so in case different user there is also a check whether there are available support slots on the planet (checkDefNum)
							($ai->allowAllianceEntities && $this->sourceEntity->ownerAlliance()==$this->targetEntity->ownerAlliance() && ($this->sourceEntity->ownerId() == $this->targetEntity->ownerId() || ($this->sourceEntity->ownerAlliance() > 0 && ($supportPossible = $this->checkDefNum()))))
						) &&
						(!$ai->allianceAction || $this->getAllianceSlots()>0 || $allowed) //this last check, checks for every AllianceAction support, alliance if there is a empty slot
					)
					{
						//Check for exclusive Actions
						$exclusiceAllowed = true;
						if ($ai->exclusive())
						{
							foreach($this->getShips() as $ship)
							{
								if (!(in_array($ai->code(),$ship['actions']) || $ship['special']))
								{
									$exclusiceAllowed = false;
									break;
								}
							}
						}
						if ($exclusiceAllowed)
						{
							if($this->targetEntity->ownerId()>0)
							{
								if (!$this->targetEntity->ownerHoliday() || $ai->allowOnHoliday())
								{
									if ($ai->attitude() > 1)
									{
										if (!$battleban)
										{
											if ($ai->allowActivePlayerEntities()
												|| $this->targetEntity->owner->isInactivLong()
												|| ($this->ownerId == $this->sourceEntity->lastUserCheck())
												)
											{
												if($this->owner->canAttackPlanet($this->targetEntity))
												{
													if (strpos($ai,'Bombardierung'))
													{
														if($this->owner->alliance->checkWar($this->targetEntity->owner->alliance->id))
															$actionObjs[$i] = $ai;
													}
													else
														$actionObjs[$i] = $ai;
												}
												else if (!$noobProtectionErrorAdded)
												{
													$this->error .= 'Der Besitzer des Ziels steht unter Anfängerschutz! '
                                                        .'Die Punkte des Users müssen zwischen '.(USER_ATTACK_PERCENTAGE*100).'% und '
                                                        .(100/USER_ATTACK_PERCENTAGE).'% von deinen Punkten liegen.<br />'
                                                        .'Ausserdem müssen beide Spieler mindestens '.(USER_ATTACK_MIN_POINTS)
                                                        .' Punkte haben.<br />';
                                                    // only add error message once, not for every action
                                                    $noobProtectionErrorAdded = true;
												}
											} // if ($ai->allowActivePlayerEntities() || ($this->targetEntity->owner->isInactiv() && !$ai->allowActivePlayerEntities()))
										} // if (!$battleban)
									} // if ($ai->attitude() > 1)
									else
									{
										$actionObjs[$i] = $ai;
									}
								} // if (!$this->targetEntity->ownerHoliday() || $ai->allowOnHoliday())
								else
								{
									$this->error .= "Der Besitzer des Ziels ist im Urlaub; viele Aktionen sind deshalb nicht möglich!<br />";
								}
							} // if($this->targetEntity->ownerId()>0)
							else
							{
								$actionObjs[$i] = $ai;
							}
						} // if ($exclusiceAllowed)
					} // Permission checks
					// print error message if support slots check failed
					if(!$supportPossible)
					{
						// Meldung ausgeben, dass Support nicht möglich ist
						$this->error .= 'Support nicht m&ouml;glich, die Maximalzahl von '.
							$cfg->p1('alliance_fleets_max_players').
							' Verteidigern ist auf diesem Planet bereits erreicht.<br />';
						$supportPossible = true;
					}
				} // foreach ($actions as $i)
			} // else Flottensperre
			//echo dump($actionObjs);
			return $actionObjs;
		}

		function getSpeed()
		{
			return $this->speed * $this->sBonusSpeed * $this->speedPercent / 100 ;
		}

		function getShips()
		{
			return $this->ships;
		}

		function getCosts()
		{
			$this->costs = ceil($this->costsPerHundredAE / 100 * $this->distance * $this->speedPercent / 100) + ceil($this->costsPerHundredAE1 / 100 * $this->distance1 * $this->speedPercent1 / 100);
			$this->costs += $this->costsLaunchLand;
			$this->capacityFuelUsed =$this->costs;
			return $this->costs;
		}

		function getCostsFood()
		{
			$cfg = Config::getInstance();
			$this->costsFood = ceil($this->getPilots() * $cfg->value('people_food_require')/3600 * $this->getDuration());
			return $this->costsFood;
		}

		function getCostsPower()
		{
			return $this->costsPower;
		}

		function getDuration()
		{
			return $this->duration + $this->duration1;
		}

		function getSpeedPercent()
		{
			return $this->speedPercent;
		}

		function setSpeedPercent($perc)
		{
			$this->speedPercent = max(1,min(100,$perc));
			$this->duration = $this->distance / $this->getSpeed();	// Calculate duration
			$this->duration *= 3600;	// Convert to seconds
			$this->duration += $this->getTimeLaunchLand();	// Add launch and land time
			$this->duration = ceil($this->duration);
		}

		function getCostsPerHundredAE()
		{
			return ceil($this->costsPerHundredAE * $this->speedPercent / 100);
		}

		function getTimeLaunchLand()
		{
			return ceil($this->timeLaunchLand * (2 - $this->sBonusReadiness));
		}

		function getCostsLaunchLand()
		{
			return $this->costsLaunchLand;
		}

		function getCapacity()
		{
			return $this->getTotalCapacity() - $this->capacityResLoaded - $this->capacityFuelUsed - $this->costsFood - $this->supportCostsFood - $this->supportCostsFuel;
		}

		function getTotalCapacity()
		{
			return $this->capacityTotal * $this->sBonusCapacity;
		}

		function getPeopleCapacity()
		{
			return $this->capacityPeopleTotal - $this->capacityPeopleLoaded;
		}

		function getTotalPeopleCapacity()
		{
			return $this->capacityPeopleTotal;
		}

		private function calcResLoaded()
		{
			$this->capacityResLoaded = 0;
			foreach ($this->res as $i)
			{
				$this->capacityResLoaded += $i;
			}
		}

		function getLoadedRes($id)
		{
			return ($this->res[$id]>0) ? $this->res[$id] : 0;
		}

		function loadResource($id,$ammount,$finalize=0)
		{
			// $ammount = max(0,$ammount);
			$this->res[$id] = 0;
			$this->calcResLoaded();
			if ($ammount >= 0)
			{
				if ($id==4)
				{
					$loaded = floor(min($ammount,$this->getCapacity(),$this->sourceEntity->getRes($id)-$this->getSupportFuel()-$this->getCosts()));
				}
				elseif ($id==5)
				{
					$loaded = floor(min($ammount,$this->getCapacity(),$this->sourceEntity->getRes($id)-$this->getSupportFood()-$this->getCostsFood()));
				}
				else
				{
					$loaded = floor(min($ammount,$this->getCapacity(),$this->sourceEntity->getRes($id)));
				}
			}
			else
			{
				if ($id==4)
				{
					$loaded = floor(min($this->getCapacity(),max(0,$this->sourceEntity->getRes($id) + $ammount - $this->getSupportFuel() - $this->getCosts())));
				}
				elseif ($id==5)
				{
					$loaded = floor(min($this->getCapacity(),max(0,$this->sourceEntity->getRes($id) + $ammount - $this->getSupportFood() - $this->getCostsFood())));
				}
				else
				{
					$loaded = floor(min($this->getCapacity(),max(0,$this->sourceEntity->getRes($id) + $ammount)));
				}

			}
			$this->res[$id] = $loaded;
			$this->calcResLoaded();

			/*if ($finalize==1)
			{
				$this->sourceEntity->chgRes($id,-$loaded);
			}*/
			return $loaded;
		}

		// subtracts the payload ress (not support/flight fuel and food)
		function finalLoadResource()
		{
			global $resNames;

			$this->sourceEntity->reloadRes();
			$resarr = array();

			foreach ($resNames as $rk => $rn)
			{
				$id = $rk+1;
				if ($this->res[$id] >= 0)
				{
					$ammount = $this->res[$id];
				}
				else
				{
					if ($id==4)
					{
						$ammount = max(0,$this->sourceEntity->getRes($id) + $this->res[$id] - $this->getSupportFuel() - $this->getCosts());
					}
					elseif ($id==5)
					{
						$ammount = max(0,$this->sourceEntity->getRes($id) + $this->res[$id] - $this->getSupportFood() - $this->getCostsFood());
					}
					else
						$ammount = max(0,$this->sourceEntity->getRes($id) + $this->res[$id]);
				}

				$this->res[$id] = 0;
				$this->calcResLoaded();
				if ($id==4) {
					$loaded = floor(min($ammount,$this->getCapacity(),$this->sourceEntity->getRes($id)-$this->getSupportFuel()-$this->getCosts()));
				}
				elseif ($id==5) {
					$loaded = floor(min($ammount,$this->getCapacity(),$this->sourceEntity->getRes($id)-$this->getSupportFood()-$this->getCostsFood()));
				}
				else {
					$loaded = floor(min($ammount,$this->getCapacity(),$this->sourceEntity->getRes($id)));
				}
				$this->res[$id] = $loaded;
				$resarr[$rk] = $loaded;
			}

			$this->calcResLoaded();

			$this->sourceEntity->subRes($resarr);
		}

		function loadPeople($ammount)
		{
			$ammount = max(0,$ammount);
			$this->capacityPeopleLoaded = floor(min($ammount,$this->capacityPeopleTotal,($this->pilotsAvailable()-$this->getPilots())));

			return $this->capacityPeopleLoaded;
		}


		function fetchResource($id,$ammount)
		{
			$ammount = max(0,$ammount);
			$this->fetch[$id] = 0;
			$this->calcResLoaded();
			$loaded = floor($ammount);
			$this->fetch[$id] = $loaded;
			$this->calcResLoaded();

			return $loaded;
		}

		function resetSupport() {
			$this->supportTime=0;
			$this->supportCostsFood=0;
			$this->supportCostsFuel=0;
		}

		function getSupportTime() {
			return $this->supportTime;
		}

		function setSupportTime($time) {
			$this->supportTime = $time;

			$this->supportCostsFood = ceil($time*$this->supportCostsFoodPerSec);
			$this->supportCostsFuel = ceil($time*$this->supportCostsFuelPerSec);
		}

		function getSupportFood() {
			return $this->supportCostsFood;
		}

		function getSupportFuel() {
			return $this->supportCostsFuel;
		}

		function getSupportMaxTime() {
			$cfg = Config::getInstance();

			$this->supportCostsFuel = 0;
			$this->supportCostsFood = 0;

			$this->supportCostsFoodPerSec = $this->pilots * $cfg->value('people_food_require')/36000;
			$this->supportCostsFuelPerSec = $this->costsPerHundredAE*$this->getSpeed()/$this->getSpeedPercent()/3600000;

			$maxTime = $this->getCapacity() / ($this->supportCostsFuelPerSec+$this->supportCostsFoodPerSec);

			$supportTimeFuel = ($this->sourceEntity->getRes(4)-$this->getLoadedRes(4)-$this->getCosts())/$this->supportCostsFuelPerSec;

			if ($this->supportCostsFoodPerSec)
				$supportTimeFood = ($this->sourceEntity->getRes(5)-$this->getLoadedRes(5)-$this->getCostsFood())/$this->supportCostsFoodPerSec;
			else
				$supportTimeFood = $supportTimeFuel;

			if($supportTimeFuel>0)
				$maxTime = min($maxTime,min($supportTimeFuel,$supportTimeFood));
			else
				$maxTime = min($maxTime,$supportTimeFood);

			return floor($maxTime);
		}

		function getSupport() {
			return "Supportkosten";
		}

		function getSupportDesc() {
			$this->calcSupportTime();

			return "".RES_FUEL.": ".nf($this->supportCostsFuelPerSec*$this->supportTime)." (".nf($this->supportCostsFuelPerSec*3600)." pro h)<br style=\"clear:both\" />".RES_FOOD.": ".nf($this->supportCostsFoodPerSec*$this->supportTime)." (".nf($this->supportCostsFoodPerSec*3600)." pro h)";
		}

		function setLeader($id) {
			$this->leaderId = $id;
		}

		function setFakeId($id) {
			$this->fakeId = $id;
		}

		function loadAllianceFleets() {
			$this->sFleets = array();
			$this->aFleets = array();
			if ($this->sourceEntity->ownerAlliance()) {
				$res = dbquery("
						SELECT
							id,
							user_id,
							entity_to,
							landtime
						FROM
							fleet
						WHERE
							leader_id>'0'
							AND action='alliance'
							AND next_id='".$this->sourceEntity->ownerAlliance()."'
							AND status='0'
						ORDER BY
							landtime ASC;");
				if (mysql_num_rows($res)>0) {
					while($arr=mysql_fetch_assoc($res)) {
						array_push($this->aFleets,$arr);
					}
				}

				$res = dbquery("
							SELECT
								entity_to
							FROM
								`fleet`
							WHERE
								action='support'
								AND (status='0' || status='3')
							GROUP BY
								entity_to;");
				if (mysql_num_rows($res)>0) {
					while ($arr=mysql_fetch_row($res)) {
						array_push($this->sFleets,$arr[0]);
					}
				}
			}
		}

		function setAllianceSlots($num) {
			$this->allianceSlots = $num + 1;

			$this->loadAllianceFleets();
		}

		function getAllianceSlots() {
			if ($this->sourceEntity->ownerAlliance() && isset($this->allianceSlots))
			{
				return $this->allianceSlots - count($this->aFleets) - count($this->sFleets);
			}
		}

		// Alliance attack already confirmed
		function checkAttNum($leaderid)
		{
			$cfg = Config::getInstance();
			if(!$cfg->value('alliance_fleets_max_players'))
			{
				return true;
			}
			// Check number of users participating in the alliance attack
			$res = dbquery('
				SELECT
					`user_id`
				FROM
					`fleet`
				WHERE
					`leader_id` = '.$leaderid.'
				GROUP BY
					`user_id`
			;');
			if(mysql_num_rows($res) < $cfg->p1('alliance_fleets_max_players'))
			{
				return true;
			}
			while($arr = mysql_fetch_assoc($res))
			{
				if($this->ownerId == $arr['user_id'])
				{
					return true;
				}
			}
			return false;
		}

		function checkDefNum()
		{
			$cfg = Config::getInstance();
			if(!$cfg->value('alliance_fleets_max_players'))
			{
				return true;
			}
			// check the number of supporters on that planet
			$res = dbquery('
				SELECT
					`user_id`
				FROM
					`fleet`
				WHERE
					`action`="support"
				AND
					(status=0 || status=3)
				AND
					`entity_to` = "'.$this->targetEntity->id().'"
				AND
					`user_id` != "'.$this->targetEntity->ownerId().'"
				GROUP BY
					`user_id`
			;');
			// user id is guaranteed to not be the target owner, so the number is reduced
			// by one, because we always have one slot reserved for the planet's owner
			if(mysql_num_rows($res) < ($cfg->p1('alliance_fleets_max_players') - 1))
			{
				return true;
			}
			// if the maximum of user slots is already reached, we check whether there
			// is already a support fleet from the same user
			while($arr = mysql_fetch_assoc($res))
			{
				// if the user already supports this planet with one fleet, he can
				// send even more fleets to support the same planet
				if($this->ownerId == $arr['user_id'])
				{
					return true;
				}
			}
			return false;
		}


		//
		// Getters
		//
		function ownerId() { return $this->ownerId; }
		function error() { return $this->error; 	}
		function raceSpeedFactor() { return $this->raceSpeedFactor; }
		function pilotsAvailable() { return $this->pilotsAvailable; }
		function possibleFleetStarts() { return $this->possibleFleetStarts; }
		function fleetSlotsUsed() { return $this->fleetSlotsUsed; }
		function fleetControlLevel() { return $this->fleetControlLevel; }

		function getDistance()
		{
			return $this->distance + $this->distance1;
		}

		function getShipCount()
		{
			return $this->shipCount;
		}

		function getPilots()
		{
			return $this->pilots * $this->sBonusPilots;
		}

		function getLeader() { return $this->leaderId; }


	}

?>
