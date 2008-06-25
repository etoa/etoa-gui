<?PHP

	class FleetActionCreateDebris extends FleetAction
	{

		function FleetActionCreateDebris()
		{
			$this->code = "createdebris";
			$this->name = "Trümmerfeld erstellen";
			$this->desc = "Erstellt ein Trümmerfeld (Flotte wird zerstört!)";
			$this->longDesc = "Mit dieser Aktion wird beim Gegner ein klitzekleines Trümmerfeld erstellt, damit den Navigationscomputern der Trümmerfeld-Sammler ein gültiges Ziel zugewiesen werden kann. 
			Die Flotte ist für den Gegner nicht sichtbar, wird beim Trümmerfeld-Erstellen aber zerstört!";
			$this->visible = false;
			$this->exclusive = true;					
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