<?PHP

	class FleetActionFakeattack extends FleetAction
	{

        public function __construct()
		{
			$this->code = "fakeattack";
			$this->name = "T&auml;uschungsangriff";
			$this->desc = "T&auml;uscht einen Angriff auf das Ziel vor.";
			$this->longDesc = "Eine weitere taktische Aktion ist der T&auml;uschungsangriff. Mit dieser Option kann man den Gegner verwirren, indem man ihm eine Flotte aus Schiffen vorgaukelt, die gar nicht existiert. Die Anzahl und der Typ, der vorgegaukelten Schiffe, h&auml;ngen von der Anzahl Schiffe ab, die einen T&auml;uschungsangriff durchf&uuml;hren k&ouml;nnen.";
			$this->visible = true;
			$this->exclusive = false;
			$this->attitude = 3;

			$this->allowPlayerEntities = true;
			$this->allowActivePlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;
			$this->allianceAction = false;
		}

		function displayName() { return "Angriff"; }

		function startAction() {}
		function cancelAction() {}
		function targetAction() {}
		function returningAction() {}

	}

?>
