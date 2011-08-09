<?PHP

	class FleetActionSupport extends FleetAction
	{

		function FleetActionSupport()
		{
			$this->code = "support";
			$this->name = "Unterstützen";
			$this->desc = "Fliegt zum Ziel, um dort ein Allianzmitglied zu unterst&uuml;tzen.";
			$this->longDesc = "Die Flotte fliegt zu einem eigenen Ziel oder einem Ziel eines Allianzmitgliedes und die Schiffe verweilen dort im Orbit und stehen dem dortigen Planetenbesitzer im Kampfe bei. Du kannst die Dauer der Unterst&uuml;tzung selbst bestimmen und du kannst die Flotte auch jederzeit wieder zur&uuml;ckziehen.";
			$this->visible = true;
			$this->exclusive = false;					
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
			$this->allowActivePlayerEntities = true;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = true;
			$this->allianceAction = true;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>