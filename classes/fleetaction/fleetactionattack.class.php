<?PHP

	class FleetActionAttack extends FleetAction
	{

		function FleetActionAttack()
		{
			$this->code = "attack";
			$this->name = "Angriff";
			$this->desc = "Greift das Ziel an und raubt dort Rohstoffe.";
			$this->longDesc = "Der Standard-Angriff auf ein bewohntes Ziel. Falls der Kampf gewonnen wird, wird (meistens) die H&auml;lfte der auf dem Planeten befindlichen Rohstoffe geraubt.";
			$this->visible = true;
			$this->exclusive = false;
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;
			$this->allianceAction = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>