<?PHP

	class FleetActionExplore extends FleetAction
	{

		function FleetActionExplore()
		{
			$this->code = "explore";
			$this->name = "Erkunden";
			$this->desc = "Die unbekannten Weiten des Raumes erkunden.";
			$this->longDesc = "Damit die Raumkarte sichtbar gemacht werden kann, muss der Raum zuerst erkundet werden.
			Dies geschieht durch Missionen mit Explorer-Sonden. Diese setzen am Ziel unbemannte kleine Drohnen aus, welche mit
			einer Nuklear-Energiezelle ausgestattet sind und damit viele Jahre lang halten. Die Drohnen sind so klein, dass sie nach dem 
			Aussetzen fast nicht mehr gros sichtbar sind; sie verf&uuml;gen auch nur über ein kleines Triebwerk zur Stabilisierung und zum Ausweichen vor 
			Raumm&uuml;ll. Jedoch senden sie konstant ein Signal zu deinem Hauptplaneten und berichten über die aktuelle Lage in ihrer Umgebung. 
			Die Explorer-Sonde deckt mit dieser Aktion jeweils die Ziel-Zelle und alle umliegenden Zellen auf. 
			Manchmal bringt die Expedition auch Souvenirs von ihrer Reise mit, manchmal schlägt sie aber auch fehl und kommt gar nicht mehr zur&uuml;ck.";
			$this->visible = false;
			$this->exclusive = false;			
			$this->attitude = 0;
			
			$this->allowPlayerEntities = false;
			$this->allowActivePlayerEntities = false;
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