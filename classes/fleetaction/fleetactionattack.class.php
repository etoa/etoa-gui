<?PHP

	class fleetActionAttack extends fleetAction
	{

		function fleetActionAttack()
		{
			$this->code = "attack";
			$this->name = "Angreifen";
			$this->isHostile = true;
			$this->isSelfOnly = false;
		}

		function targetAction() {} 
		function returningAction() {}		
		
	}

?>