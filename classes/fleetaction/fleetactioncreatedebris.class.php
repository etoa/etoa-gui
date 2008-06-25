<?PHP

	class FleetActionCreateDebris extends FleetAction
	{

		function FleetActionCreateDebris()
		{
			$this->code = "createdebris";
			$this->name = "Trümmerfeld erstellen";
			$this->desc = "Erstellt ein Trümmerfeld (Flotte wird zerstört!)";
			
			$this->attitude = 0;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = true;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>