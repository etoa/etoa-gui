<?PHP

	class FleetActionSupport extends FleetAction
	{

		function FleetActionSupport()
		{
			$this->code = "support";
			$this->name = "Unterstützen";
			$this->desc = "Fliegt zum Ziel, um dort ein Allianzmitglied zu unterstützen.";
			$this->longDesc = "Die Flotte fliegt zu einem eigenen Ziel oder einem Ziel eines Allianzmitgliedes und die Schiffe verweilen dort im Orbit und stehen dem dortigen Planetenbesitzer im Kampfe bei. Du kannst die Dauer der Unterstützung selbst bestimmen und du kannst die Flotte auch jederzeit wieder zurückziehen.";
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