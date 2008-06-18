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
		

		
		
		
	}

?>