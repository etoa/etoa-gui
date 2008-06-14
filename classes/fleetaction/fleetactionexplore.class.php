<?PHP

	class FleetActionExplore extends FleetAction
	{

		function FleetActionExplore()
		{
			$this->code = "explore";
			$this->name = "Erforschen";
			$this->desc = "Die unbekannten Weiten des Raumes erkunden";
			
			$this->attitude = 0;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = true;
			$this->allowSourceEntity = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>