<?PHP

	class FleetActionPosition extends FleetAction
	{

		function FleetActionPosition()
		{
			$this->code = "position";
			$this->name = "Stationieren";
			$this->desc = "Fliegt zum Ziel und stationiert sich dort.";
			$this->longDesc = "Die Flotte fliegt zu einem eigenen Ziel und die Schiffe landen dort. Bei erfolgreicher
			Durchführung wird der unverbrauchte Treibstoff (also die Hälfte) auf dem Zielplaneten ausgeladen und dort gespeichert.";
			$this->visible = true;
			$this->exclusive = false;					
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = true;
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