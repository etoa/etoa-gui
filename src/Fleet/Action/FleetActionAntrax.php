<?PHP

namespace EtoA\Fleet\Action;

use EtoA\Fleet\FleetAction;

class FleetActionAntrax extends FleetAction
{

    public function __construct()
    {
        $this->code = "antrax";
        $this->name = "Antraxangriff";
        $this->desc = "Verübt einen Antraxangriff auf das Ziel, um Bewohner und Nahrung zu dezimieren.";
        $this->longDesc = "Diese Fähigkeit ermöglicht dem Angreifer bei Gelingen der Aktion Bewohner und Nahrung eines Planeten zu vernichten. Die Schadenshöhe wird in beiden Fällen zufällig entschieden. Taktisch sinnvoll, wenn man dem Gegner nach gewonnenem Kampf noch zusätzlich Schaden will. (Pro Stufe +5%)";
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
