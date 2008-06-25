<?PHP

	class FleetActionFakeattack extends FleetAction
	{

		function FleetActionFakeattack()
		{
			$this->code = "fakeattack";
			$this->name = "Täuschungsangriff";
			$this->desc = "Täuscht einen Angriff auf das Ziel an";
			
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
		}

		function displayName() { return "Angriff"; }

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>