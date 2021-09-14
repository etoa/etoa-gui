<?PHP

use EtoA\Core\ObjectWithImage;

/**
 * Class for empty space entities
 */
class UnExplored extends Entity
{
    private $name;
    protected $id;
    protected $coordsLoaded;
    protected $isValid;
    public $pos;
    public $sx;
    public $sy;
    public $cx;
    public $cy;
    protected $cellId;

    /**
     * The constructor
     */
    public function __construct($id = 0)
    {
        $this->isValid = true;
        $this->id = intval($id);
        $this->pos = 0;
        $this->name = "Unbekannt";
        $this->coordsLoaded = false;
        $this->isVisible = true;
    }

    public function allowedFleetActions()
    {
        return array("flight", "explore");
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
        return "Unbekannt";
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
        return "Unerforschte Raumzelle!";
    }

    /**
     * Returns type
     */
    function type()
    {
        return "Unbekannt";
    }

    function imagePath($opt = "")
    {
        return ObjectWithImage::BASE_PATH . "/unexplored/ue1.png";
    }

    /**
     * Returns type
     */
    function entityCode()
    {
        return "e";
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
