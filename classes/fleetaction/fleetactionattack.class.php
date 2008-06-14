<?PHP

	class FleetActionAttack extends FleetAction
	{

		function FleetActionAttack()
		{
			$this->code = "attack";
			$this->name = "Angreifen";
			$this->desc = "Greift das Ziel an und klaut Rohstoffe";
			
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