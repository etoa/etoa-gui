<?PHP
	
	/**
	* Handles data and actions for a fleet object
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/
	class Fleet
	{
		//
		// Variables
		//
		private $id;
		private $valid;
		private $ownerId;
		private $sourceId;
		private $targetId;
		private $actionCode;
		private $status;
		private $launchTime;
		private $landTime;
		private $nextActionTime;
		private $ships;
		
		/**
		* Constructor
		*
		* Creates a new fleet object and loads
		* it's data from the table
		*/
		function Fleet($fid,$uid=-1,$leadid=-1)
		{
			if ($uid>=0) {
				$uidStr = " AND user_id=".$uid."";
			} else {
				$uidStr = "";
			}
				
			if ($leadid>0) {
				$leadStr = " leader_id=".$leadid."";
			} else {
				$leadStr = " id=".$fid."";
			}
			
			$this->valid = false;
			$res = dbquery("
			SELECT
				*
			FROM
				fleet
			WHERE
				 ".$leadStr."
				".$uidStr."
			ORDER BY 
				launchtime ASC
			;");
			if (mysql_num_rows($res) > 0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->id = $fid;
				$this->ownerId = $arr['user_id'];
				$this->leaderId = $arr['leader_id'];
				$this->sourceId = $arr['entity_from'];
				$this->targetId = $arr['entity_to'];
				$this->nextId = $arr['next_id']; //special value for alliance Actions
				$this->actionCode = $arr['action'];
				$this->status = $arr['status'];
				$this->launchTime = $arr['launchtime'];
				$this->landTime = $arr['landtime'];
				$this->nextActionTime = $arr['nextactiontime'];
				$this->pilots = $arr['pilots'];

				$this->usageFuel = $arr['usage_fuel'] + $arr['support_usage_fuel'];
				$this->usageFood = $arr['usage_food'] + $arr['support_usage_food'];
				$this->usagePower = $arr['usage_power'];

				$this->resMetal = $arr['res_metal'];
				$this->resCrystal = $arr['res_crystal'];
				$this->resPlastic = $arr['res_plastic'];
				$this->resFuel = $arr['res_fuel'];
				$this->resFood = $arr['res_food'];
				$this->resPower = $arr['res_power'];
				$this->resPeople = $arr['res_people'];
                
				$this->valid = true;
				
				// TODO: Needs some improvement / redesign
				if (mysql_num_rows($res) > 1)
				{
					$this->fleets = array();
					while ($arr = mysql_fetch_assoc($res))
					{
						if ($arr['status']==3) {
							$this->fleets[$arr['id']] = new Fleet($arr['id']);
						}
					}
				}
				else
					$this->fleets = array(); // Hack to avoid notice error!
			}
		}
		
		//
		// Getters
		//
		function valid() { return $this->valid;	}
		function id() { return $this->id; }
		function ownerId() { return $this->ownerId; }
		function leaderId() { return $this->leaderId; }
		function launchTime() {	return $this->launchTime; }
		function landTime() {	return $this->landTime;	}
		
		function ownerAllianceId()
		{
			$res = dbquery("
						   	SELECT
								user_alliance_id
							FROM
								users
							WHERE
								user_id='".$this->ownerId()."'
							LIMIT 1;");
			$arr = mysql_fetch_row($res);
			return $arr[0];
		}

		function remainingTime() 
		{	
			return max(0,$this->landTime-time()); 
		}

		function pilots($fleet=-1)
		{
			$cnt = 0;
			if ($fleet<0 && count($this->fleets))
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->pilots();
				}
			}
			return $this->pilots + $cnt;
		
		}		
		function status() 
		{ 
			return $this->status; 
		}

		function usageFuel($fleet=-1) {
			$cnt = 0;
			if ($fleet<0 && count($this->fleets) )
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->usageFuel();
				}
			}
			return $this->usageFuel + $cnt;
		}
		
		function usageFood($fleet=-1) {
			$cnt = 0;
			if ($fleet<0 && count($this->fleets))
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->usageFood();
				}
			}
			return $this->usageFood + $cnt;
		}
		
		function usagePower($fleet=-1) {
			$cnt = 0;
			if ($fleet<0 && count($this->fleets))
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->usagePower();
				}
			}
			return $this->usagePower + $cnt;
		}
	
		function resMetal($fleet=-1) {
			$cnt = 0;
			if ($fleet<0 && count($this->fleets))
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->resMetal();
				}
			}
			return $this->resMetal + $cnt;
		}
	
		function resCrystal($fleet=-1) {
			$cnt = 0;
			if (count($this->fleets) && $fleet<0)
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->resCrystal();
				}
			}
			return $this->resCrystal + $cnt;
		}
	
		function resPlastic($fleet=-1) {
			$cnt = 0;
			if (count($this->fleets) && $fleet<0)
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->resPlastic();
				}
			}
			return $this->resPlastic + $cnt;
		}
	
		function resFuel($fleet=-1) {
			$cnt = 0;
			if (count($this->fleets) && $fleet<0)
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->resFuel();
				}
			}
			return $this->resFuel + $cnt;
		}
	
		function resFood($fleet=-1) {
			$cnt = 0;
			if (count($this->fleets) && $fleet<0)
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->resFood();
				}
			}
			return $this->resFood + $cnt;
		}
	
		function resPower($fleet=-1) {
			$cnt = 0;
			if (count($this->fleets) && $fleet<0)
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->resPower();
				}
			}
			return $this->resPower + $cnt;
		}
	
		function resPeople($fleet=-1) {
			$cnt = 0;
			if (count($this->fleets) && $fleet<0)
			{
				foreach($this->fleets as $id=>$f)
				{
					$cnt += $f->resPeople();
				}
			}
			return $this->resPeople + $cnt;
		}
		
		/**
		* Loads the source entity (if needed) and returns it
		*/		
		function & getSource()
		{
			if (!isset($this->source))
			{
				if ($this->getAction()->visibleSource())
					$this->source = Entity::createFactoryById($this->sourceId);
				else
					$this->source = Entity::createFactory($this->getAction()->sourceCode());
			}
			return $this->source;
		}		

		/**
		* Loads the target entity (if needed) and returns it
		*/
		function & getTarget()
		{
			if (!isset($this->target))
			{
				$this->target = Entity::createFactoryById($this->targetId);
			}
			return $this->target;
		}
		
		/**
		* Loads the home entity (if needed) and returns it, special for the support action!!
		*/
		function getHome()
		{
			if (!isset($this->home))
			{
				$this->home = Entity::createFactoryById($this->nextId);
			}
			return $this->home;
		}
		
		/**
		* Loads and returns the flet action object
		*/
		function getAction()
		{
			if (!isset($this->action))
			{
				$this->action = FleetAction::createFactory($this->actionCode);
			}
			return $this->action;
		}
		
		
		/**
		* Load fleet's ship id's and stores them 
		* in the shipIds array
		*/
		private function loadShipIds($fleet=-1)
		{
			$this->shipsIds = array();
			$this->shipCount = 0;	
			if (count($this->fleets) && $fleet<0)
			{	
				$sres = dbquery("
					SELECT 
						fs_ship_id, 
						SUM( fs_ship_cnt )
					FROM 
						fleet_ships
					INNER JOIN 
						fleet 
					ON 
						fleet.id = fs_fleet_id
						AND fleet.leader_id = '".$this->id."'
						AND (fs_ship_cnt > '0')
						AND fs_ship_faked = '0'
					GROUP BY 
						fs_ship_id
				;");
				
			}
			else
			{

				$sres = dbquery("
				SELECT
					fs_ship_id,
					fs_ship_cnt
				FROM
			fleet_ships
				WHERE
			fs_fleet_id='".$this->id."'
			AND fs_ship_cnt > '0'
			AND fs_ship_faked='0'
				;");
			}
			if (mysql_num_rows($sres)>0)
			{
				while ($arr=mysql_fetch_row($sres))
				{
					$this->shipsIds[$arr[0]] = $arr[1];
					$this->shipCount += $arr[1];
				}
			}
		}
		
		/**
		* Returns the total amount of ships
		* in the fleet (load them first if needed)
		*/
		function countShips()
		{
			if (!isset($this->shipsIds))
			{
				$this->loadShipIds();
			}
			return $this->shipCount;			
		}
		
		/**
		* Returns the array of the ship id's
		*/
		function getShipIds()
		{
			if (!isset($this->shipsIds))
			{
				$this->loadShipIds();
			}
			return $this->shipsIds;			
		}
		
		/**
		* Loads and returns an array of
		* all ship objects
		*/
		function getShips()
		{
			if (!isset($this->ships))
			{
				$this->ships = array();
				foreach ($this->getShipIds() as $sid=>$cnt)
				{
					$this->ships[$sid] = new Ship($sid);
				}		
			}
			return $this->ships;			
		}
		
		/**
		* Returns the full storage capacity
		*/
		function getCapacity()
		{
			$this->capacity = 0;
			$this-> BCapa = 1;
			foreach ($this->getShips() as $sid => $sobj)
			{
				$this->capacity += $sobj->capacity() * $this->shipsIds[$sid];
				$this-> BCapa += $sobj->bCapa*10;
			}

			return $this->capacity*$this->BCapa;
		}

		/**
		* Returns the full passenger capacity
		*/
		function getPeopleCapacity()
		{
			$this->peopleCapacity = 0;
			foreach ($this->getShips() as $sid => $sobj)
			{
				$this->peopleCapacity += $sobj->peopleCapacity() * $this->shipsIds[$sid];
			}
			return $this->peopleCapacity;
		}

		/**
		* Returns the free space for passengers
		*/
		function getFreePeopleCapacity()
		{
			return $this->getPeopleCapacity() - $this->resPeople;
		}
		
		/**
		* Returns the free storage capacity
		*/
		function getFreeCapacity()
		{
			return $this->getCapacity() 
			- $this->usageFuel
			- $this->usageFood
			-	$this->usagePower
			-	$this->resMetal
			-	$this->resCrystal
			-	$this->resPlastic
			-	$this->resFuel
			-	$this->resFood
			-	$this->resPower;
		}
		
		/**
		* Cancels the flight, this means that it sets on a
		* return course with the cancelled status flag enabled.
		* This is only possible if the fleet hasn't reached it's destination
		*/
		function cancelFlight($alliance = false, $is_child = false)
		{
			if ($this->status == 0 || $this->status == 3)
			{
				if ($this->landTime() > time() || $is_child)
				{
					if ($this->getAction()->cancelable())
					{
						if ($this->id == $this->leaderId)
						{
							if ($alliance)
							{
								foreach($this->fleets as $id=>$f)
								{
									$f->cancelFlight(false, true);
								}
							}
							else
							{
								$res = dbquery("SELECT
														id
												FROM
													fleet
												WHERE
													leader_id='".$this->id."'
													AND next_id='".$this->nextId."'
													AND status='3'
												LIMIT 1;");
								if (mysql_num_rows($res)>0)
								{
									$arr = mysql_fetch_row($res);
									dbquery("UPDATE
												fleet
											SET
												status='0',
												landtime='".$this->landTime."'
											WHERE
												id='".$arr[0]."'
											LIMIT 1;");
									dbquery("UPDATE
												fleet
											SET
												leader_id='".$arr[0]."'
											WHERE
												leader_id='".$this->id."';");
								}	
							}
						}
						
						$log = new Fleetlog($this->ownerId,$this->sourceId);
						$log->cancel($this->id,$this->launchTime,$this->landTime,$this->targetId,$this->actionCode,$this->status,$this->pilots);
						$log->addFleetRes(array($this->resMetal,$this->resCrystal,$this->resPlastic,$this->resFuel,$this->resFood),$this->resPeople,null,false);
						
                        // ### STATUS ###
                        // 0: Hinflug
                        // 1: Rückflug
                        // 2: Abgebrochen
                        // 3: Supporting
                        
						$time = time();
						// how long is the fleet already flying
						$difftime = 0;//time() - $this->launchTime;
						// what is the total flight time (one-way plus supporting time)
						$tottime = 0;//$this->landTime() - $this->launchTime + $this->nextActionTime;
						
						// status 3 => supporting at target
						if ($this->actionCode=="support" && $this->status==3)
						{
							// time supporting plus single way from source to target
							// (which is the same as target to source, thus nextActionTime)
							$difftime = $time - $this->launchTime + $this->nextActionTime;
							// total support time plus single way from source to target
							$tottime = $this->landTime() - $this->launchTime + $this->nextActionTime;
							
							$this->launchTime = $time;
							$this->landTime = $time + $this->nextActionTime;
							
							$this->targetId = $this->nextId;
                            
                            $this->removeSupportRes();
						}
						else
						{
							// how long is the fleet already flying on its way to target
							$difftime = $time - $this->launchTime;
							if($this->actionCode=="support") // support on its way to target
							{
								// total support time plus single way from source to target
								$tottime = $this->landTime() - $this->launchTime + $this->nextActionTime;
                                $this->removeSupportRes();
							}
							else
							{
								// single way from source to target
								$tottime = $this->landTime() - $this->launchTime;
							}
							
							$this->launchTime = $time;
							$this->landTime = $time + $difftime ;
							
							$tmp = $this->targetId;
							$this->targetId = $this->sourceId;
							$this->sourceId = $tmp;
						}
						
						$this->status = 2;
						$this->leaderId = 0;
						$passed = $difftime / $tottime;
						$returnFactor = 1 - $passed;

						// Fleet gets unused costs back
						$this->resFuel += ceil($this->usageFuel * $returnFactor);
						$this->resFood += ceil($this->usageFood * $returnFactor);
						$this->resPower += ceil($this->usagePower * $returnFactor);

						$this->usageFuel = floor($this->usageFuel * $passed);
						$this->usageFood = floor($this->usageFood * $passed);
						$this->usagePower = floor($this->usagePower * $passed);
						
						$log->fuel = $this->usageFuel;
						$log->food = $this->usageFood;
						$log->addFleetRes(array($this->resMetal,$this->resCrystal,$this->resPlastic,$this->resFuel,$this->resFood),$this->resPeople,null,true);
						if ($this->update())
							return true;

					}
					else
					{
						$this->error = "Abbruch nicht erlaubt!";
					}
				}
				else
					$this->error = "Flotte ist bereits beim Ziel angekommen!";
			}
			else
				$this->error = "Flotte ist bereits auf dem Rückflug!";
			return false;
		}

		/**
		* Returns the flight. This switches the source
		* and the target and adjusts the correct time
		*/
		function returnFlight()
		{
			if ($this->status == 0)
			{
				if ($this->landTime() > time())
					$difftime = $this->landTime() - $this->launchTime;
				else
					$difftime = time() - $this->launchTime;
				
				$this->launchTime = time();
				$this->landTime = $this->launchTime + $difftime ;
				
				$tmp = $this->targetId;
				$this->targetId = $this->sourceId;
				$this->sourceId = $tmp;
				
				$this->status = 1;
				$this->leaderId = 0;
	
				$this->update();	
				return true;			
			}
			else
				$this->error = "Flotte ist bereits auf dem R�ckflug!";
			return false;
		}
		
		/**
		* Updates changed data with the database
		*/
		private function update()
		{
			dbquery("
			UPDATE 
				fleet
			SET 
				launchtime='".$this->launchTime."',
				landtime='".$this->landTime."',
				entity_from=".$this->sourceId.",
				entity_to=".$this->targetId.",
				status='".$this->status."',
				leader_id='".$this->leaderId."',
				usage_fuel='".$this->usageFuel."',
				usage_food='".$this->usageFood."',
				usage_power='".$this->usagePower."',
				res_metal='".$this->resMetal."',
				res_crystal='".$this->resCrystal."',
				res_plastic='".$this->resPlastic."',
				res_fuel='".$this->resFuel."',
				res_food='".$this->resFood."',
				res_power='".$this->resPower."',
				res_people='".$this->resPeople."'
			WHERE 
				id='".$this->id."';");			
			if (mysql_affected_rows()>0)
				return true;
			return false;
		}

		/** 
		* Add a given ammount of ships specified by
		* their id to the fleet
		*/
		function addShips($shipId,$count)
		{
			dbquery("
			UPDATE 
				fleet_ships 
			SET 
				fs_ship_cnt=fs_ship_cnt+".$count." 
			WHERE 
				fs_fleet_id=".$this->id." 
				AND fs_ship_id=".$shipId.";");
			if (mysql_affected_rows()==0)
			{
				dbquery("
				INSERT INTO 
					fleet_ships 
				(
					fs_fleet_id,
					fs_ship_id,
					fs_ship_cnt
				) 
				VALUES 
				(
					".$this->id.",
					".$shipId.",
					".$count."
				);");
			}
			$this->shipIds[$shipId] = $this->shipIds[$shipId] + $count;
			return true;
		}
		
		/**
		* Remove all ships of the given ship id from the fleet
		*/
		function removeShips($shipId)
		{
			dbquery("
			DELETE FROM
				fleet_ships
			WHERE
				fs_ship_id=".$shipId."
				AND fs_fleet_id=".$this->id."
			;");		
			unset ($this->shipIds[$shipId]);
			unset ($this->ships[$shipId]);
			return (boolean)mysql_affected_rows();
		}
		

		/**
		* Land fleet
		*/
		function land()
		{
			if (($this->ownerId==0 || $this->ownerId==$this->getTarget()->ownerId()) && $this->getTarget()->ownerId() > 0)
			{
				$sl = new ShipList($this->targetId,$this->getTarget()->ownerId());

				foreach ($this->getShipIds() as $sid => $scnt)
				{
					$sl->add($sid,$scnt);
					$this->removeShips($sid);
				}
				
				// TODO: Perhaps all entities can get res in the future...
				if ($this->getTarget()->code == 'p')
				{
					$this->getTarget()->changeRes($this->resMetal,$this->resCrystal,$this->resPlasic,$this->resFuel,$this->resFood,$this->resPower);
					$this->getTarget()->chgPeople($this->pilots + $this->resPeople);

					// Add halve of the resources used for the engines to the target, 
					// if the action, for example, is colonize or position
					if ($this->status == 0)
					{
						$this->getTarget()->changeRes(0,0,0,$this->usageFuel/2,$this->usageFood/2,$this->usagePower/2);
					}
				}
				
				dbquery("
				DELETE FROM
					fleet
				WHERE
					id=".$_GET['fleetedit'].";");
				
				$this->valid = false;
				return true;
			}
			else
			{
				$this->error = "Kann Flotte nicht landen, Ziel ist unbewohnt oder Flottenbesitzer entspricht nicht Zielbesitzer.";
			}
			return false;
		}
        
        /**
         * Removes support fuel/food for
         * cancelled fleet
         */
        function removeSupportRes()
        {
			dbquery("
			UPDATE 
				fleet 
			SET 
                support_usage_fuel='0', 
                support_usage_food='0' 
			WHERE 
				id='".$this->id."';");
        }

		
		/**
		* Returns an error message (if setup)
		* or false
		*/
		function getError()
		{
			if (isset($this->error))
				return $this->error;
			return false;
		}
		
	}

?>