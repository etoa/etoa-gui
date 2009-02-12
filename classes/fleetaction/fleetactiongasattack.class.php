<?PHP

	class FleetActionGasAttack extends FleetAction
	{

		function FleetActionGasAttack()
		{
			$this->code = "gasattack";
			$this->name = "Gasangriff";
			$this->desc = "Greift das Ziel an und vernichtet Nahrung.";
			$this->longDesc = "Diese Fähigkeit ermöglicht dem Angreifer bei Gelingen der Aktion Nahrung eines Planeten zu vernichten. Die Schadenhöhe wird zufällig entschieden. Einsetzbar, wenn man dem Gegner nach gewonnenem Kampf noch die restliche Nahrung vernichten will.
Die Chance einen erfolgreichen Gasangriff durchzuführen erhöht sich, indem man die Giftgas-Technologie weiter erforscht!";
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