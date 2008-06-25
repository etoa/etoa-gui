<?PHP

	class FleetActionEmp extends FleetAction
	{

		function FleetActionEmp()
		{
			$this->code = "emp";
			$this->name = "EMP-Attacke";
			$this->desc = "Deaktiviert ein Gebäude des Ziels für eine gewisse Zeit.";
			
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