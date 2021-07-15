<?PHP

class FleetActionPosition extends FleetAction
{

    public function __construct()
    {
        $this->code = "position";
        $this->name = "Stationieren";
        $this->desc = "Fliegt zum Ziel und stationiert sich dort.";
        $this->longDesc = "Die Flotte fliegt zu einem eigenen Ziel und die Schiffe landen dort. Bei erfolgreicher
			Durchf&uuml;hrung wird der unverbrauchte Treibstoff und die unverbrauchte Nahrung (also die H&auml;lfte) auf dem Zielplaneten ausgeladen und dort gespeichert.";
        $this->visible = true;
        $this->exclusive = false;
        $this->attitude = 1;

        $this->allowPlayerEntities = false;
        $this->allowActivePlayerEntities = true;
        $this->allowOwnEntities = true;
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
