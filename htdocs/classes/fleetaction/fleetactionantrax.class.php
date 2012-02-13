<?PHP

	class FleetActionAntrax extends FleetAction
	{

		function FleetActionAntrax()
		{
			$this->code = "antrax";
			$this->name = "Antraxangriff";
			$this->desc = "Ver&uuml;bt einen Antraxangriff auf das Ziel, um Bewohner und Nahrung zu dezimieren.";
			$this->longDesc = "Diese F&auml;higkeit erm&ouml;glicht dem Angreifer bei Gelingen der Aktion Bewohner und Nahrung eines Planeten zu vernichten. Die Schadensh&ouml;he wird in beiden F&auml;llen zufällig entschieden. Taktisch sinnvoll, wenn man dem Gegner nach gewonnenem Kampf noch zus&auml;tzlich Schaden will. (Pro Stuffe +5%)";
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