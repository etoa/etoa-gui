<?PHP

	class FleetActionCreateDebris extends FleetAction
	{

        public function __construct()
		{
			$this->code = "createdebris";
			$this->name = "Trümmerfeld erstellen";
			$this->desc = "Erstellt ein Tr&uuml;mmerfeld (Flotte wird zerst&ouml;rt!).";
			$this->longDesc = "Mit dieser Aktion wird beim Gegner ein Tr&uuml;mmerfeld erstellt, damit den Navigationscomputern der Tr&uuml;mmerfeld-Sammler ein gültiges Ziel zugewiesen werden kann. 
			Die Flotte ist für den Gegner nicht sichtbar, wird beim Tr&uuml;mmerfeld-Erstellen aber vollst&auml;ndig zerst&ouml;rt!";
			$this->visible = false;
			$this->exclusive = true;
			$this->attitude = 0;

			$this->allowPlayerEntities = true;
			$this->allowActivePlayerEntities = true;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = true;
			$this->allowAllianceEntities = true;
			$this->allianceAction = false;
		}

		function startAction() {}
		function cancelAction() {}
		function targetAction() {}
		function returningAction() {}

	}

?>
