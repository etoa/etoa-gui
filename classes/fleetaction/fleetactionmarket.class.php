<?PHP

	class FleetActionMarket extends FleetAction
	{

		function FleetActionMarket()
		{
			$this->code = "market";
			$this->name = "Marktlieferung";
			$this->desc = "Bringt Waren und Schiffe vom Markt.";
			$this->longDesc = "Diese Aktion kann nur vom neutralen Händler durchgeführt werden.";
			$this->visible = true;
			$this->exclusive = false;				
			$this->attitude = 1;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;

			$this->cancelable = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>