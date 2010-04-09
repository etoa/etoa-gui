<?PHP

	class FleetActionEmp extends FleetAction
	{

		function FleetActionEmp()
		{
			$this->code = "emp";
			$this->name = "EMP-Attacke";
			$this->desc = "Deaktiviert ein Geb&auml;ude des Ziels für eine gewisse Zeit.";
			$this->longDesc = "Diese Fähigkeit erm&ouml;glicht dem Angreifer bei Gelingen der Aktion ein Geb&auml;ude des Opfers nach Zufallsprinzip zu deaktivieren. Für eine bestimmte Zeit (ebenfalls zufallsm&auml;ssig) kann das Opfer dieses Geb&auml;ude nicht mehr aktiv nutzen!
			Die Chance ein Geb&auml;ude erfolgreich zu deaktivieren erh&ouml;ht sich, indem man die EMP-Technologie weiter erforscht! (Pro Stufe +5%)";
			$this->visible = true;
			$this->exclusive = false;		 				
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
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