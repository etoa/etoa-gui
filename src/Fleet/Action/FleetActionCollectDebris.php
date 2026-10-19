<?PHP

namespace EtoA\Fleet\Action;

use EtoA\Fleet\FleetAction;

class FleetActionCollectDebris extends FleetAction
{

    public function __construct()
    {
        $this->code = "collectdebris";
        $this->name = "Trümmer sammeln";
        $this->desc = ">Sammelt Überreste eines Kampfes, um daraus Rohstoffe  wiederzugewinnen.";
        $this->longDesc = "In Kämpfen, bei denen Schiffe oder Verteidigungsanlagen zerstört werden, entstehen zum Teil massive Trümmerfelder (TFs). Sammle diese mit speziell dafür vorgesehenen Schiffen ein und gewinne dadurch viele Ressourcen wieder. Beim Sammeln von Trümmerfeldern können normale Schiffe für mehr Laderaum mitgeschickt werden.";
        $this->visible = false;
        $this->exclusive = false;
        $this->attitude = 0;

        $this->allowPlayerEntities = true;
        $this->allowActivePlayerEntities = true;
        $this->allowOwnEntities = true;
        $this->allowNpcEntities = true;
        $this->allowSourceEntity = true;
        $this->allowAllianceEntities = true;
        $this->allianceAction = false;
    }

    function allowOnHoliday(): bool
    {
        return true;
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
