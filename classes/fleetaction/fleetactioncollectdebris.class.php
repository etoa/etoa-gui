<?PHP

	class FleetActionCollectDebris extends FleetAction
	{

		function FleetActionCollectDebris()
		{
			$this->code = "collectdebris";
			$this->name = "Trümmer sammeln";
			$this->desc = "Überreste eines Kampfes sammeln um Rohstoffe daraus zu gewninnen";
			
			$this->attitude = 0;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = true;
			$this->allowSourceEntity = true;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>