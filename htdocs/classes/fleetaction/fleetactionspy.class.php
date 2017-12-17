<?PHP

	class FleetActionSpy extends FleetAction
	{

        public function __construct()
		{
			$this->code = "spy";
			$this->name = "Ausspionieren";
			$this->desc = "Sammelt Informationen &uuml;ber das Ziel.";
			$this->longDesc = "Du hast die M&ouml;glichkeit Planeten anderer Spieler auszuspionieren. Du erh&auml;lst Informationen &uuml;ber Geb&auml;ude, Forschung, Schiffe, Verteidigung und Ressourcen des Planeten.
			Hol dir diese Infos, um strategisch vorgehen zu k&ouml;nnen! Je nach H&ouml;he der eigenen und der gegnerischen Spionagetechnik kannst du unterschiedliche Informationen &uuml;ber das Ziel herausfinden.";
			$this->visible = true;
			$this->exclusive = false;
			$this->attitude = 2;

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
