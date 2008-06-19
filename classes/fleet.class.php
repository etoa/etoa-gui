<?PHP

	class Fleet
	{
		private $id,$valid;
		private $sourceId, $targetId;
		private $actionCode, $status;
		private $launchTime, $landTime;
		
		
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
		
		function getSource()
		{
			if (!isset($this->source))
			{
				$this->source = Entity::createFactoryById($this->sourceId);
			}
			return $this->source;
		}		

		function getTarget()
		{
			if (!isset($this->target))
			{
				$this->target = Entity::createFactoryById($this->targetId);
			}
			return $this->target;
		}
		
		function getAction()
		{
			if (!isset($this->action))
			{
				$this->action = FleetAction::createFactory($this->actionCode);
			}
			return $this->action;
		}
		
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
		
		function countShips()
		{
			if (!isset($this->shipsIds))
			{
				$this->loadShipIds();
			}
			return $this->shipCount;			
		}
		
		function getShipIds()
		{
			if (!isset($this->shipsIds))
			{
				$this->loadShipIds();
			}
			return $this->shipsIds;			
		}
		
		private function loadShips()
		{
			$this->ships = array();
			foreach ($this->getShipIds() as $sid=>$cnt)
			{
				$this->ships[$cnt] = new Ship($sid);
			}			
		}
		
		function getShips()
		{
			if (!isset($this->ships))
			{
				$this->loadShips();
			}
			return $this->ships;			
		}	
		
		function getCapacity()
		{
			$this->capacity = 0;
			foreach ($this->getShips() as $cnt => $sobj)
			{
				$this->capacity += $sobj->capacity() * $cnt;
			}
			return $this->capacity;
		}

		function getPeopleCapacity()
		{
			$this->peopleCapacity = 0;
			foreach ($this->getShips() as $cnt => $sobj)
			{
				$this->peopleCapacity += $sobj->peopleCapacity() * $cnt;
			}
			return $this->peopleCapacity;
		}

		function getFreePeopleCapacity()
		{
			return $this->getPeopleCapacity() - $this->resPeople;
		}
		
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

		function returnFlight()
		{
			if ($this->stauts == 0)
			{
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
		
		function getError()
		{
			if (isset($this->error))
				return $this->error;
			return false;
		}
		
	}

?>