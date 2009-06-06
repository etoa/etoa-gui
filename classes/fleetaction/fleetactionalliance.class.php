<?PHP

	class FleetActionAlliance extends FleetAction
	{

		function FleetActionAlliance()
		{
			$this->code = "alliance";
			$this->name = "Allianzangriff";
			$this->desc = "Greift das Ziel an und raubt Rohstoffe.";
			$this->longDesc = "Wie beim normalen Angriff kannst du mit dem Allianzangriff einen fremden Planeten angreifen. Jedoch kannst du deine Allianzmitglieder einladen sich an dem Angriff zu beteiligen und somit mit einer grösseren Flotte angreifen. Aber es können nur Flotten am Angriff teilnehmen, welche spätestens zur gleichen Zeit beim Zielplaneten ankommen, wie die erste Flotte des Angriffs. Falls der Kampf gewonnen wird, wird (meistens) die Hälfte der Rohstoffe geraubt und brüderlich unter den einzelnen Angreifer aufgeteilt.";
			$this->visible = true;
			$this->exclusive = false;
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;
			$this->allianceAction = true;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>