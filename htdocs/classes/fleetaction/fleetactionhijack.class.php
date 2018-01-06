<?PHP

	class FleetActionHijack extends FleetAction
	{

        public function __construct()
		{
			$this->code = "hijack";
			$this->name = "Schiff entf&uuml;hren";
			$this->desc = "Versucht, ein Schiff vom Ziel zu stehlen.";
			$this->longDesc = "N&auml;hert sich unbemerkt dem Zielplaneten und versucht dort, ein Schiff zu stehlen.";
			$this->visible = false;
			$this->exclusive = true;
			$this->attitude = 3;

			$this->allowPlayerEntities = true;
			$this->allowActivePlayerEntities = true;
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
