<?PHP

	class FleetActionFakeattack extends FleetAction
	{

		function FleetActionFakeattack()
		{
			$this->code = "fakeattack";
			$this->name = "Täuschungsangriff";
			$this->desc = "Täuscht einen Angriff auf das Ziel an";
			$this->longDesc = "Eine weitere taktische Aktion ist der Fakeangriff. Mit dieser Option kann man den Gegner verwirren, indem man ihm eine Flotte aus Schiffen vorgaukelt die gar nicht existiert! Die imaginären Schiffstypenauswahl hängt von der Anzahl Schiffe ab die einen Fakeangriff durchführen können.";
			$this->visible = true;
			$this->exclusive = false;						
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
		}

		function displayName() { return "Angriff"; }

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>