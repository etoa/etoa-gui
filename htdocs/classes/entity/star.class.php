<?PHP

use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Star\SolarType;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\Universe\Star\StarRepository;

/**
 * Star-Class
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class Star extends Entity
{
    protected $id;
    protected $pos;
    private $name;
    private $typeId;
    protected $isValid;
    protected $typeName;
    public $named;
    protected $coordsLoaded;
    protected $sx;
    protected $sy;
    protected $cx;
    protected $cy;
    protected $cellId;
    private ?SolarType $solType = null;

    /**
     * The constructor
     */
    public function __construct($id = 0, \EtoA\Universe\Entity\Entity $entity = null)
    {
        global $app;

        if ($entity === null) {
            /** @var EntityRepository $entityRepository */
            $entityRepository = $app[EntityRepository::class];
            $entity = $entityRepository->getEntity($id);
        }

        $this->isValid = false;
        $this->coordsLoaded = false;
        $this->isVisible = true;

        /** @var StarRepository $starRepository */
        $starRepository = $app[StarRepository::class];
        $star = $starRepository->find($id);

        if ($star !== null) {
            /** @var SolarTypeRepository $solTypeRepository */
            $solTypeRepository = $app[SolarTypeRepository::class];
            $this->solType = $solTypeRepository->find($star->typeId);

            $this->id = $star->id;

            if ($star->name != "") {
                $this->name = stripslashes($star->name);
                $this->named = true;
            } else {
                $this->name = "Unbenannt";
                $this->named = false;
            }
            $this->typeId = $star->typeId;
            $this->pos = $entity->pos;
            $this->typeName = $this->solType->name;

            $this->isValid = true;
        }
    }

    public function typeData()
    {
        $rtn = array(
            "metal" => $this->solType->metal,
            "crystal" => $this->solType->crystal,
            "plastic" => $this->solType->plastic,
            "fuel" => $this->solType->fuel,
            "food" => $this->solType->food,
            "power" => $this->solType->power,
            "population" => $this->solType->people,
            "buildtime" => $this->solType->buildTime,
            "researchtime" => $this->solType->researchTime,
            "comment" => $this->solType->comment
        );
        return $rtn;
    }

    public function allowedFleetActions()
    {
        return array("flight", "explore");
    }

    /**
     * Returns validity
     */
    public function isValid()
    {
        return $this->isValid;
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
        return (addslashes($this->name));
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
        return "Stern";
    }

    /**
     * Returns star type
     */
    function type()
    {
        return $this->typeName;
    }

    function imagePath($opt = "")
    {
        if ($opt == "b") {
            return IMAGE_PATH . "/stars/star" . $this->typeId . ".png";
        }
        return IMAGE_PATH . "/stars/star" . $this->typeId . "_small.png";
    }

    /**
     * Returns type
     */
    function entityCode()
    {
        return "s";
    }

    /**
     * To-String function
     */
    function __toString()
    {
        if (!$this->coordsLoaded) {
            $this->loadCoords();
        }
        return $this->formatedCoords() . " " . $this->name;
    }

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
