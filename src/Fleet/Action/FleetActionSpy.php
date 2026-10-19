<?PHP

namespace EtoA\Fleet\Action;

use EtoA\Fleet\FleetAction;

class FleetActionSpy extends FleetAction
{

    public function __construct()
    {
        $this->code = "spy";
        $this->name = "Ausspionieren";
        $this->desc = "Sammelt Informationen über das Ziel.";
        $this->longDesc = "Du hast die Möglichkeit Planeten anderer Spieler auszuspionieren. Du erhälst Informationen über Gebäude, Forschung, Schiffe, Verteidigung und Ressourcen des Planeten.
			Hol dir diese Infos, um strategisch vorgehen zu können! Je nach Höhe der eigenen und der gegnerischen Spionagetechnik kannst du unterschiedliche Informationen über das Ziel herausfinden.";
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

    function startAction()
    {
    }

    function cancelAction()
    {
    }

    function targetAction()
    {
    }

    function returningAction()
    {
    }

    public function getAttitudeString()
    {
        return parent::$attitudeString[$this->attitude];
    }

    public function getAttitudeColor()
    {
        return parent::$attitudeColor[$this->attitude];
    }
}
