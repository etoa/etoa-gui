<?PHP

namespace EtoA\Building;

use ArrayIterator;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use IteratorAggregate;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;


//TODO: refactor this mess
class BuildList implements IteratorAggregate
{
    private Planet $entity;
    private User $owner;

    /** @var BuildListItem[] */
    private ?array $items = null;
    public static bool $underConstruction = false;

    private array $tmpItems = array();

    private string $errorMsg;

    public static int $GENTECH = 0;

    /**
     * Constructor
     * @access public
     */
    public function __construct(
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly Security                 $security,
        private readonly RequestStack $requestStack,
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly BuildingDataRepository $buildingDataRepository
    )
    {
        $this->load();
    }

    /**
     *  Returns an Iterator with every element in the buildlist,
     * to specify the selection use the $load param in the Constructor
     *
     * @return ArrayIterator with key() building_id and current() buildlistitem
     *
     * @access public
     */
    public function getIterator(): ArrayIterator
    {
        if ($this->items == null)
            $this->load();
        return new ArrayIterator($this->items);
    }

    /**
     *  Returns an ArrayIterator with every element in the selected category,
     * use the $mode param to specify the returned buildings aswell as the $load param in the Constructor
     *
     * @param int $catId
     * @param string $mode {all | buildable | resable}
     *
     * @return ArrayIterator<int, BuildingListItem>	with key() building_id and current() buildlistitem
     *
     * @access public
     */
    public function getCatIterator(int $catId = 0, string $mode = 'all'): ArrayIterator
    {
        if ($this->items == null)
            $this->load();
        $catItems = array();

        foreach ($this->items as $item) {
            if ($item->getType()->getId() == $catId) {
                $add = true;
                if ($mode == 'buildable') {
                    if (!$this->requirementsPassed($item['q_id']) || $item->isMaxLevel())
                        $add = false;
                } elseif ($mode == 'resable') {
                    if (!($this->checkBuildable($item['q_id']) == 1))
                        $add = false;
                }
                if ($add)
                    $catItems[$item->getId()] = $item;
            }
        }

        return new ArrayIterator($catItems);
    }

    private function load(): void
    {
        $this->owner = $this->security->getUser()->getData();
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        self::$GENTECH = $this->technologyListItemRepository->getTechnologyLevel($this->owner, TechnologyId::GEN)??0;
        $this->items = array();

        $list = $this->buildingDataRepository->getBuildingsWithBuildList($cp);

        foreach ($list as $arr) {
            $this->items[$arr->getId()] = $arr;

            if (($arr->bl?->getBuildType() === 3 || $arr->bl?->getBuildType() === 4) && $arr->bl?->getEndTime() > time()) {
                self::$underConstruction = true;
            }
        }
    }

    function item($bid)
    {
        if ($this->items == null)
            $this->load();
        if (isset($this->items[$bid]))
            return $this->items[$bid];
        if (isset($this->tmpItems[$bid]))
            return $this->tmpItems[$bid];
        return false;
    }

    function isUnderConstruction(): bool
    {
        if (!isset(self::$underConstruction))
            $this->load();
        return self::$underConstruction;
    }

    function getDeactivated($bid)
    {
        if ($this->items == null)
            $this->load();
        if (isset($this->items[$bid])) {
            if ($this->items[$bid]->getDeactivated() > time()) {
                return $this->items[$bid]->getDeactivated();
            }
        }
        return false;
    }

    // use only for tech and buildings
    function setPeopleWorking($bid, $people, $tech = false)
    {
        if ($this->items == null)
            $this->load();

        // BUGFIX: if first part is false, check for $tech in second part!

        if ((!$tech && !$this->isUnderConstruction()) || ($tech)) {
            if (isset($this->items[$bid])) {
                global $cp;
                // Free: Total people on planet minus total working people on planet
                // PLUS people working in this building (these can be set again)
                $peopleWorking = $this->buildingListItemRepository->getPeopleWorking($this->entity);
                $free = $cp->people - $peopleWorking->total + $peopleWorking->getById($bid);
                if ($free >= $people) {
                    return $this->items[$bid]->setPeopleWorking($people, $tech);
                }
            }
        }
        return false;
    }

    function getCosts($bid, $type = 'build', $levelUp = 0)
    {
        if ($type == 'build') {
            return $this->items[$bid]->getBuildCosts($levelUp);
        } else {
            return $this->items[$bid]->getDemolishCosts($levelUp);
        }
    }

    function build($bid): bool
    {
        if ($this->checkBuildable($bid) > 0) {
            if (isset($this->items[$bid])) {
                $this->errorMsg =  $this->items[$bid]->build();
                if ($this->errorMsg == "")
                    return true;
                else
                    return false;
            }
        }
        $this->errorMsg = "Geb&auml;de nicht baubar!";
        return false;
    }

    function demolish($bid)
    {
        if ($this->checkDemolishable($bid)) {
            $this->errorMsg =  $this->items[$bid]->demolish();
            if ($this->errorMsg == "")
                return true;
            else
                return false;
        }
        $this->errorMsg = "Geb&auml;de nicht abreissbar!";
        return false;
    }

    function cancelBuild($bid)
    {
        if (isset($this->items[$bid])) {
            $this->errorMsg =  $this->items[$bid]->cancelBuild();
            if ($this->errorMsg == "")
                return true;
            else
                return false;
        }
        $this->errorMsg = "Geb&aauml;de nicht vorhanden!";
        return false;
    }

    function cancelDemolish($bid)
    {
        if (isset($this->items[$bid])) {
            $this->errorMsg =  $this->items[$bid]->cancelDemolish();
            if ($this->errorMsg == "")
                return true;
            else
                return false;
        }
        $this->errorMsg = "Geb&aauml;de nicht vorhanden!";
        return false;
    }

    /**
     * Check wether an item is buildable. Conditions are
     * no building under construction, enough resources, not maxed out level, enough fieldsUsed,
     * and satisfied prerequisites.
     *
     *
     *	@return int 1=buildable,0=not buildable but show resbox, -1= not buildable & no res box
     */
    function checkBuildable($bid, $uncheckConstruction = false)
    {
        if (!isset($this->items[$bid]->buildableStatus)) {
            // check all the buildings
            if (!$this->isUnderConstruction() || $uncheckConstruction) {
                // check max level
                if (!$this->items[$bid]->isMaxLevel()) {
                    $cst = $this->items[$bid]->getBuildCosts();
                    // Check costs
                    if (
                        $cst['costs0'] <= $this->entity->getRes1(0)
                        && $cst['costs1'] <= $this->entity->getRes1(1)
                        && $cst['costs2'] <= $this->entity->getRes1(2)
                        && $cst['costs3'] <= $this->entity->getRes1(3)
                        && $cst['costs4'] <= $this->entity->getRes1(4)
                    ) {
                        // check fields
                        if ($this->items[$bid]->building->fields == 0 || $cp->fields_used + $this->items[$bid]->building->fields <= $cp->fields + $cp->fields_extra) {
                            if ($this->requirementsPassed($bid))
                                $this->items[$bid]->buildableStatus = 1;
                            else {
                                $this->errorMsg = 'Voraussetzungen nicht erf&uuml;llt!';
                                $this->items[$bid]->buildableStatus = -1;
                            }
                        } else {
                            $this->errorMsg = 'Nicht gen&uuml;gend Felder vorhanden!';
                            $this->items[$bid]->buildableStatus = 0;
                        }
                    } else {
                        $this->errorMsg = 'Zuwenig Rohstoffe vorhanden!';
                        $this->items[$bid]->buildableStatus = 0;
                    }
                } else {
                    $this->errorMsg = 'Maximalstufe erreicht! Kein weiterer Ausbau m&ouml;glich!';
                    $this->items[$bid]->buildableStatus = -1;
                }
            } else {
                $this->errorMsg = 'Es wird gerade an einem Geb&auml;ude gebaut!';
                $this->items[$bid]->buildableStatus = 0;
            }
        }
        return $this->items[$bid]->buildableStatus;
    }

    /**
     * Check wether an item is demolishable. Conditions are
     * no building under construction and enough resources.
     */
    function checkDemolishable($bid)
    {
        // check all the buildings
        $this->load();

        if (!$this->getDeactivated($bid)) {
            if (!$this->isUnderConstruction()) {
                $cst = $this->items[$bid]->getDemolishCosts();
                // Check costs
                if (
                    $cst['costs0'] <= $this->entity->getRes1(0)
                    && $cst['costs1'] <= $this->entity->getRes1(1)
                    && $cst['costs2'] <= $this->entity->getRes1(2)
                    && $cst['costs3'] <= $this->entity->getRes1(3)
                    && $cst['costs4'] <= $this->entity->getRes1(4)
                ) {
                    return true;
                } else
                    $this->errorMsg = "Zuwenig Rohstoffe vorhanden!";
            } else
                $this->errorMsg = "Es wird gerade an einem Geb&auml;ude gebaut!";
            return false;
        } else {
            $this->errorMsg = "Das Geb&auml;ude wurde deaktiviert!";
        }
    }

    public function requirementsPassed($bid = 0): bool
    {
        if (isset($this->items[$bid])) {
            $requirements = $this->items[$bid]->getObjectRequirements();
            foreach ($requirements as $requirement) {
                if ($requirement->getBuilding() && $requirement->getLevel() > $this->items[$requirement->getBuilding()->getId()]->bl?->getCurrentLevel()) {
                    return false;
                }

                if ($requirement->getTech() && $requirement->getLevel() > $this->technologyListItemRepository->findOneBy(['user'=>$this->owner,'technology'=>$requirement->getTech()])?->getLevel()) {
                    return false;
                }
            }

            return true;
        }
        return false;
    }

    /**
     * Returns a message of the last error produced by this instance
     */
    function getLastError(): string
    {
        return $this->errorMsg;
    }
}
