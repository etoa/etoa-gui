<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\Universe\Star\StarRepository;

/**
 * Planet class
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class Planet extends Entity
{
    protected $id;
    protected bool $isMain;
    protected $isValid;
    protected $coordsLoaded;
    private $desc;
    private $name;
    // TODO: Make protected and ad getter
    public $resources;
    protected $temp_from;
    protected $temp_to;
    protected $pos;
    protected $starTypeName;
    protected $fields;
    protected $fieldsUsed;
    protected $fieldsBase;
    protected $fieldsExtra;
    protected $debrisField;
    protected $debrisMetal;
    protected $debrisCrystal;
    protected $debrisPlastic;

    /**
     * Constructor
     * Erwartet ein Array mit dem Inhalt des MySQL-Datensatzes, oder die ID eines Planeten
     */
    public function __construct()
    {
        $this->exploreCode = 'e';
        $this->explore = false;
        $this->isValid = false;
        $this->isVisible = true;
    }

    public static function getById($id, \EtoA\Universe\Entity\Entity $entity = null)
    {
        global $app;

        if ($entity === null) {
            /** @var EntityRepository $entityRepository */
            $entityRepository = $app[EntityRepository::class];
            $entity = $entityRepository->getEntity($id);
        }

        /** @var PlanetRepository $planetRepository */
        $planetRepository = $app[PlanetRepository::class];
        $planet = $planetRepository->find($id);

        if ($planet !== null && $entity !== null) {
            $p = self::getByArray($planet, $entity);
            $p->isValid = true;
            return $p;
        }
        return null;
    }

    private static function getByArray(\EtoA\Universe\Planet\Planet $planet, \EtoA\Universe\Entity\Entity $entity)
    {
        global $app;

        $p = new Planet();

        $p->id = $planet->id;
        $p->cellId = $entity->cellId;
        $p->userId = $planet->userId;
        $p->name = $planet->displayName();
        $p->desc = $planet->description;
        $p->image = $planet->image;
        $p->updated = $planet->lastUpdated;
        $p->userChanged = $planet->userChanged;
        $p->lastUserId = $planet->lastUserId;

        $p->owner = new User($planet->userId);

        $p->sx = $entity->sx;
        $p->sy = $entity->sy;
        $p->cx = $entity->cx;
        $p->cy = $entity->cy;
        $p->pos = $entity->pos;

        /** @var PlanetTypeRepository $planetTypeRepository */
        $planetTypeRepository = $app[PlanetTypeRepository::class];
        $planetType = $planetTypeRepository->get($planet->typeId);
        $p->typeId = $planetType->id;
        $p->typeName = $planetType->name;

        $p->habitable = $planetType->habitable;
        $p->collectGas = $planetType->collectGas;

        $p->typeMetal = $planetType->metal;
        $p->typeCrystal = $planetType->crystal;
        $p->typePlastic = $planetType->plastic;
        $p->typeFuel = $planetType->fuel;
        $p->typeFood = $planetType->food;
        $p->typePower = $planetType->power;
        $p->typePopulation = $planetType->people;
        $p->typeResearchtime = $planetType->researchTime;
        $p->typeBuildtime = $planetType->buildTime;

        /** @var StarRepository $starRepository */
        $starRepository = $app[StarRepository::class];
        $star = $starRepository->findStarForCell($entity->cellId);
        /** @var SolarTypeRepository $starTypeRepository */
        $starTypeRepository = $app[SolarTypeRepository::class];
        $starType = $starTypeRepository->find($star->typeId);
        $p->starTypeId = $starType->id;
        $p->starTypeName = $starType->name;
        $p->starMetal = $starType->metal;
        $p->starCrystal = $starType->crystal;
        $p->starPlastic = $starType->plastic;
        $p->starFuel = $starType->fuel;
        $p->starFood = $starType->food;
        $p->starPower = $starType->power;
        $p->starPopulation = $starType->people;
        $p->starResearchtime = $starType->researchTime;
        $p->starBuildtime = $starType->buildTime;

        $p->debrisMetal = $planet->wfMetal;
        $p->debrisCrystal = $planet->wfCrystal;
        $p->debrisPlastic = $planet->wfPlastic;

        $p->debrisField = ($p->debrisMetal + $p->debrisCrystal + $p->debrisPlastic > 0);

        $p->fieldsBase = $planet->fields;
        $p->fieldsExtra = $planet->fieldsExtra;
        $p->fieldsUsed = $planet->fieldsUsed;

        $p->fields_extra = $planet->fieldsExtra;
        $p->fields_used = $planet->fieldsUsed;

        $p->fields = $p->fieldsBase + $p->fieldsExtra;

        $p->temp_from = $planet->tempFrom;
        $p->temp_to = $planet->tempTo;
        $p->people = max(0, $planet->people);
        $p->people_place = max(0, $planet->peoplePlace);

        $p->resMetal = max(0, floor($planet->resMetal));
        $p->resCrystal = max(0, floor($planet->resCrystal));
        $p->resPlastic = max(0, floor($planet->resPlastic));
        $p->resFuel = max(0, floor($planet->resFuel));
        $p->resFood = max(0, floor($planet->resFood));
        $p->usePower = max(0, floor($planet->usePower));

        $p->resources = array(
            $p->resMetal,
            $p->resCrystal,
            $p->resPlastic,
            $p->resFuel,
            $p->resFood
        );

        $p->bunkerMetal = max(0, $planet->bunkerMetal);
        $p->bunkerCrystal = max(0, $planet->bunkerCrystal);
        $p->bunkerPlastic = max(0, $planet->bunkerPlastic);
        $p->bunkerFuel = max(0, $planet->bunkerFuel);
        $p->bunkerFood = max(0, $planet->bunkerFood);

        $p->storeMetal = $planet->storeMetal;
        $p->storeCrystal = $planet->storeCrystal;
        $p->storePlastic = $planet->storePlastic;
        $p->storeFuel = $planet->storeFuel;
        $p->storeFood = $planet->storeFood;

        $p->prodMetal = $planet->prodMetal;
        $p->prodCrystal = $planet->prodCrystal;
        $p->prodPlastic = $planet->prodPlastic;
        $p->prodFuel = $planet->prodFuel;
        $p->prodFood = $planet->prodFood;
        $p->prodPower = max(0, $planet->prodPower);
        $p->prodPeople = $planet->prodPeople;

        $p->isMain = $planet->mainPlanet;

        return $p;
    }

    public function __get($var)
    {
        if ($var == 'desc') {
            return StringUtils::encodeDBStringToPlaintext($this->desc);
        }
        if ($var == 'name') {
            return htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8', true);
        }
        return $this->$var;
    }

    public function allowedFleetActions()
    {
        $arr = array();
        if ($this->ownerId() > 0) {
            $arr[] = "transport";
            $arr[] = "fetch";
            $arr[] = "position";
            $arr[] = "attack";
            $arr[] = "spy";
            $arr[] = "invade";
            $arr[] = "spyattack";
            $arr[] = "stealthattack";
            $arr[] = "fakeattack";
            $arr[] = "bombard";
            $arr[] = "antrax";
            $arr[] = "gasattack";
            $arr[] = "createdebris";
            $arr[] = "alliance";
            $arr[] = "support";
            $arr[] = "hijack";
            $arr[] = "market";
            $arr[] = "emp";
        }
        if ($this->ownerId() == 0 && $this->habitable)
            $arr[] = "colonize";
        if ($this->debrisField)
            $arr[] = "collectdebris";
        if ($this->collectGas) {
            $arr[] = "collectfuel";
            $arr[] = "analyze";
        }
        $arr[] = "flight";
        return $arr;
    }

    function id()
    {
        return $this->id;
    }

    function entityCode()
    {
        return "p";
    }


    function entityCodeString()
    {
        return "Planet";
    }

    function ownerId()
    {
        return $this->userId;
    }

    function owner()
    {
        return $this->owner;
    }

    function ownerMain()
    {
        return $this->isMain;
    }

    function type()
    {
        return $this->typeName;
    }
    function imagePath($opt = "")
    {
        if ($opt == "b") {
            return IMAGE_PATH . "/planets/planet" . $this->image . "." . IMAGE_EXT;
        }
        if ($opt == "m") {
            return IMAGE_PATH . "/planets/planet" . $this->image . "_middle." . IMAGE_EXT;
        }
        return IMAGE_PATH . "/planets/planet" . $this->image . "_small." . IMAGE_EXT;
    }

    function name()
    {
        return $this->__get('name'); //htmlspecialchars($this->name);
    }

    function getNoBrDesc()
    {
        return htmlspecialchars($this->desc, ENT_QUOTES, 'UTF-8', true);
    }

    function __toString()
    {
        return $this->formatedCoords() . " " . $this->name();
    }

    function cellId()
    {
        return $this->cellId;
    }

    /**
     * Returns current cell and stellar system
     *
     * @return string
     */
    function getSectorSolsys()
    {
        return $this->sx . "/" . $this->sy . " : " . $this->cx . "/" . $this->cy;
    }

    function userChanged()
    {
        return $this->userChanged;
    }

    /**
     * Returns current coordinates
     *
     * @return string
     */
    function getCoordinates()
    {
        return $this->formatedCoords();
    }

    //
    // Getters
    //
    function resMetal()
    {
        return $this->resMetal;
    }
    function resCrystal()
    {
        return $this->resCrystal;
    }
    function resPlastic()
    {
        return $this->resPlastic;
    }
    function resFuel()
    {
        return $this->resFuel;
    }
    function resFood()
    {
        return $this->resFood;
    }
    function usePower()
    {
        return $this->usePower;
    }
    function people()
    {
        return $this->people;
    }

    function ownerPoints()
    {
        return $this->owner->points;
    }
    function ownerHoliday()
    {
        return $this->owner->holiday;
    }
    function ownerLocked()
    {
        return $this->owner->locked;
    }
    function ownerAlliance()
    {
        return $this->owner->allianceId;
    }

    function getRes($i)
    {
        switch ($i) {
            case 1:
                return $this->resMetal;
            case 2:
                return $this->resCrystal;
            case 3:
                return $this->resPlastic;
            case 4:
                return $this->resFuel;
            case 5:
                return $this->resFood;
        }
    }

    //Added getter with 0-5 like everywhere else
    function getRes1($i)
    {
        switch ($i) {
            case 0:
                return $this->resMetal;
            case 1:
                return $this->resCrystal;
            case 2:
                return $this->resPlastic;
            case 3:
                return $this->resFuel;
            case 4:
                return $this->resFood;
        }
    }

    function getProd($i)
    {
        switch ($i) {
            case 0:
                return $this->prodMetal;
            case 1:
                return $this->prodCrystal;
            case 2:
                return $this->prodPlastic;
            case 3:
                return $this->prodFuel;
            case 4:
                return $this->prodFood;
        }
    }

    function checkRes($data)
    {
        foreach (ResourceNames::NAMES as $rk => $rn) {
            if (isset($data[$rk]) && $data[$rk] >= 0) {
                if ($this->resources[$rk] - intval($data[$rk]) < 0)
                    return false;
            }
        }
        return true;
    }

    function reloadRes()
    {
        global $app;

        /** @var PlanetRepository $planetRepository */
        $planetRepository = $app[PlanetRepository::class];

        $resources = $planetRepository->getPlanetResources($this->id());
        if ($resources !== null) {
            $this->resMetal = floor($resources->metal);
            $this->resCrystal = floor($resources->crystal);
            $this->resPlastic = floor($resources->plastic);
            $this->resFuel = floor($resources->fuel);
            $this->resFood = floor($resources->food);
            $this->people = floor($resources->people);
        }
    }

    public function getFleetTargetForwarder()
    {
        return null;
    }

    public function getResourceLog()
    {
        return $this->resMetal . ":" . $this->resCrystal . ":" . $this->resPlastic . ":" . $this->resFuel . ":" . $this->resFood . ":" . $this->people . ":0,w," . $this->debrisMetal . ":" . $this->debrisCrystal . ":" . $this->debrisPlastic;
    }

    public function lastUserCheck()
    {
        $t = $this->userChanged() + COLONY_DELETE_THRESHOLD;
        if ($t > time()) {
            return $this->lastUserId;
        }
        return 0;
    }
}
