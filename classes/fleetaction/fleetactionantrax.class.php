<?PHP

	class FleetActionAntrax extends FleetAction
	{

		function FleetActionAntrax()
		{
			$this->code = "antrax";
			$this->name = "Antraxangriff";
			$this->desc = "Verübt einen Antraxangriff auf das Ziel, um Bewohner und Nahrung zu dezimieren.";
			$this->longDesc = "Diese Fähigkeit ermöglicht dem Angreifer bei Gelingen der Aktion Bewohner und Nahrung eines Planeten zu vernichten. Die Schadenhöhe wird in beiden Fällen zufällig entschieden. Taktisch sinnvoll, wenn man dem Gegner nach gewonnenem Kampf noch zusätzlich Schaden will!";
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