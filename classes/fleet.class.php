<?PHP
	
	/**
	*	Handles data and actions for a fleet object
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/
	class Fleet
	{
		//
		// Variables
		//
		private $id,$valid, $ownerId;
		private $sourceId, $targetId;
		private $actionCode, $status;
		private $launchTime, $landTime;
		
		/**
		* Constructor
		*
		* Creates a new fleet objects and loads
		* it's data from the table
		*/
		function Fleet($fid,$uid=-1)
		{
			if ($uid>=0)
				$uidStr = " AND user_id=".$uid."";
			$this->valid = false;
			$res = dbquery("
			SELECT
				*
			FROM
				fleet
			WHERE
				id=".$fid."
				".$uidStr."
			");
			if (mysql_num_rows($res) > 0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->id = $fid;
				$this->ownerId = $arr['user_id'];
				$this->sourceId = $arr['entity_from'];
				$this->targetId = $arr['entity_to'];
				$this->actionCode = $arr['action'];
				$this->status = $arr['status'];
				$this->launchTime = $arr['launchtime'];
				$this->landTime = $arr['landtime'];
				$this->pilots = $arr['pilots'];

				$this->usageFuel = $arr['usage_fuel'];
				$this->usageFood = $arr['usage_food'];
				$this->usagePower = $arr['usage_power'];

				$this->resMetal = $arr['res_metal'];
				$this->resCrystal = $arr['res_crystal'];
				$this->resPlastic = $arr['res_plastic'];
				$this->resFuel = $arr['res_fuel'];
				$this->resFood = $arr['res_food'];
				$this->resPower = $arr['res_power'];
				$this->resPeople = $arr['res_people'];
			
				$this->valid = true;
			}		
		}
		
		//
		// Getters
		//
		function valid() { return $this->valid;	}
		function launchTime() {	return $this->launchTime; }
		function landTime() {	return $this->landTime;	}
		function pilots()	{	return $this->pilots;	}		
		function status() { return $this->status; }

		function usageFuel() { return $this->usageFuel; }
		function usageFood() { return $this->usageFood; }
		function usagePower() { return $this->usagePower; }

		function resMetal() { return $this->resMetal; }
		function resCrystal() { return $this->resCrystal; }
		function resPlastic() { return $this->resPlastic; }
		function resFuel() { return $this->resFuel; }
		function resFood() { return $this->resFood; }
		function resPower() { return $this->resPower; }
		function resPeople() { return $this->resPeople; }
		
		/**
		* Loads the source entity (if needed) and returns it
		*/		
		function getSource()
		{
			if (!isset($this->source))
			{
				$this->source = Entity::createFactoryById($this->sourceId);
			}
			return $this->source;
		}		

		/**
		* Loads the target entity (if needed) and returns it
		*/
		function getTarget()
		{
			if (!isset($this->target))
			{
				$this->target = Entity::createFactoryById($this->targetId);
			}
			return $this->target;
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
		private function loadShipIds()
		{
			$this->shipsIds = array();
			$this->shipCount = 0;
			$sres = dbquery("
			SELECT
				fs_ship_id,
        fs_ship_cnt
			FROM
      	fleet_ships
			WHERE
        fs_fleet_id='".$this->id."'
        AND fs_ship_cnt>'0'
        AND fs_ship_faked='0'
			;");
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
					$this->ships[$cnt] = new Ship($sid);
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
			foreach ($this->getShips() as $cnt => $sobj)
			{
				$this->capacity += $sobj->capacity() * $cnt;
			}
			return $this->capacity;
		}

		/**
		* Returns the full passenger capacity
		*/
		function getPeopleCapacity()
		{
			$this->peopleCapacity = 0;
			foreach ($this->getShips() as $cnt => $sobj)
			{
				$this->peopleCapacity += $sobj->peopleCapacity() * $cnt;
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
		function cancelFlight()
		{
			if ($this->stauts == 0)
			{
				if ($this->landTime() > time())
				{
					$difftime = time() - $this->launchTime;
					$this->launchTime = time();
					$this->landTime = $this->launchTime + $difftime ;
					
					$tmp = $this->targetId;
					$this->targetId = $this->sourceId;
					$this->sourceId = $tmp;
					
					$this->status = 2;
	
					$this->update();	
					return true;			
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
			if ($this->stauts == 0)
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
	
				$this->update();	
				return true;			
			}
			else
				$this->error = "Flotte ist bereits auf dem Rückflug!";
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
				status='".$this->status."'
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
				if ($trgEnt->code == 'p')
				{
					$trgEnt->changeRes($arr['res_metal'],$arr['res_crystal'],$arr['res_plastic'],$arr['res_fuel'],$arr['res_food'],$arr['res_power']);
					$trgEnt->chgPeople($arr['pilots']+$arr['res_people']);
				}
				
				// TODO: Add parts of usaged stuff (power cells, fuel, food)

				dbquery("
				DELETE FROM
					fleet
				WHERE
					id=".$_GET['fleetedit'].";");
				ok_msg("Flotte gelandet!");
				
				$this->valid = false;
			}
			else
			{
				err_msg("Kann Flotte nicht landen, Ziel ist unbewohnt oder Flottenbesitzer entspricht nicht Zielbesitzer.");
			}
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