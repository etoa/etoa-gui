<?PHP

use EtoA\Core\ObjectWithImage;

/**
 * Class for nebula entity
 */
class Nebula extends Entity
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
        return array("collectcrystal", "analyze", "flight", "explore");
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
        return "Interstellarer Gasnebel";
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
        $numImages = 9;
        $r = ($this->id % $numImages) + 1;
        return ObjectWithImage::BASE_PATH . "/nebulas/nebula" . $r . "_small.png";
    }

    /**
     * Returns type
     */
    function entityCode()
    {
        return "n";
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
