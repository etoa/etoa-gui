<?PHP

namespace EtoA\Fleet\Action;

use EtoA\Fleet\FleetAction;

class FleetActionBombard extends FleetAction
{

    public function __construct()
    {
        $this->code = "bombard";
        $this->name = "Bombardierung";
        $this->desc = "Bombardiert das Ziel, um ein Gebäude zu beschädigen.";
        $this->longDesc = "Bombardieren ist eine der grausamsten Waffen des Universums.
			Bei erfolgreicher Aktion wird dem Gegner ein Gebäude um ein Level gesenkt. Das Gebäude wird durch Zufall ausgewählt.
			Um die Chance auf eine erfolgreiche Bombardierung zu erhöhen erforsche die Bombentechnik (Pro Stufe +5%)";
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
