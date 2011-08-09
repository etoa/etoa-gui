<?PHP

	class FleetActionGasAttack extends FleetAction
	{

		function FleetActionGasAttack()
		{
			$this->code = "gasattack";
			$this->name = "Gasangriff";
			$this->desc = "Greift das Ziel an und vernichtet Nahrung.";
			$this->longDesc = "Diese F&auml;higkeit erm&ouml;glicht dem Angreifer bei Gelingen der Aktion Nahrung eines Planeten zu vernichten. Die Schadensh&ouml;he wird zuf&auml;llig entschieden. Einsetzbar, wenn man dem Gegner nach gewonnenem Kampf noch die restliche Nahrung vernichten will.
Die Chance einen erfolgreichen Gasangriff durchzuf&uuml;hren erh&ouml;ht sich, indem man die Giftgas-Technologie weiter erforscht. (Pro Stufe +5%)";
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