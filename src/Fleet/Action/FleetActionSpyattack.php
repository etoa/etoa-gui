<?PHP

namespace EtoA\Fleet\Action;

use EtoA\Fleet\FleetAction;

class FleetActionSpyattack extends FleetAction
{

    public function __construct()
    {
        $this->code = "spyattack";
        $this->name = "Spionageangriff";
        $this->desc = "Stiehlt eine Technologie vom Ziel.";
        $this->longDesc = "Mit der Option Spionageangriff hat man die Möglichkeit einem anderen Spieler eine spezielle Technologie (welche durch Zufallsprinzip bestimmt wird) abzuschauen. Bei Gelingen hat man sofort die gleiche Stufe der Technologie, wie sie der Spieler, dem ihr sie abgeschaut habt, hat. Es können nur Technologien abgeschaut werden die man selbst schon mindestens auf Stufe 1 erforscht hat. Die Chance für ein Gelingen ist relativ klein, kann aber durch Erforschen der Spionagetechnologie erhöht werden. Die Chance wird durch mehreren Spionageschiffen in einer Flotte erhöht.";
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
