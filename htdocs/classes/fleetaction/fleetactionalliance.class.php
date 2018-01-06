<?PHP

	class FleetActionAlliance extends FleetAction
	{

        public function __construct()
		{
			$this->code = "alliance";
			$this->name = "Allianzangriff";
			$this->desc = "Greift das Ziel an und raubt Rohstoffe.";
			$this->longDesc = "Wie beim normalen Angriff kannst du mit dem Allianzangriff einen fremden Planeten angreifen. Jedoch kannst du deine Allianzmitglieder einladen sich an dem Angriff zu beteiligen und somit mit einer gr&ouml;sseren Flotte angreifen. Aber es k&ouml;nnen nur Flotten am Angriff teilnehmen, welche sp&auml;testens zur gleichen Zeit beim Zielplaneten ankommen, wie die Flotte des Spielers der den Allianzangriff einleitet. Falls der Kampf gewonnen wird, wird (meistens) die H&auml;lfte der Rohstoffe geraubt und br&uuml;derlich unter den einzelnen Angreifer aufgeteilt.";
			$this->visible = true;
			$this->exclusive = false;
			$this->attitude = 3;

			$this->allowPlayerEntities = true;
			$this->allowActivePlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
			$this->allowAllianceEntities = false;
			$this->allianceAction = true;
		}

		function startAction() {}
		function cancelAction() {}
		function targetAction() {}
		function returningAction() {}

	}

?>
