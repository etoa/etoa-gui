<?PHP

	class FleetActionSupport extends FleetAction
	{

		function FleetActionSupport()
		{
			$this->code = "support";
			$this->name = "Unterstützen";
			$this->desc = "Fliegt zum Ziel, um dort ein Allianzmitglied zu unterstützen.";
			$this->longDesc = "Die Flotte fliegt zu einem eigenen Ziel und die Schiffe verweilen dort im Orbit und stehen dem dortigen Planetenbesitzer im Kampfe bei.";
			$this->visible = true;
			$this->exclusive = false;					
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = true;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>