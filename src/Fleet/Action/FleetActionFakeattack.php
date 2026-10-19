<?PHP

namespace EtoA\Fleet\Action;

use EtoA\Fleet\FleetAction;

class FleetActionFakeattack extends FleetAction
{

    public function __construct()
    {
        $this->code = "fakeattack";
        $this->name = "Täuschungsangriff";
        $this->desc = "Täuscht einen Angriff auf das Ziel vor.";
        $this->longDesc = "Eine weitere taktische Aktion ist der Täuschungsangriff. Mit dieser Option kann man den Gegner verwirren, indem man ihm eine Flotte aus Schiffen vorgaukelt, die gar nicht existiert. Die Anzahl und der Typ, der vorgegaukelten Schiffe, hängen von der Anzahl Schiffe ab, die einen Täuschungsangriff durchführen können.";
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

    function displayName(): string
    {
        return "Angriff";
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
