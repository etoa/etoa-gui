<?PHP

	class FleetActionEmp extends FleetAction
	{

		function FleetActionEmp()
		{
			$this->code = "emp";
			$this->name = "EMP-Attacke";
			$this->desc = "Deaktiviert ein Gebäude des Ziels für eine gewisse Zeit.";
			$this->longDesc = "Diese Fähigkeit ermöglicht dem Angreiffer bei Gelingen der Aktion, ein Gebäude des Opfers nach Zufallsprinzip zu deaktivieren. Für eine bestimmte Zeit (ebenfalls zufallsmässig) kann das Opfer dieses Gebäude nicht mehr aktiv nutzen!
			Die Chance ein Gebäude erfolgreich zu deaktivieren erhöht sich, in dem man EMP-Technologie weiter erforscht! (Pro Stufe +5%)";
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