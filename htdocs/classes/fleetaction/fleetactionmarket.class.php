<?PHP

class FleetActionMarket extends FleetAction
{

    public function __construct()
    {
        $this->code = "market";
        $this->name = "Marktlieferung";
        $this->desc = "Bringt Waren und Schiffe vom Markt.";
        $this->longDesc = "Diese Aktion kann nur vom neutralen H&auml;ndler durchgef&uuml;hrt werden.";
        $this->visible = true;
        $this->exclusive = false;
        $this->attitude = 1;

        $this->allowPlayerEntities = true;
        $this->allowActivePlayerEntities = true;
        $this->allowOwnEntities = false;
        $this->allowNpcEntities = false;
        $this->allowSourceEntity = false;
        $this->allowAllianceEntities = false;
        $this->allianceAction = false;

        $this->visibleSource = false;
        $this->sourceCode = "m";
        $this->cancelable = false;
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
