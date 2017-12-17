<?PHP

	class FleetActionFetch extends FleetAction
	{

        public function __construct()
		{
			$this->code = "fetch";
			$this->name = "Waren abholen";
			$this->desc = "Fliegt zum Ziel und holt dort Waren ab.";
			$this->longDesc = "Die Transportflotte fliegt zu einem eigenen Ziel und holt dort die aufgelisteten Waren ab, falls sie dort vorhanden sind. Diese Aktion kann nur f&uuml;r Flotten, die auch Transporter beinhalten, ausgew&auml;hlt werden.";
			$this->visible = true;
			$this->exclusive = false;
			$this->attitude = 1;

			$this->allowPlayerEntities = false;
			$this->allowActivePlayerEntities = false;
			$this->allowOwnEntities = true;
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
