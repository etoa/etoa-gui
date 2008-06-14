<?PHP

	class Fleet
	{
		public $sourceEntity;
		public $targetEntity;
		var $ownerId;

		var $ships;
		var $shipCount;	
		var $shipsFixed;

		var $speed;
		var $speedPercent;
		var $duration;
		var $action;
		var $costsPerHundredAE;
		var $timeLaunchLand;
		var $costsLaunchLand;
		var $pilots;
		var $capacityTotal;
		var $capacityResUses;
		var $capacityFuelUsed;
		var $capacityPeopleTotal;
		var $capacityPeopleUsed;
		
		var $distance;
		
		function Fleet($args="")
		{
			if ($args!="")
			{
				
				
			}
			else
			{
				$this->ships = array();
				$this->speedPercent=100;
				$this->speed = 0;
				$this->duration=0;
				$this->action='';
				$this->costsPerHundredAE=0;
				$this->timeLaunchLand=0;
				$this->costsLaunchLand=0;
				$this->pilots=0;
				$this->capacityTotal=0;
				$this->capacityResUsed=0;
				$this->capacityFuelUsed=0;
				$this->capacityPeopleTotal=0;
				$this->capacityPeopleUsed=0;		
				$this->shipCount=0;
				$this->distance=0;
				$this->shipsFixed=false;
			}
		}
		
		function addShip($sid,$cnt)
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
					AND shiplist_user_id='".$this->ownerId."'
					AND shiplist_planet_id='".$this->sourceEntity->id()."'
	        AND ship_id=".$sid."
	        AND shiplist_count>0;");
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_array($res);	
					$cnt = min($cnt,$arr['shiplist_count']);
					
					$this->ships[$sid] = array(
					"count" => $cnt,
					"speed" => $arr['ship_speed'],
					"fuel_use" => $arr['ship_fuel_use'] * $cnt,
					"name" => $arr['ship_name'],
					"pilots" => $arr['ship_pilots'] * $cnt,
					);
					
					// Set global speed
					if ($this->speed <= 0)
					{
						$this->speed = $arr['ship_speed'];
					}
					else
					{
						$this->speed = min($this->speed, $arr['ship_speed']);
					}													     
					
					$this->timeLaunchLand = max($this->timeLaunchLand, $arr['ship_time2land'] + $arr['ship_time2start']);
					$this->costsLaunchLand += 2 * ($arr['ship_fuel_use_launch'] + $arr['ship_fuel_use_landing']) * $cnt;						
					$this->pilots += $arr['ship_pilots'] * $cnt;
					$this->capacityTotal += $arr['ship_capacity'] * $cnt;
					$this->capacityPeopleTotal += $arr['ship_people_capacity'] * $cnt;
					$this->shipCount += $cnt;
				}		
			}	
		}
		
		function fixShips()
		{	
			if (!$this->shipsFixed)
			{
				$this->shipsFixed=true;
				// Calc Costs for all ships, based on regulated speed
				foreach ($this->ships as $sid => $sd)
				{
					$cpae = $sd['fuel_use'] * $this->speed / $sd['speed'];
					$this->ships[$sid]['costs_per_ae'] = $cpae;
					$this->costsPerHundredAE += $cpae;				
				}
			}
		}
		
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
			$this->capacityResUsed=0;
			$this->capacityFuelUsed=0;
			$this->capacityPeopleTotal=0;
			$this->capacityPeopleUsed=0;		
			$this->shipCount=0;
			$this->distance=0;
			$this->shipsFixed=false;
		}		
		
		function setSource($ent)
		{
			$this->sourceEntity=$ent;
		}

		function setTarget($ent)
		{
			$this->targetEntity=$ent;
			$this->distance = $this->sourceEntity->distance($this->targetEntity);			
		}		
		
		function getDistance()
		{
			return $this->distance;
		}
		
		function setOwnerId($id)
		{
			$this->ownerId=$id;
		}
		
		function getShipCount()
		{
			return $this->shipCount;
		}
		
		function getPilots()
		{
			return $this->pilots;	
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
			return $this->costs;
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
			$this->duration = $this->distance / $this->getSpeed();	// Calculate duration
			$this->duration *= 3600;	// Convert to seconds
			$this->duration += $this->timeLaunchLand;	// Add launch and land time
			$this->duration = ceil($this->duration);
			$this->speedPercent = min(100,$perc);
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
		
		
	}

?>