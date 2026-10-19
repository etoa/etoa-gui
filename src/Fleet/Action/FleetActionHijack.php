<?PHP

namespace EtoA\Fleet\Action;

use EtoA\Fleet\FleetAction;

class FleetActionHijack extends FleetAction
{

    public function __construct()
    {
        $this->code = "hijack";
        $this->name = "Schiff entführen";
        $this->desc = "Versucht, ein Schiff vom Ziel zu stehlen.";
        $this->longDesc = "Nähert sich unbemerkt dem Zielplaneten und versucht dort, ein Schiff zu stehlen.";
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
