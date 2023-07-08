<?PHP

namespace EtoA\Fleet\Action;

use EtoA\Fleet\FleetAction;

class FleetActionAttack extends FleetAction
{

    public function __construct()
    {
        $this->code = "attack";
        $this->name = "Angriff";
        $this->desc = "Greift das Ziel an und raubt dort Rohstoffe.";
        $this->longDesc = "Der Standard-Angriff auf ein bewohntes Ziel. Falls der Kampf gewonnen wird, wird (meistens) die H&auml;lfte der auf dem Planeten befindlichen Rohstoffe geraubt.";
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
}
