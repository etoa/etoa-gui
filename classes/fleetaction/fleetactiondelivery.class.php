<?PHP

	class FleetActionDelivery extends FleetAction
	{

		function FleetActionDelivery()
		{
			$this->code = "delivery";
			$this->name = "Allianzlieferung";
			$this->desc = "Liefert Allianzschiffe.";
			$this->longDesc = "Diese Aktion kann nur ausgefürt werden, um Allianzschiffe zum Spieler zu liefern.";
			$this->visible = true;
			$this->exclusive = false;				
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>