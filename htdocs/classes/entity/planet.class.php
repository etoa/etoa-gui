<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;

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

    public static function getById($id)
    {
        $res = dbquery("
		SELECT
			planets.*,
			cells.sx,
			cells.sy,
			cells.cx,
			cells.cy,
			cells.id as cell_id,
			pentity.pos,
			planet_types.*,
			sol_types.*
		FROM
		(
			planets
			INNER JOIN
				planet_types
				ON planets.planet_type_id = planet_types.type_id
				AND planets.id='" . intval($id) . "'
		)
		INNER JOIN
		(
			entities AS pentity
			INNER JOIN cells
				ON cells.id = pentity.cell_id
			INNER JOIN
				entities AS sentity
				ON cells.id = sentity.cell_id
				AND sentity.pos =0
			INNER JOIN stars
				ON stars.id = sentity.id
			INNER JOIN sol_types
				ON sol_types.sol_type_id = stars.type_id
		)
		ON planets.id = pentity.id
		LIMIT 1;
		;");
        if (mysql_num_rows($res) > 0) {
            $arr = mysql_fetch_assoc($res);
            $p = self::getByArray($arr);
            $p->isValid = true;
            return $p;
        }
        return null;
    }

    public static function getByArray($arr)
    {
        $p = new Planet();

        $p->id = $arr['id'];
        $p->cellId = $arr['cell_id'];
        $p->userId = $arr['planet_user_id'];
        $p->name = $arr['planet_name'] != "" ? ($arr['planet_name']) : 'Unbenannt';
        $p->desc = $arr['planet_desc'];
        $p->image = $arr['planet_image'];
        $p->updated = $arr['planet_last_updated'];
        $p->userChanged = $arr['planet_user_changed'];
        $p->lastUserId = $arr['planet_last_user_id'];

        $p->owner = new User($arr['planet_user_id']);

        $p->sx = $arr['sx'];
        $p->sy = $arr['sy'];
        $p->cx = $arr['cx'];
        $p->cy = $arr['cy'];
        $p->pos = $arr['pos'];

        $p->typeId = $arr['type_id'];
        $p->typeName = $arr['type_name'];

        $p->habitable = (bool)$arr['type_habitable'];
        $p->collectGas = (bool)$arr['type_collect_gas'];

        $p->typeMetal = $arr['type_f_metal'];
        $p->typeCrystal = $arr['type_f_crystal'];
        $p->typePlastic = $arr['type_f_plastic'];
        $p->typeFuel = $arr['type_f_fuel'];
        $p->typeFood = $arr['type_f_food'];
        $p->typePower = $arr['type_f_power'];
        $p->typePopulation = $arr['type_f_population'];
        $p->typeResearchtime = $arr['type_f_researchtime'];
        $p->typeBuildtime = $arr['type_f_buildtime'];

        $p->starTypeId = $arr['sol_type_id'];
        $p->starTypeName = $arr['sol_type_name'];
        $p->starMetal = $arr['sol_type_f_metal'];
        $p->starCrystal = $arr['sol_type_f_crystal'];
        $p->starPlastic = $arr['sol_type_f_plastic'];
        $p->starFuel = $arr['sol_type_f_fuel'];
        $p->starFood = $arr['sol_type_f_food'];
        $p->starPower = $arr['sol_type_f_power'];
        $p->starPopulation = $arr['sol_type_f_population'];
        $p->starResearchtime = $arr['sol_type_f_researchtime'];
        $p->starBuildtime = $arr['sol_type_f_buildtime'];

        $p->debrisMetal = $arr['planet_wf_metal'];
        $p->debrisCrystal = $arr['planet_wf_crystal'];
        $p->debrisPlastic = $arr['planet_wf_plastic'];

        $p->debrisField = ($p->debrisMetal + $p->debrisCrystal + $p->debrisPlastic > 0);

        $p->fieldsBase = $arr['planet_fields'];
        $p->fieldsExtra = $arr['planet_fields_extra'];
        $p->fieldsUsed = $arr['planet_fields_used'];

        $p->fields_extra = $arr['planet_fields_extra'];
        $p->fields_used = $arr['planet_fields_used'];

        $p->fields = $p->fieldsBase + $p->fieldsExtra;

        $p->temp_from = $arr['planet_temp_from'];
        $p->temp_to = $arr['planet_temp_to'];
        $p->people = max(0, $arr['planet_people']);
        $p->people_place = max(0, $arr['planet_people_place']);

        $p->resMetal = max(0, floor($arr['planet_res_metal']));
        $p->resCrystal = max(0, floor($arr['planet_res_crystal']));
        $p->resPlastic = max(0, floor($arr['planet_res_plastic']));
        $p->resFuel = max(0, floor($arr['planet_res_fuel']));
        $p->resFood = max(0, floor($arr['planet_res_food']));
        $p->usePower = max(0, floor($arr['planet_use_power']));

        $p->resources = array(
            $p->resMetal,
            $p->resCrystal,
            $p->resPlastic,
            $p->resFuel,
            $p->resFood
        );

        $p->bunkerMetal = max(0, $arr['planet_bunker_metal']);
        $p->bunkerCrystal = max(0, $arr['planet_bunker_crystal']);
        $p->bunkerPlastic = max(0, $arr['planet_bunker_plastic']);
        $p->bunkerFuel = max(0, $arr['planet_bunker_fuel']);
        $p->bunkerFood = max(0, $arr['planet_bunker_food']);

        $p->storeMetal = $arr['planet_store_metal'];
        $p->storeCrystal = $arr['planet_store_crystal'];
        $p->storePlastic = $arr['planet_store_plastic'];
        $p->storeFuel = $arr['planet_store_fuel'];
        $p->storeFood = $arr['planet_store_food'];

        $p->prodMetal = $arr['planet_prod_metal'];
        $p->prodCrystal = $arr['planet_prod_crystal'];
        $p->prodPlastic = $arr['planet_prod_plastic'];
        $p->prodFuel = $arr['planet_prod_fuel'];
        $p->prodFood = $arr['planet_prod_food'];
        $p->prodPower = max(0, $arr['planet_prod_power']);
        $p->prodPeople = $arr['planet_prod_people'];

        $p->isMain = ($arr['planet_user_main'] == 1);

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
        global $resNames;

        foreach ($resNames as $rk => $rn) {
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
