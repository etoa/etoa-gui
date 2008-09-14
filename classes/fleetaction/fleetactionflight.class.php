<?PHP

	class FleetActionFlight extends FleetAction
	{

		function FleetActionFlight()
		{
			$this->code = "flight";
			$this->name = "Flug";
			$this->desc = "Fliegt zum Ziel, kehrt dort sofort um und fliegt wieder zurück.";
			$this->longDesc = "Eine Standard-Aktion, welche aber am Ziel nichts macht. Dies kann gut zum Sichern der Flotte (saven) verwendet werden, da die Flotte am Ziel sofort umkehrt und nicht angegriffen werden kann.";
			$this->visible = true;
			$this->exclusive = false;							
			$this->attitude = 0;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = true;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = true;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>