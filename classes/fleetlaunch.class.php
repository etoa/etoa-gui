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
		var $ownerId;

		var $ships;
		var $shipCount;	
		var $shipsFixed;

		var $speed;
		var $speedPercent;
		var $duration;
		var $costsPerHundredAE;
		var $timeLaunchLand;
		var $costsLaunchLand;
		var $pilots;
		var $capacityTotal;
		var $capacityResUses;
		var $capacityFuelUsed;
		var $capacityPeopleTotal;
		var $capacityPeopleLoaded;
		
		var $distance;

		private $action;
		private $resources;
		private $error;
		
		/**
		* The constructor
		*
		* >> Step 1 <<
		*/
		function Fleetlaunch(&$sourceEnt,&$ownerEnt)
		{
		
			$this->sourceEntity = $sourceEnt;
			$this->owner = $ownerEnt;
			$this->ownerId = $ownerEnt->id;
			$this->ownerRaceName = $ownerEnt->race->name;
			$this->raceSpeedFactor = $ownerEnt->race->speedFactor;
			$this->possibleFleetStarts = 0;
			$this->fleetSlotsUsed = 0;
			$this->fleetControlLevel =0;
			
			$this->ships = array();
			$this->speedPercent=100;
			$this->speed = 0;
			$this->duration=0;
			$this->action='';
			$this->costsPerHundredAE=0;
			$this->timeLaunchLand=0;
			$this->costsLaunchLand=0;
			$this->pilots=0;
			$this->pilotsAvailable = 0;
			$this->capacityTotal=0;
			$this->capacityResLoaded=0;
			$this->capacityFuelUsed=0;
			$this->capacityPeopleTotal=0;
			$this->capacityPeopleLoaded=0;		
			$this->shipCount=0;
			$this->distance=0;
			$this->res = array(0,0,0,0,0,0);
			$this->costs = 0;
			$this->costsFood = 0;
			$this->costsPower = 0;
			$this->supportTime = 0;
			$this->supportCostsFood = 0;
			$this->supportCostsFuel = 0;
			$this->supportCostsFuelPerSec = 0;
			$this->supportCostsFoodPerSec = 0;
			$this->leaderId = 0;

			$this->shipActions = array();

			$this->havenOk=false;
			$this->shipsFixed=false;
			$this->targetOk=false;
			$this->actionOk=false;			
			
			$this->error="";
			
			//Create targetentity
			if (isset($_SESSION['haven']['targetId'])) {
				$this->targetEntity = Entity::createFactoryById($_SESSION['haven']['targetId']);
			}
			elseif (isset($_SESSION['haven']['cellTargetId'])) {
				$this->targetEntity = Entity::createFactoryUnkownCell($_SESSION['haven']['cellTargetId']);
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

				$bl = new BuildList($this->sourceEntity->id());

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
					$this->possibleFleetStarts = FLEET_NOCONTROL_NUM + $this->fleetControlLevel - $this->fleetSlotsUsed;
					
					if ($this->possibleFleetStarts > 0)
					{					
						// Piloten
						$this->pilotsAvailable = floor($this->sourceEntity->people() - $bl->totalPeopleWorking());			
					
						$this->havenOk = true;
					}
					else
					{
						$this->error = "Von hier können keine weiteren Flotten starten, alle Slots (".$this->fleetSlotsUsed.") sind belegt!";
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
						$timefactor = 0;
						$vres=dbquery("
								SELECT
									techlist.techlist_current_level,
									ship_requirements.req_req_tech_level
								FROM
									techlist
								INNER JOIN
									ship_requirements
								ON ship_requirements.req_req_tech_id=techlist.techlist_tech_id
									AND ship_requirements.req_ship_id=".$sid."
									AND techlist.techlist_user_id=".$this->ownerId()."
								GROUP BY
									ship_requirements.req_id;");
							
							$timefactor=$this->raceSpeedFactor();
							if (mysql_num_rows($vres)>0)
							{
								while ($varr=mysql_fetch_array($vres))
								{
									if($varr['techlist_current_level']-$varr['req_req_tech_level']<=0)
									{
										$timefactor+=0;
									}
									else
									{
										$timefactor+=max(0,($varr['techlist_current_level']-$varr['req_req_tech_level'])*0.1);
									}
								}
							}
						$cnt = min(nf_back($cnt),$arr['shiplist_count']);
						
						$this->ships[$sid] = array(
						"count" => $cnt,
						"speed" => $arr['ship_speed']*$timefactor,
						"fuel_use" => $arr['ship_fuel_use'] * $cnt,
						"name" => $arr['ship_name'],
						"pilots" => $arr['ship_pilots'] * $cnt					
						);
			
						$this->shipActions = array_merge($this->shipActions,explode(",",$arr['ship_actions']));
						$this->shipActions = array_unique($this->shipActions);
			
						// Set global speed
						if ($this->speed <= 0)
						{
							$this->speed = $arr['ship_speed']*$timefactor;
						}
						else
						{
							$this->speed = min($this->speed, $arr['ship_speed']*$timefactor);
						}													     
						
						$this->timeLaunchLand = max($this->timeLaunchLand, $arr['ship_time2land'] + $arr['ship_time2start']);
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
			if (!$this->shipsFixed)
			{
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
						return $this->shipsFixed;
					}
					else
						$this->error = "Es sind zuwenig Piloten für diese Flotte vorhanden.(".$this->pilotsAvailable()." verfügbar, ".$this->getPilots()." benötigt)";
				}
				else
					$this->error = "Kann Schiffauswahl nicht fertigstellen, es wurde keine Schiffe zur Flotte hinzugefügt.";
			}
			else
				$this->error = "Kann Schiffauswahl nicht fertigstellen, die Flotte wurde bereits fertig zusammengestellt!";
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
					$this->distance = $this->sourceEntity->distance($this->targetEntity);			
					
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
					if ($this->getCapacity()>0)
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
				if (isset($actions[$actionCode]));
				{
					$this->action = $actionCode;
					
					$this->actionOk = true;
					return true;
				}
			}
			return false;
		}

		
		function launch()
		{
			if ($this->actionOk)
			{
				$time = time();
				
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
					// Subtract flight costs from source
					$this->sourceEntity->chgRes(4,-$this->getCosts());
					$this->sourceEntity->chgPeople(-($this->pilots+$this->capacityPeopleLoaded));
					
					if ($this->action=="alliance" && $this->leaderId!=0) $status=3;
					else $status = 0;
					
					// Create fleet record
					$sql = "
					INSERT INTO
						fleet
					(
						user_id,
						leader_id,
						entity_from,
						entity_to,
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
						".$time.",
						".($time+$this->duration).",
						".$this->supportTime.",
						'".$this->action."',
						'".$status."',
						".$this->pilots.",
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
						0
					)
					";
					dbquery($sql);
					$fid = mysql_insert_id();
					
					foreach ($this->ships as $sid => $sda)
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
					$this->sourceEntity->chgRes(5,-$this->getCostsFood());
					return $fid;
				}
				else
					$this->error = "Konnte keine Schiffe zur Flotte hinzufügen da keine vorhanden sind!";
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
		}				
		
		
		
		
		/**
		* 
		*/
		function getAllowedActions()
		{
			$cfg = Config::getInstance();
			
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
				// Test each possible action
				foreach ($actions as $i)
				{
					$ai = FleetAction::createFactory($i);
					
					
	
					// Permission checks
					if (
					($this->sourceEntity->id() == $this->targetEntity->id() && $ai->allowSourceEntity()) || 
					($this->sourceEntity->ownerId() == $this->targetEntity->ownerId() && $this->sourceEntity->id() != $this->targetEntity->id() && $ai->allowOwnEntities()) ||
					($this->sourceEntity->ownerId() != $this->targetEntity->ownerId() && $this->targetEntity->ownerId()>0 && $ai->allowPlayerEntities()) ||
					($this->targetEntity->ownerId() == 0 && $ai->allowNpcEntities()) || ($ai->allowAllianceEntities && $this->sourceEntity->ownerAlliance()==$this->targetEntity->ownerAlliance())
					)
					{
						if($this->targetEntity->ownerId()>0)
						{
							if (!$this->targetEntity->ownerHoliday() || $ai->allowOnHoliday())
							{
								if ($ai->attitude() > 1)
								{
									if (!$battleban)
									{
										if( ( $this->sourceEntity->ownerPoints()*USER_ATTACK_PERCENTAGE <= $this->targetEntity->ownerPoints() 
										&& $this->sourceEntity->ownerPoints()/USER_ATTACK_PERCENTAGE >= $this->targetEntity->ownerPoints() ) 
										|| $uarr['user_last_online']<INACTIVE_TIME 
										|| $this->targetEntity->ownerLocked() )
										{
											$actionObjs[$i] = $ai;
										}
										else
										{
											$this->error = "Der Besitzer des Ziels steht unter Anfängerschutz!  Die Punkte des Users m&uuml;ssen zwischen ".(USER_ATTACK_PERCENTAGE*100)."% und ".(100/USER_ATTACK_PERCENTAGE)."% von deinen Punkten liegen";
										}
									}
								}
								else
								{
									$actionObjs[$i] = $ai;
								}
							}
							else
							{
								$this->error = "Der Besitzer des Zielst ist im Urlaub; viele Aktionen sind deshalb nicht möglich!";
							}
						}
						else
						{
							$actionObjs[$i] = $ai;
						}
					}
				}
			}	
			return $actionObjs;			
		}
		
		

		

		

		
		function getSpeed()
		{
			return $this->speed * $this->speedPercent / 100 ;
		}
		
		function getShips()
		{
			return $this->ships;	
		}
		
		function getCosts()
		{
			$this->costs = ceil($this->costsPerHundredAE / 100 * $this->distance * $this->speedPercent / 100);
			$this->costs += $this->costsLaunchLand;
			$this->capacityFuelUsed =$this->costs;
			return $this->costs;
		}
		
		function getCostsFood()
		{
			$cfg = Config::getInstance();
			$this->costsFood = ceil($this->pilots * $cfg->value('people_food_require')/3600 * $this->getDuration());
			return $this->costsFood;
		}

		function getCostsPower()
		{
			return $this->costsPower;
		}		
		
		function getDuration()
		{
			return $this->duration;
		}
		
		function getSpeedPercent()
		{
			return $this->speedPercent;
		}
		
		function setSpeedPercent($perc)
		{
			$this->speedPercent = min(100,$perc);
			$this->duration = $this->distance / $this->getSpeed();	// Calculate duration
			$this->duration *= 3600;	// Convert to seconds
			$this->duration += $this->timeLaunchLand;	// Add launch and land time
			$this->duration = ceil($this->duration);
		}
		
		function getCostsPerHundredAE()
		{
			return ceil($this->costsPerHundredAE * $this->speedPercent / 100);
		}
		
		function getTimeLaunchLand()
		{
			return $this->timeLaunchLand;
		}
		
		function getCostsLaunchLand()
		{
			return $this->costsLaunchLand;
		}		
		       
		function getCapacity()
		{
			return $this->capacityTotal - $this->capacityResLoaded - $this->capacityFuelUsed - $this->costsFood - $this->supportCostsFood - $this->supportCostsFuel;
		}

		function getTotalCapacity()
		{
			return $this->capacityTotal;
		}
		
		function getPeopleCapacity()
		{
			return $this->capacityPeopleTotal - $this->capacityPeopleLoaded;		
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
			$ammount = max(0,$ammount);
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
			$this->calcResLoaded();
			
			if ($finalize==1)
			{
				$this->sourceEntity->chgRes($id,-$loaded);
			}			
			return $loaded;
		}
		
			
		function fetchResource($id,$ammount)
		{
			$ammount = max(0,$ammount);
			$this->fetch[$id] = 0;
			$this->calcResLoaded();
			$loaded = floor(min($ammount,$this->getCapacity()));
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
				
			$maxTime = min($maxTime,min($supportTimeFuel,$supportTimeFood));
			
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
			return $this->distance;
		}

		function getShipCount()
		{
			return $this->shipCount;
		}
		
		function getPilots()
		{
			return $this->pilots;	
		}
		
		function getLeader() { return $this->leaderId; }


	}

?>