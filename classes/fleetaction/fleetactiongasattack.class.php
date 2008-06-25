<?PHP

	class FleetActionGasAttack extends FleetAction
	{

		function FleetActionGasAttack()
		{
			$this->code = "gasattack";
			$this->name = "Gasangriff";
			$this->desc = "Greift das Ziel an und vernichtet Nahrung.";
			
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