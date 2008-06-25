<?PHP

	class FleetActionSpyattack extends FleetAction
	{

		function FleetActionSpyattack()
		{
			$this->code = "spyattack";
			$this->name = "Spionageangriff";
			$this->desc = "Stiehlt eine Technologie vom Ziel.";
			$this->longDesc = "Mit der Option Spionagenagriff hat man die Möglichkeit einem anderen User eine spezielle Technologie (welche durch Zufallsprinzip bestimmt wird) abzuschauen.
Bei Gelingen hat man sofort die gleiche Stufe der Technologie, wie sie der Spieler, dem ihr sie abgeschaut habt, hat.
Die Chance für ein Gelingen ist relativ klein, kann aber durch Erforschen der Spionagetechnologie erhöht werden.";
			$this->visible = true;
			$this->exclusive = false;		
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>