<?PHP

	class FleetActionTransport extends FleetAction
	{

		function FleetActionTransport()
		{
			$this->code = "transport";
			$this->name = "Waren transportieren";
			$this->desc = "Bringt Waren zum Ziel und lädt sie dort ab.";
			
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>