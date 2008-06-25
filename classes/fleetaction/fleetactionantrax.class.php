<?PHP

	class FleetActionAntrax extends FleetAction
	{

		function FleetActionAntrax()
		{
			$this->code = "antrax";
			$this->name = "Antrax-Angriff";
			$this->desc = "Verübt einen Antrax-Angriff auf das Ziel um Bewohner und Nahrung zu dezimieren.";
			$this->longDesc = "Diese Fähigkeit ermöglicht dem Angreiffer bei Gelingen der Aktion, Bewohner und Nahrung eines Planeten zu vernichten. Die Schadenshöhe wird in beiden Fällen zufällig entschieden. Taktisch sinnvoll, wenn man dem Gegner nach gewonnenem Kampf noch zusätzlich Schaden will!";
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