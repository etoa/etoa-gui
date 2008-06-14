<?PHP

	class FleetActionAnalyze extends FleetAction
	{

		function FleetActionAnalyze()
		{
			$this->code = "analyze";
			$this->name = "Analysieren";
			$this->desc = "Das Ziel sondieren um vorhandene Rohstoffvorkommen festzustellen";
			
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