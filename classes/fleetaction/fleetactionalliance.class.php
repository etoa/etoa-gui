<?PHP

	class FleetActionAlliance extends FleetAction
	{

		function FleetActionAlliance()
		{
			$this->code = "alliance";
			$this->name = "Allianzangriff";
			$this->desc = "Greift das Ziel an und klaut Rohstoffe";
			$this->longDesc = "Der Allianz-Angriff auf ein bewohntes Ziel, ermöglicht es Allianzmitgliedern zusammen ein Ziel anzugreiffen. Falls der Kampf gewonnen wird, wird (meistens) die Hälfte der Rohstoffe geklaut.";
			$this->visible = true;
			$this->exclusive = false;
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
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