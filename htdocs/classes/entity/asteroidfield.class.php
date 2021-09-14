<?PHP

/**
 * Class for asteroidfield entity
 */
class AsteroidField extends Entity
{
    protected $id;
    protected $coordsLoaded;
    protected $isValid;
    public $pos;
    public $sx;
    public $sy;
    public $cx;
    public $cy;
    protected $cellId;
    private $name;

    /**
     * The constructor
     */
    public function __construct($id = 0)
    {
        $this->isValid = true;
        $this->id = intval($id);
        $this->pos = 0;
        $this->name = "";
        $this->coordsLoaded = false;
        $this->isVisible = true;
    }

    public function allowedFleetActions()
    {
        return array("collectmetal", "analyze", "flight", "explore");
    }


    /**
     * Returns id
     */
    function id()
    {
        return $this->id;
    }

    /**
     * Returns id
     */
    function name()
    {
        return $this->name;
    }


    /**
     * Returns owner
     */
    function owner()
    {
        return "Niemand";
    }

    /**
     * Returns owner
     */
    function ownerId()
    {
        return 0;
    }

    function ownerMain()
    {
        return false;
    }

    /**
     * Returns type string
     */
    function entityCodeString()
    {
        return "Asteroidenfeld";
    }


    /**
     * Returns type
     */
    function type()
    {
        return "";
    }

    function imagePath($opt = "")
    {
        $numImages = 5;
        $r = ($this->id % $numImages) + 1;
        return IMAGE_PATH . "/asteroids/asteroids" . $r . "_small.png";
    }

    /**
     * Returns type
     */
    function entityCode()
    {
        return "a";
    }

    /**
     * To-String function
     */
    function __toString()
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }
        return $this->formatedCoords();
    }

    /**
     * Returns the cell id
     */
    function cellId()
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }
        return $this->cellId;
    }
    public function getFleetTargetForwarder()
    {
        return null;
    }
}
