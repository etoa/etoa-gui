<?PHP

	class FleetActionBombard extends FleetAction
	{

		function FleetActionBombard()
		{
			$this->code = "bombard";
			$this->name = "Bombardieren";
			$this->desc = "Bombardiert das Ziel um ein Gebäude zu beschädigen.";
			$this->longDesc = "Bombardieren, eine der grausamsten Waffen in diesem Universum.
			Bei erfolgreicher Aktion, wird dem Gegner ein Gebäude um ein Level gesenkt. Das Gebäude wird durch Zufall ausgewählt.
			Um die Chance auf eine erfolgreiche Bombardierung zu erhöhen erforsche die Bombentechnik (Pro Stufe +5%)";
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