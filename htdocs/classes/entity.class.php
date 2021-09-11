<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;

/**
 * Abstract class for all space entities
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
abstract class Entity
{
    protected $isVisible;

    private ConfigurationService $config;

    /**
     * Private constructor
     * Prevents usage as object
     */
    private function __construct()
    {
        // TODO
        global $app;

        $this->config = $app[ConfigurationService::class];
    }

    //
    // Abstract methods
    //

    /**
     * Return entity-id
     */
    public abstract function id();

    /**
     * Return entity name
     */
    public abstract function name();

    /**
     * Return entity-owner
     */
    public abstract function owner();

    /**
     * Return entity-owner id
     */
    public abstract function ownerId();

    /**
     * Return true if this is the owner's main entity
     */
    public abstract function ownerMain();

    /**
     * Return entity-code
     */
    public abstract function entityCode();

    /**
     * Return entity-code string
     */
    public abstract function entityCodeString();

    /**
     * Some entities are dividet into special
     * types; return this type
     */
    public abstract function type();

    /**
     * Provies the current image path
     */
    public abstract function imagePath($opt = "");

    /**
     * Return coordinates
     */
    public abstract function __toString();

    /**
     * Return cell id
     */
    public abstract function cellId();

    public abstract function getFleetTargetForwarder();

    //
    // General methods
    //

    public function smallImage()
    {
        return "<img src=\"" . $this->imagePath() . "\" style=\"width:40px;height:40px;\" alt=\"" . $this->name() . "\" />";
    }

    /**
     * Return if entity is visible in map
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    public function getCell()
    {
        return new Cell($this->cellId());
    }

    public abstract function allowedFleetActions();

    function sx()
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }
        return $this->sx;
    }

    function sy()
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }
        return $this->sy;
    }

    function cx()
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }
        return $this->cx;
    }

    function cy()
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }
        return $this->cy;
    }

    public function getEntityCoordinates(): \EtoA\Universe\Entity\EntityCoordinates
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }

        return new \EtoA\Universe\Entity\EntityCoordinates($this->sx, $this->sy, $this->cx, $this->cy, $this->pos());
    }

    /**
     * Returns owner
     */
    function pos()
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }
        return $this->pos;
    }

    // Overwritable functions
    function ownerPoints()
    {
        return 0;
    }
    function ownerHoliday()
    {
        return false;
    }
    function ownerLocked()
    {
        return false;
    }
    function ownerAlliance()
    {
        return 0;
    }
    function lastUserCheck()
    {
        return 0;
    }

    /**
     * check if data could be loaded
     */
    public function isValid()
    {
        return $this->isValid;
    }

    public function loadCoords()
    {
        if (!$this->coordsLoaded) {
            global $app;

            /** @var EntityRepository $entityRepository */
            $entityRepository = $app[EntityRepository::class];

            $entity = $entityRepository->getEntity($this->id);
            if ($entity !== null) {
                $this->sx = $entity->sx;
                $this->sy = $entity->sy;
                $this->cx = $entity->cx;
                $this->cy = $entity->cy;
                $this->pos = $entity->pos;
                $this->cellId = $entity->cellId;
                $this->coordsLoaded = true;
            }
        }
    }

    protected function formatedCoords()
    {
        $this->loadCoords();
        return $this->sx . "/" . $this->sy . " : " . $this->cx . "/" . $this->cy . " : " . $this->pos;
    }

    public function coordsArray()
    {
        $this->loadCoords();
        return array($this->sx, $this->sy, $this->cx, $this->cy, $this->pos);
    }

    /**
     * Creates an instance of a child class
     * using the factory design pattern
     */
    public static function createFactory($type, $id = 0)
    {
        switch ($type) {
            case EntityType::STAR:
                return new Star($id);
            case EntityType::PLANET:
                return Planet::getById($id);
            case EntityType::ASTEROID:
                return new AsteroidField($id);
            case EntityType::NEBULA:
                return new Nebula($id);
            case EntityType::WORMHOLE:
                return new Wormhole($id);
            case EntityType::EMPTY_SPACE:
                return new EmptySpace($id);
            case EntityType::MARKET:
                return new Market($id);
            case EntityType::UNEXPLORED:
                return new UnExplored($id);
            case EntityType::ALLIANCE_MARKET:
                return new Allianz($id);
            default:
                return new UnknownEntity($id);
        }
    }

    /**
     * Creates an instance of a child class
     * using the factory design pattern
     */
    public static function createFactoryById($id)
    {
        global $app;

        /** @var EntityRepository $entityRepository */
        $entityRepository = $app[EntityRepository::class];

        $entity = $entityRepository->getEntity($id);
        if ($entity !== null) {
            switch ($entity->code) {
                case EntityType::STAR:
                    return new Star($id);
                case EntityType::PLANET:
                    return Planet::getById($id);
                case EntityType::ASTEROID:
                    return new AsteroidField($id);
                case EntityType::NEBULA:
                    return new Nebula($id);
                case EntityType::WORMHOLE:
                    return new Wormhole($id);
                case EntityType::EMPTY_SPACE:
                    return new EmptySpace($id);
                case EntityType::MARKET:
                    return new Market($id);
                case EntityType::ALLIANCE_MARKET:
                    return new Allianz($id);
                default:
                    return new UnknownEntity($id);
            }
        }

        return null;
        //die ("Ungültige ID");
    }

    /**
     * Creates an instance of a child class
     *
     */
    public static function createFactoryUnkownCell($cell = 0)
    {
        global $app;

        /** @var EntityRepository $entityRepository */
        $entityRepository = $app[EntityRepository::class];

        $entity = $entityRepository->findByCellAndPosition($cell, 0);
        if ($entity !== null) {
            return new UnknownEntity($entity->id);
        }

        return false;
    }

    public static $entityColors = [
        EntityType::STAR => '#ff0',
        EntityType::PLANET => '#0f0',
        EntityType::ASTEROID => '#ccc',
        EntityType::NEBULA => '#FF00FF',
        EntityType::WORMHOLE => '#8000FF',
        EntityType::EMPTY_SPACE => '#55f',
        EntityType::ALLIANCE_MARKET => '#fff',
        EntityType::MARKET => '#fff'
    ];

    public function detailLink()
    {
        return "<a href=\"?page=entity&amp;id=" . $this->id . "\">" . $this->__toString() . "</a>";
    }
}
