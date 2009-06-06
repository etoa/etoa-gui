<?PHP

	class FleetActionCollectDebris extends FleetAction
	{

		function FleetActionCollectDebris()
		{
			$this->code = "collectdebris";
			$this->name = "Trümmer sammeln";
			$this->desc = "Überreste eines Kampfes sammeln, um Rohstoffe daraus zu gewninnen.";
			$this->longDesc = "In Kämpfen, bei denen Schiffe/Verteidigungen zerstört werden, entstehen zum Teil massive Trümmerfelder (TFs).
			Sammle diese mit speziell dafür vorgesehenen Schiffen ein und gewinne dadurch viele Ressourcen wieder.
			Beim Sammeln von Trümmerfeldern können normale Schiffe mitgeschickt werden.";
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