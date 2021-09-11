<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Wormhole\WormholeRepository;

/**
 * Class for nebula entity
 */
class Wormhole extends Entity
{
    protected $id;
    protected $coordsLoaded;
    public $pos;
    protected $isValid;
    public $sx;
    public $sy;
    public $cx;
    public $cy;
    protected $cellId;
    private $name;
    private $targetId;
    private $persistent;
    private $changed;
    private $dataLoaded;

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
        $this->dataLoaded = false;
        $this->targetId = -1;
        $this->changed = -1;
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
        return "Wurmloch";
    }

    /**
     * Returns type
     */
    function type()
    {
        if (!$this->dataLoaded) {
            $this->loadData();
        }
        return $this->persistent ? "stabil" : "veränderlich";
    }

    function imagePath($opt = "")
    {
        if (!$this->dataLoaded) {
            $this->loadData();
        }
        $prefix = $this->persistent ? 'wormhole_persistent' : 'wormhole';
        return IMAGE_PATH . "/wormholes/" . $prefix . "1_small." . IMAGE_EXT;
    }

    /**
     * Returns type
     */
    function entityCode()
    {
        return "w";
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

    function loadData()
    {
        if ($this->dataLoaded == false) {
            global $app;

            /** @var WormholeRepository $wormholeRepository */
            $wormholeRepository = $app[WormholeRepository::class];
            $wormhole = $wormholeRepository->find($this->id);
            if ($wormhole !== null) {
                $this->targetId = $wormhole->targetId;
                $this->persistent = $wormhole->persistent;
                $this->changed = $wormhole->changed;
                $this->dataLoaded = true;
            }
        }
    }

    function targetId()
    {
        if (!$this->dataLoaded) {
            $this->loadData();
        }
        return $this->targetId;
    }

    function isPersistent()
    {
        if (!$this->dataLoaded) {
            $this->loadData();
        }
        return $this->persistent;
    }

    function changed()
    {
        if (!$this->dataLoaded) {
            $this->loadData();
        }
        return $this->changed;
    }

    public function getFleetTargetForwarder()
    {
        // Forward in 0 secs to the other end of the wormhole and allow selection of new target
        return array($this->targetId, 0, true);
    }
}
