<?PHP

	class FleetActionCollectCrystal extends FleetAction
	{

		function FleetActionCollectCrystal()
		{
			$this->code = "collectcrystal";
			$this->name = RES_CRYSTAL."staub sammeln";
			$this->desc = RES_CRYSTAL." von Sternennebeln sammeln";
			
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