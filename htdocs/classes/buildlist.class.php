<?PHP

use EtoA\Building\BuildingRepository;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyRepository;

class BuildList implements IteratorAggregate
{
    private $entityId;
    private $ownerId;

    private $entity;

    /** @var BuildListItem[] */
    private $items = null;
    private $count = null;
    public static $underConstruction = false;

    private $tmpItems = array();

    private $errorMsg;

    public static $GENTECH = 0;

    /**
     * Constructor
     * @param int $entityId
     * @param int $ownerId
     *
     * @access public
     */
    public function __construct($entityId, $ownerId)
    {
        $this->entityId = $entityId;
        $this->ownerId = $ownerId;
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
    public function getIterator()
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
     * @return ArrayIterator<int, BuildListItem>	with key() building_id and current() buildlistitem
     *
     * @access public
     */
    public function getCatIterator($catId = 0, $mode = 'all')
    {
        if ($this->items == null)
            $this->load();
        $catItems = array();

        foreach ($this->items as $id => $item) {
            if ($item->building->typeId == $catId) {
                $add = true;
                if ($mode == 'buildable') {
                    if (!$this->requirementsPassed($id) || $item->isMaxLevel())
                        $add = false;
                } elseif ($mode == 'resable') {
                    if (!($this->checkBuildable($id, false) == 1))
                        $add = false;
                }
                if ($add)
                    $catItems[$id] = $item;
            }
        }
        return new ArrayIterator($catItems);
    }

    private function load()
    {
        global $app;

        /** @var TechnologyRepository $technologyRepository */
        $technologyRepository = $app[TechnologyRepository::class];
        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];

        self::$GENTECH = $technologyRepository->getTechnologyLevel((int) $this->ownerId, TechnologyId::GEN);
        $this->items = array();

        $list = $buildingRepository->getLegacyBuildList($this->entityId);
        $this->count = count($list);
        foreach ($list as $arr) {
            $this->items[$arr['building_id']] = new BuildListItem($arr);
            $this->count++;

            if (($arr['buildlist_build_type'] == 3 || $arr['buildlist_build_type'] == 4) && $arr['buildlist_build_end_time'] > time()) {
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

    function isUnderConstruction()
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
            if ($this->items[$bid]->deactivated > time()) {
                return $this->items[$bid]->deactivated;
            }
        }
        return false;
    }

    // use only for tech and buildings
    function setPeopleWorking($bid, $people, $tech = false)
    {
        global $app;

        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];

        if ($this->items == null)
            $this->load();

        // BUGFIX: if first part is false, check for $tech in second part!

        if ((!$tech && !$this->isUnderConstruction()) || ($tech)) {
            if (isset($this->items[$bid])) {
                global $cp;
                // Free: Total people on planet minus total working people on planet
                // PLUS people working in this building (these can be set again)
                $peopleWorking = $buildingRepository->getPeopleWorking($this->entityId);
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

    function build($bid)
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
                global $cu, $cp;
                if ($this->entity == null) {
                    if ($cp->id != $this->entityId)
                        $this->entity = Entity::createFactoryById($this->entityId);
                    else
                        $this->entity = &$cp;
                }

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
                global $cu, $cp;
                if ($this->entity == null) {
                    if ($cp->id != $this->entityId)
                        $this->entity = Entity::createFactoryById($this->entityId);
                    else
                        $this->entity = &$cp;
                }

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

    public function requirementsPassed($bid = 0)
    {
        if (isset($this->items[$bid])) {
            $req = $this->items[$bid]->building->getBuildingRequirements();
            foreach ($req as $rk => $rv) {
                if (isset($this->items[$rk]) && $rv > $this->items[$rk]->level) {
                    return false;
                }
            }
            $req = $this->items[$bid]->building->getTechRequirements();
            global $app;

            /** @var TechnologyRepository $technologyRepository */
            $technologyRepository = $app[TechnologyRepository::class];
            $techlist = $technologyRepository->getTechnologyLevels($this->ownerId);
            foreach ($req as $rk => $rv) {
                if ($rv > ($techlist[$rk] ?? 0)) {
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
    function getLastError()
    {
        return $this->errorMsg;
    }
}
