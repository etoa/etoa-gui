<?PHP

	class FleetActionCollectDebris extends FleetAction
	{

		function FleetActionCollectDebris()
		{
			$this->code = "collectdebris";
			$this->name = "Trümmer sammeln";
			$this->desc = "Überreste eines Kampfes sammeln um Rohstoffe daraus zu gewninnen";
			$this->longDesc = "Bei Kämpfen wo Schiffe/Verteidigungen zerstört werden, entstehen zum Teils massive Trümmerfelder (TF`s)
			Sammle sie mit speziell dafür vorgesehenen Schiffen ein und gewinne dadurch viele Ressourcen wieder.
			Beim Sammeln von Trümmerfeldern können normale Schiffe mitgeschickt werden.";
			$this->visible = false;
			$this->exclusive = false;						
			$this->attitude = 0;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = true;
			$this->allowSourceEntity = true;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>