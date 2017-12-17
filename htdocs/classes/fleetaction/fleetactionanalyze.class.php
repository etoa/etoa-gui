<?PHP

	class FleetActionAnalyze extends FleetAction
	{

        public function __construct()
		{
			$this->code = "analyze";
			$this->name = "Analysieren";
			$this->desc = "Das Ziel sondieren um vorhandene Rohstoffvorkommen festzustellen.";
			$this->longDesc = "Analysiert Asteroidenfelder, interstellare Nebel und Gasplaneten um festzustellen wie viele Ressourcen sich abbauen lassen.";
			$this->visible = false;
			$this->exclusive = false;
			$this->attitude = 0;

			$this->allowPlayerEntities = false;
			$this->allowActivePlayerEntities = false;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = true;
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
