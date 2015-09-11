<?PHP

	class FleetActionSpyattack extends FleetAction
	{

		function FleetActionSpyattack()
		{
			$this->code = "spyattack";
			$this->name = "Spionageangriff";
			$this->desc = "Stiehlt eine Technologie vom Ziel.";
			$this->longDesc = "Mit der Option Spionageangriff hat man die M&ouml;glichkeit einem anderen Spieler eine spezielle Technologie (welche durch Zufallsprinzip bestimmt wird) abzuschauen. Bei Gelingen hat man sofort die gleiche Stufe der Technologie, wie sie der Spieler, dem ihr sie abgeschaut habt, hat. Es k&ouml;nnen nur Technologien abgeschaut werden die man selbst schon mindestens auf Stufe 1 erforscht hat. Die Chance f&uuml;r ein Gelingen ist relativ klein, kann aber durch Erforschen der Spionagetechnologie erh&ouml;ht werden. Die Chance wird durch mehreren Spionageschiffen in einer Flotte erh&ouml;ht.";
			$this->visible = true;
			$this->exclusive = false;		
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowActivePlayerEntities = true;
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