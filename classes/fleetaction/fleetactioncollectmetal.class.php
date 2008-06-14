<?PHP

	class FleetActionCollectMetal extends FleetAction
	{

		function FleetActionCollectMetal()
		{
			$this->code = "collectmetal";
			$this->name = RES_METAL."erz sammeln";
			$this->desc = RES_METAL." von Asteroiden sammeln";
			
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