<?PHP

	class FleetActionColonize extends FleetAction
	{

		function FleetActionColonize()
		{
			$this->code = "colonize";
			$this->name = "Kolonialisieren";
			$this->desc = "Eine Basis auf dem Ziel errichten.";
			$this->longDesc = "Am Anfang jeder Spielerkarriere hat man einen Planeten zum Verwalten. Im ganzen Universum hat es jedoch noch unzählige andere Planeten, die unbewohnt sind. Um dies zu ändern gibt es spezielle Schiffe, welche diese freien Planeten besiedeln können.
Ein solches Schiff kann meist nicht grosse Mengen an Ressourcen mitnehmen, aber für diesen Zweck hat man die Möglichkeit andere Schiffe mitzuschicken.
Es ist zu beachten, dass man maximal 15 Planeten kontrollieren kann! Bei einer erfolgreichen Kolonialisierung wird das Besiedlungsschiff verbraucht.";
			$this->visible = true;
			$this->exclusive = false;					
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = true;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;
			$this->allianceAction = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>