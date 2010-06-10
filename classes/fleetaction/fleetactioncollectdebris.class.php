<?PHP

	class FleetActionCollectDebris extends FleetAction
	{

		function FleetActionCollectDebris()
		{
			$this->code = "collectdebris";
			$this->name = "Trümmer sammeln";
			$this->desc = "&Uuml;berreste eines Kampfes sammeln, um Rohstoffe daraus zu wiederzugewinnen..";
			$this->longDesc = "In K&auml;mpfen, bei denen Schiffe oder Verteidigungsanlagen zerst&ouml;rt werden, entstehen zum Teil massive Tr&uuml;mmerfelder (TFs). Sammle diese mit speziell daf&uuml;r vorgesehenen Schiffen ein und gewinne dadurch viele Ressourcen wieder. Beim Sammeln von Tr&uuml;mmerfeldern k&ouml;nnen normale Schiffe f&uuml;r mehr Laderaum mitgeschickt werden.";
			$this->visible = false;
			$this->exclusive = false;						
			$this->attitude = 0;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = true;
			$this->allowSourceEntity = true;
			$this->allowAllianceEntities = true;
			$this->allianceAction = false;
			
		}

		function allowOnHoliday() { return true; }


		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>