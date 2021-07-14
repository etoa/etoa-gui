<?PHP

class FleetActionStealthattack extends FleetAction
{

    public function __construct()
    {
        $this->code = "stealthattack";
        $this->name = "Tarnangriff";
        $this->desc = "Greift das Ziel getarnt an und raubt Rohstoffe.";
        $this->longDesc = "Eine taktisch extrem effektive Methode ist der Tarnangriff. Mit dieser Option ist man in der Lage den Gegner anzugreifen und den ganzen Flug unentdeckt zu bleiben.
Bedingt jedoch, dass keine anderen Schiffe mitfliegen. Bis heute gibt es noch keine M&ouml;glichkeit diese Schiffe ausfindig zu machen. Wenn man sie bemerkt, ist es immer schon zu spät.";
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
}
