<?PHP

	class FleetActionSpy extends FleetAction
	{

		function FleetActionSpy()
		{
			$this->code = "spy";
			$this->name = "Ausspionieren";
			$this->desc = "Sammelt Informationen über das Ziel.";
			$this->longDesc = "Du hast die Möglichkeit Planeten anderer Spieler auszuspionieren. Du erhälst Informationen über Gebäude, Forschung, Schiffe, Verteidigung und Ressourcen des Planeten.
			Hol dir diese Infos, um strategisch vorgehen zu können! Je nach Höhe der eigenen und der gegnerischen Spionagetechnik kannst du unterschiedliche Informationen über das Ziel herausfinden.";
			$this->visible = true;
			$this->exclusive = false;		
			$this->attitude = 2;
			
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