<?PHP

	class FleetActionBombard extends FleetAction
	{

		function FleetActionBombard()
		{
			$this->code = "bombard";
			$this->name = "Bombardierung";
			$this->desc = "Bombardiert das Ziel, um ein Geb&auml;ude zu beschädigen.";
			$this->longDesc = "Bombardieren ist eine der grausamsten Waffen des Universums.
			Bei erfolgreicher Aktion wird dem Gegner ein Geb&auml;ude um ein Level gesenkt. Das Geb&auml;ude wird durch Zufall ausgew&auml;hlt.
			Um die Chance auf eine erfolgreiche Bombardierung zu erh&ouml;hen erforsche die Bombentechnik (Pro Stufe +5%)";
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