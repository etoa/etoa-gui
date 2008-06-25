<?PHP

	class FleetActionMarket extends FleetAction
	{

		function FleetActionMarket()
		{
			$this->code = "market";
			$this->name = "Marktlieferung";
			$this->desc = "Bringt Waren und Schiffe vom Markt.";
			
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
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