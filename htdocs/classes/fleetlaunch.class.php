<?PHP

use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Fleet\FleetStatus;
use EtoA\Log\FleetLogRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Support\StringUtils;
use EtoA\Specialist\SpecialistService;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;

/**
 * Fleet launch class, provides the full workflow for starting a fleet
 * and thus creating a fleet object and record in the database
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class FleetLaunch
{
    public const FLEET_NOCONTROL_NUM = 1;

    //
    // Variable definitions
    //
    public Planet $sourceEntity;
    public ?Entity $targetEntity = null;
    public $owner;
    public ?Entity $wormholeEntryEntity = null;
    public ?Entity $wormholeExitEntity = null;
    var $ownerId;

    var $ships;
    var $shipCount;
    var $shipsFixed;

    var $speed;
    var $speed1;
    var $speedPercent;
    var $speedPercent1;
    var $duration;
    var $duration1;
    var $costsPerHundredAE;
    var $costsPerHundredAE1;
    var $timeLaunchLand;
    var $costsLaunchLand;
    var $pilots;
    var $capacityTotal;
    var $capacityResUses;
    var $capacityFuelUsed;
    var $capacityPeopleTotal;
    var $capacityPeopleLoaded;

    var $distance;
    var $distance1;

    private $action;
    private $error;
    public $sBonusSpeed;
    public $wormholeEnable;
    /** @var \EtoA\Fleet\Fleet[] */
    public array $aFleets = [];
    /** @var int[] */
    private array $supportedAllianceEntities = [];
    public $allianceSlots;
    private string $entityResourceLogStart;

    /**
     * The constructor
     *
     * >> Step 1 <<
     */
    public function __construct(Planet $sourceEnt, $ownerEnt)
    {
        // TODO
        global $app;

        /** @var RaceDataRepository $raceRepository */
        $raceRepository = $app[RaceDataRepository::class];
        $race = $raceRepository->getRace($ownerEnt->raceId);

        $this->sourceEntity = $sourceEnt;
        $this->owner = $ownerEnt;
        $this->ownerId = $ownerEnt->id;
        $this->ownerRaceName = $race->name;
        $this->raceSpeedFactor = $race->fleetTime;
        $this->possibleFleetStarts = 0;
        $this->fleetSlotsUsed = 0;
        $this->fleetControlLevel = 0;

        $this->ships = array();
        $this->speedPercent = 100;
        $this->speed = 0;
        $this->speed1 = 0;
        $this->sBonusSpeed = 1;
        $this->sBonusReadiness = 1;
        $this->duration = 0;
        $this->action = '';
        $this->costsPerHundredAE = 0;
        $this->costsPerHundredAE1 = 0;
        $this->timeLaunchLand = 0;
        $this->costsLaunchLand = 0;
        $this->pilots = 0;
        $this->pilotsAvailable = 0;
        $this->sBonusPilots = 1;
        $this->capacityTotal = 0;
        $this->capacityResLoaded = 0;
        $this->capacityFuelUsed = 0;
        $this->capacityPeopleTotal = 0;
        $this->capacityPeopleLoaded = 0;
        $this->sBonusCapacity = 1;
        $this->shipCount = 0;
        $this->distance = 0;
        $this->res = array(0, 0, 0, 0, 0, 0);
        $this->fetch = array(0, 0, 0, 0, 0, 0, 0);
        $this->costs = 0;
        $this->costsFood = 0;
        $this->costsPower = 0;
        $this->supportTime = 0;
        $this->supportCostsFood = 0;
        $this->supportCostsFuel = 0;
        $this->supportCostsFuelPerSec = 0;
        $this->supportCostsFoodPerSec = 0;
        $this->leaderId = 0;
        $this->fakeId = 0;

        $this->shipActions = array();

        $this->havenOk = false;
        $this->shipsFixed = false;
        $this->targetOk = false;
        $this->actionOk = false;

        $this->error = "";
        $this->entityResourceLogStart = $this->sourceEntity->getResourceLog();

        //Create targetentity
        if (isset($_SESSION['haven']['targetId'])) {
            $this->targetEntity = Entity::createFactoryById($_SESSION['haven']['targetId']);
        } elseif (isset($_SESSION['haven']['cellTargetId'])) {
            $this->targetEntity = Entity::createFactoryUnkownCell($_SESSION['haven']['cellTargetId']);
        }

        //Wormhole enable?
        /** @var TechnologyRepository $technologyRepository */
        $technologyRepository = $app[TechnologyRepository::class];
        $this->wormholeEnable = $technologyRepository->getTechnologyLevel((int) $this->ownerId, TechnologyId::WORMHOLE) > 0;
    }

    //
    // Main workflow
    //

    /**
     * Checks main conditions on source planet and
     * returns true if they are ok.
     * The conditions are: Disabled flightban, enabled fleetcontrol, a free fleet slot
     *
     * >> Step 2 <<
     */
    function checkHaven()
    {
        global $app;

        $this->havenOk = false;

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        // Check if flights are possible
        if (
            !$config->getBoolean('flightban')
            || $config->param1Int('flightban_time') > time()
            || $config->param2Int('flightban_time') < time()
        ) {

            if ($config->getBoolean('emp_action')) {
                $action = 3;
            } else {
                $action = 2;
            }

            /** @var BuildingRepository $buildingRepository */
            $buildingRepository = $app[BuildingRepository::class];
            $fleetControl = $buildingRepository->getEntityBuilding((int) $this->ownerId, (int) $this->sourceEntity->id(), BuildingId::FLEET_CONTROL);

            // Check if haven is out of order
            if (null === $fleetControl || $fleetControl->currentLevel === 0) {
                $this->error = "Der Raumschiffhafen ist noch nicht gebaut.";
            } elseif ($fleetControl->isDeactivated()) {
                $this->error = "Dieser Raumschiffhafen ist bis " . StringUtils::formatDate($fleetControl->deactivated) . " deaktiviert.";
            } else {
                /** @var FleetRepository $fleetRepository */
                $fleetRepository = $app[FleetRepository::class];
                $this->fleetSlotsUsed = $fleetRepository->count(FleetSearch::create()->user($this->ownerId)->controlledByEntity($this->sourceEntity->id()));

                $this->fleetControlLevel = $fleetControl->currentLevel;
                $totalSlots = self::FLEET_NOCONTROL_NUM + $this->fleetControlLevel;

                /** @var SpecialistService $specialistService */
                $specialistService = $app[SpecialistService::class];
                $specialist = $specialistService->getSpecialistOfUser($this->ownerId);
                if ($specialist !== null) {
                    $totalSlots += $specialist->fleetMax;
                }

                $this->possibleFleetStarts = $totalSlots - $this->fleetSlotsUsed;

                if ($this->possibleFleetStarts > 0) {
                    // Piloten
                    $this->pilotsAvailable = max(0, floor($this->sourceEntity->people() - $buildingRepository->getPeopleWorking($this->sourceEntity->id())->total));

                    $this->havenOk = true;
                } else {
                    $this->error = "Von hier können keine weiteren Flotten starten, alle Slots (" . $totalSlots . ") sind belegt!";
                }
            }
        } else {
            $this->error = "Wegen einer Flottensperre können bis " . StringUtils::formatDate($config->param2Int('flightban_time')) . " keine Flotten gestartet werden! " . $config->param1('flightban');
        }
        return $this->havenOk;
    }

    /**
     * Adds $cnt items of ship $sid to the fleet.
     * Returns the effective number of added ships or false if no ship
     * of that type was on the source entity
     *
     * >> Step 3 <<
     */
    function addShip($sid, $cnt)
    {
        global $app;

        if ($this->havenOk) {
            if (!$this->shipsFixed) {
                /** @var ShipRequirementRepository $shipRequirementRepository */
                $shipRequirementRepository = $app[ShipRequirementRepository::class];
                /** @var TechnologyRepository $technologyRepository */
                $technologyRepository = $app[TechnologyRepository::class];
                /** @var ShipRepository $shipListRepository */
                $shipListRepository = $app[ShipRepository::class];
                $shipItems = $shipListRepository->findForUser($this->ownerId(), $this->sourceEntity->id(), [$sid]);
                if (count($shipItems) === 1 && $shipItems[0]->count > 0) {
                    $shipItem = $shipItems[0];

                    /** @var ShipDataRepository $shipDataRepository */
                    $shipDataRepository = $app[ShipDataRepository::class];
                    $ship = $shipDataRepository->getShip($sid, false);

                    /** @var SpecialistService $specialistService */
                    $specialistService = $app[SpecialistService::class];
                    $specialist = $specialistService->getSpecialistOfUser($this->ownerId);

                    $timefactor = $this->raceSpeedFactor() + ($specialist !== null ? $specialist->fleetSpeed : 1) - 1;

                    $requirements = $shipRequirementRepository->getRequiredSpeedTechnologies($sid);
                    if (count($requirements) > 0) {
                        $technologyLevels = $technologyRepository->getTechnologyLevels($this->ownerId());
                        foreach ($requirements as $requirement) {
                            $level = $technologyLevels[$requirement->id] ?? 0;
                            if ($level - $requirement->requiredLevel <= 0) {
                                $timefactor += 0;
                            } else {
                                $timefactor += max(0, ($level - $requirement->requiredLevel) * 0.1);
                            }
                        }
                    }
                    $cnt = min(StringUtils::parseFormattedNumber($cnt), $shipItem->count);

                    $this->ships[$sid] = array(
                        "count" => $cnt,
                        "speed" => ($ship->speed / FLEET_FACTOR_F) * $timefactor,
                        "fuel_use" => $ship->fuelUse * $cnt,
                        "fake" => strpos($ship->actions, "fakeattack"),
                        "name" => $ship->name,
                        "pilots" => $ship->pilots * $cnt,
                        "special" => $ship->special,
                        "actions" => array_filter(explode(",", $ship->actions)),
                        'item' => $shipItem,
                    );

                    if ($ship->special) {
                        $this->sBonusSpeed += $shipItem->specialShipBonusSpeed * $ship->specialBonusSpeed;
                        $this->sBonusReadiness += $shipItem->specialShipBonusReadiness * $ship->specialBonusReadiness;
                        $this->sBonusPilots = max(0, $this->sBonusPilots - $shipItem->specialShipBonusPilots * $ship->specialBonusPilots);
                        $this->sBonusCapacity += $shipItem->specialShipBonusCapacity * $ship->specialBonusCapacity;
                    }

                    $this->shipActions = array_merge($this->shipActions, explode(",", $ship->actions));
                    $this->shipActions = array_unique($this->shipActions);

                    // Set global speed
                    if ($this->speed <= 0) {
                        $this->speed = ($ship->speed / FLEET_FACTOR_F) * $timefactor;
                    } else {
                        $this->speed = min($this->speed, ($ship->speed / FLEET_FACTOR_F) * $timefactor);
                    }

                    $this->timeLaunchLand = max($this->timeLaunchLand, $ship->timeToLand / FLEET_FACTOR_S + $ship->timeToStart / FLEET_FACTOR_L);
                    $this->costsLaunchLand += 2 * ($ship->fuelUseLaunch + $ship->fuelUseLanding) * $cnt;
                    $this->pilots += $ship->pilots * $cnt;
                    $this->capacityTotal += $ship->capacity * $cnt;
                    $this->capacityPeopleTotal += $ship->peopleCapacity * $cnt;
                    $this->shipCount += $cnt;

                    return $cnt;
                } else
                    $this->error = "Dieses Schiff ist hier nicht vorhanden!";
            } else
                $this->error = "Kann kein Schiff hinzufügen, die Flotte wurde bereits fertig zusammengestellt!";
        } else
            $this->error = "Kann kein Schiff hinzufügen, es liegt noch ein Problem mit der Flottenkontrolle vor.";
        return false;
    }

    /**
     * Fix ships, prevents the user from adding more ships
     * and calculates the final costs per ae
     *
     * >> Step 4 <<
     */
    function fixShips()
    {
        if ($this->shipsFixed) {
            $this->costsPerHundredAE = 0;
            $this->shipsFixed = false;
        }

        if ($this->shipCount > 0) {
            if ($this->pilotsAvailable() >= $this->getPilots()) {
                // Calc Costs for all ships, based on regulated speed
                foreach ($this->ships as $sid => $sd) {
                    $cpae = $sd['fuel_use'] * $this->speed / $sd['speed'];
                    $this->ships[$sid]['costs_per_ae'] = $cpae;
                    $this->costsPerHundredAE += $cpae;
                }
                $this->shipsFixed = true;
                $this->error = "";
                return $this->shipsFixed;
            } else
                $this->error = "Es sind zuwenig Piloten für diese Flotte vorhanden.(" . $this->pilotsAvailable() . " verfügbar, " . $this->getPilots() . " benötigt)";
        } else
            $this->error = "Kann Schiffauswahl nicht fertigstellen, es wurde keine Schiffe zur Flotte hinzugefügt.";
        /*}
        else
            $this->error = "Kann Schiffauswahl nicht fertigstellen, die Flotte wurde bereits fertig zusammengestellt!";*/
        return false;
    }

    /**
     * Set the wormhole entity
     *
     * >> Step 5.1
     */
    function setWormhole(&$ent, $speedPercent = 100)
    {
        if ($this->wormholeEnable) {
            if (is_array($ent->getFleetTargetForwarder())) {
                $this->wormholeEntryEntity = $ent;
                $this->wormholeExitEntity = Entity::createFactoryById($this->wormholeEntryEntity->targetId());
                $this->costsPerHundredAE1 = $this->costsPerHundredAE;
                $this->speed1 = $this->speed;
                $this->duration1 = $this->duration - $this->getTimeLaunchLand();
                $this->speedPercent1 = $this->speedPercent;
                return true;
            } else
                $this->error = "Ungültiges Zielobjekt";
        } else
            $this->error = "Wurmlochforschung noch nicht erforscht";
        return false;
    }

    /**
     * Sets the target entity
     *
     * >> Step 5 <<
     */
    function setTarget(&$ent, $speedPercent = 100)
    {
        global $app;

        /** @var EntityService $entityService */
        $entityService = $app[EntityService::class];

        if ($this->shipsFixed) {
            if ($ent->isValid()) {
                $this->targetEntity = $ent;
                if ($this->wormholeEntryEntity != NULL) {
                    $this->distance = $entityService->distanceByCoords($this->wormholeExitEntity->getEntityCoordinates(), $this->targetEntity->getEntityCoordinates());
                    $this->distance1 = $entityService->distanceByCoords($this->sourceEntity->getEntityCoordinates(), $this->wormholeEntryEntity->getEntityCoordinates());
                } else {
                    $this->distance = $entityService->distanceByCoords($this->sourceEntity->getEntityCoordinates(), $this->targetEntity->getEntityCoordinates());
                    $this->distance1 = 0;
                }

                $this->setSpeedPercent($speedPercent);

                return true;
            } else
                $this->error = "Ungültiges Zielobjekt";
        } else
            $this->error = "Flotte nicht fertig zusammengestellt";
        return false;
    }


    /**
     * Check if fleet can fly to this target
     *
     * >> Step 6 <<
     */
    function checkTarget()
    {
        if ($this->sourceEntity->resFuel() >= $this->getCosts()) {
            if ($this->sourceEntity->resFood() >= $this->getCostsFood()) {
                if ($this->getCapacity() >= 0) {
                    $this->targetOk = true;
                    return $this->targetOk;
                } else
                    $this->error = "Zu wenig Laderaum für soviel Treibstoff und Nahrung (" . StringUtils::formatNumber(abs($this->getCapacity())) . " zuviel)!";
            } else
                $this->error = "Zuwenig Nahrung! " . StringUtils::formatNumber($this->sourceEntity->resFood()) . " t " . ResourceNames::FOOD . " vorhanden, " . StringUtils::formatNumber($this->getCostsFood()) . " t benötigt.";
        } else
            $this->error = "Zuwenig Treibstoff! " . StringUtils::formatNumber($this->sourceEntity->resFuel()) . " t " . ResourceNames::FUEL . " vorhanden, " . StringUtils::formatNumber($this->getCosts()) . " t benötigt.";
        return false;
    }

    /**
     * Set the desired action
     *
     * >> Step 7 <<
     */
    function setAction($actionCode)
    {
        if ($this->targetOk) {
            $actions = $this->getAllowedActions();
            if (isset($actions[$actionCode])) {
                $this->action = $actionCode;

                $this->actionOk = true;
                return true;
            }
        }
        $this->error = "Es befindet sich kein Schiff in der Flotte, welches die Aktion ausführen kann.";
        return false;
    }


    function launch()
    {
        global $app;

        /** @var FleetRepository $fleetRepository */
        $fleetRepository = $app[FleetRepository::class];
        /** @var PlanetRepository $planetRepository */
        $planetRepository = $app[PlanetRepository::class];

        if ($this->actionOk) {
            if ($this->checkHaven()) {
                $time = time();
                $this->landTime = ($time + $this->getDuration());

                // Subtract ships from source
                /** @var ShipRepository $shipRepository */
                $shipRepository = $app[ShipRepository::class];
                $addcnt = 0;
                foreach ($this->ships as $sid => $sda) {
                    $this->ships[$sid]['count'] = $shipRepository->removeShips((int) $sid, (int) $sda['count'], (int) $this->ownerId, (int) $this->sourceEntity->id());
                    $addcnt += $this->ships[$sid]['count'];
                }

                if ($addcnt > 0) {

                    // Load resource (is needed because of the xajax use)
                    // subtracts payload ressources from source
                    $this->finalLoadResource();

                    // Subtract flight and support costs from source
                    $planetRepository->addResources($this->sourceEntity->id(), 0, 0, 0, -$this->getCosts() - $this->getSupportFuel(), -$this->getCostsFood() - $this->getSupportFood(), - ($this->getPilots() + $this->capacityPeopleLoaded));
                    $this->sourceEntity->reloadRes();

                    if ($this->action == "alliance" && $this->leaderId != 0) {
                        $status = 3;
                        $nextId = $this->sourceEntity->ownerAlliance();
                    } elseif ($this->action == "support") {
                        $status = 0;
                        $nextId = $this->sourceEntity->id();
                    } else {
                        $status = 0;
                        $nextId = 0;
                    }

                    // Create fleet record
                    $resources = new BaseResources();
                    $resources->metal = $this->res[1];
                    $resources->crystal = $this->res[2];
                    $resources->plastic = $this->res[3];
                    $resources->fuel = $this->res[4];
                    $resources->food = $this->res[5];
                    $resources->people = $this->capacityPeopleLoaded;

                    $fetch = new BaseResources();
                    $fetch->metal = $this->fetch[1];
                    $fetch->crystal = $this->fetch[2];
                    $fetch->plastic = $this->fetch[3];
                    $fetch->fuel = $this->fetch[4];
                    $fetch->food = $this->fetch[5];
                    $fetch->people = $this->fetch[6];

                    $fid = $fleetRepository->add($this->ownerId, $time, $this->landTime, $this->sourceEntity->id(), $this->targetEntity->id(), $this->action, $status, $resources, $fetch, $this->getPilots(), $this->getCosts(), $this->getCostsFood(), $this->getCostsPower(), $this->leaderId, $nextId, $this->supportTime, $this->supportCostsFuel, $this->supportCostsFood);

                    $shipLog = "";
                    foreach ($this->ships as $sid => $sda) {
                        $shipLog .= $sid . ":" . $sda['count'] . ",";
                        if ($sda['special']) {
                            $fleetRepository->addSpecialShipsToFleet($fid, $sid, $sda['count'], $sda['item']);
                        } elseif ($sda['fake'] !== false) {
                            $fleetRepository->addShipsToFleet($fid, $sid, $sda['count'], $this->fakeId);
                        } else {
                            $fleetRepository->addShipsToFleet($fid, $sid, $sda['count']);
                        }
                    }

                    //add all the cool stuff to the fleetLog
                    $resources = new BaseResources();
                    $resources->metal = $this->res[1];
                    $resources->crystal = $this->res[2];
                    $resources->plastic = $this->res[3];
                    $resources->fuel = $this->res[4];
                    $resources->food = $this->res[5];
                    $resources->people = $this->capacityPeopleLoaded;

                    $fetch = new BaseResources();
                    $fetch->metal = $this->fetch[1];
                    $fetch->crystal = $this->fetch[2];
                    $fetch->plastic = $this->fetch[3];
                    $fetch->fuel = $this->fetch[4];
                    $fetch->food = $this->fetch[5];
                    $fetch->people = $this->fetch[6];

                    /** @var FleetLogRepository $fleetLogRepository */
                    $fleetLogRepository = $app[FleetLogRepository::class];
                    $fleetLogRepository->addLaunch($fid, $this->ownerId, $this->sourceEntity->id, $this->targetEntity->id(), $time, $this->landTime, $this->action, $this->getPilots(), $this->getCosts() + $this->supportCostsFuel, $this->getCostsFood() + $this->supportCostsFood, $resources, $fetch, $shipLog, $this->entityResourceLogStart, $this->sourceEntity->getResourceLog());

                    if ($this->action === \EtoA\Fleet\FleetAction::ALLIANCE && $this->leaderId == 0) {
                        $fleetRepository->markAsLeader($fid, $this->sourceEntity->ownerAlliance());
                    }
                    return $fid;
                } else {
                    $this->error = "Konnte keine Schiffe zur Flotte hinzufügen da keine vorhanden sind!";
                }
            }
        } else {
            $this->error = "Aktion nocht nicht festgelegt!";
        }
        return false;
    }



    //
    // Helpers
    //

    /**
     * Unfixes ships and resets the ships array
     * This can be used in the haven when revising
     * the ship selection
     */
    function resetShips()
    {
        $this->ships = array();
        $this->shipActions = array();
        $this->res = array(0, 0, 0, 0, 0, 0);
        $this->fetch = array(0, 0, 0, 0, 0, 0, 0);
        $this->shipsFixed = false;
        $this->speed = 0;
        $this->duration = 0;
        $this->costsPerHundredAE = 0;
        $this->timeLaunchLand = 0;
        $this->costsLaunchLand = 0;
        $this->pilots = 0;
        $this->capacityTotal = 0;
        $this->capacityResLoaded = 0;
        $this->capacityFuelUsed = 0;
        $this->capacityPeopleTotal = 0;
        $this->capacityPeopleLoaded = 0;
        $this->shipCount = 0;
        $this->distance = 0;
        $this->shipsFixed = false;
        $this->sBonusCapacity = 1;
        $this->sBonusPilots = 1;
        $this->sBonusSpeed = 1;
        $this->sBonusReadiness = 1;
    }

    function unsetWormhole()
    {
        $this->wormholeEntryEntity = NULL;
        $this->wormholeExitEntity = NULL;
        $this->costsPerHundredAE1 = 0;
        $this->speed1 = 0;
        $this->duration1 = 0;
        $this->speedPercent1 = 0;
    }

    /**
     *
     */
    function getAllowedActions()
    {
        global $app;

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];
        /** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
        $allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];

        $this->error = '';

        //$allowed =  ($this->sFleets && count($this->sFleets) && ( $this->leaderId>0 || in_array($this->targetEntity->id,$this->sFleets))) ? true : false;
        $allowed = true;
        // Get possible actions by intersecting ship actions and allowed target actions
        $actions = array_intersect($this->shipActions, $this->targetEntity->allowedFleetActions());
        $actionObjs = array();

        $battleban = false;
        if ($config->getBoolean("battleban") && $config->param1Int("battleban_time") <= time() && $config->param2Int("battleban_time") > time()) {
            $this->error = "Kampfsperre von " . StringUtils::formatDate($config->param1Int("battleban_time")) . " bis " . StringUtils::formatDate($config->param2Int("battleban_time")) . ". " . $config->param1("battleban");
            $battleban = true;
        }

        if ($config->getBoolean("flightban") && $config->param1Int("flightban_time") <= time() && $config->param2Int("flightban_time") > time()) {
            $this->error = "Flottensperre von " . StringUtils::formatDate($config->param1Int("flightban_time")) . " bis " . StringUtils::formatDate($config->param2Int("flightban_time")) . ". " . $config->param1("flightban");
        } else {
            $noobProtectionErrorAdded = false;

            // Test each possible action
            foreach ($actions as $i) {
                // variable to check whether a support overflow error message should be printed
                $supportPossible = true;

                $ai = FleetAction::createFactory($i);

                // Skip this action if it is an alliance action and ABS is disabled
                // and if the owner of the target planet is not the same user (support)
                // or if alliance battle system is only allowed for alliances at war
                // and the source's and target's alliances aren't at war against each other
                if (
                    $this->sourceEntity->ownerId() != $this->targetEntity->ownerId() &&
                    $ai->allianceAction && (
                        // alliance battle system is disabled
                        !$config->getBoolean("abs_enabled") || (
                            // or abs is enabled for alliances at war only
                            $config->param1Boolean("abs_enabled") && (
                                (
                                    // and it is an agressive action
                                    $ai->attitude() == 3 &&
                                    // and the two alliances are not at war against each other
                                    !$allianceDiplomacyRepository->isAtWar($this->sourceEntity->owner->allianceId(), $this->targetEntity->ownerAlliance())) || (
                                    // or it is a defensive action
                                    $ai->attitude() == 1 &&
                                    // and the user's alliance is not at war
                                    !$allianceDiplomacyRepository->isAtWar($this->owner->allianceId())))))
                ) {
                    continue;
                }

                // Permission checks
                if (
                    // Action is allowed if:
                    (
                        // * Source and target are the same and the action allows that
                        ($this->sourceEntity->id() == $this->targetEntity->id() && $ai->allowSourceEntity()) ||
                        // * source and target are different but belong to the same user and the action is possible for the same user (e.g. ok for transport, not ok for attack)
                        ($this->sourceEntity->ownerId() == $this->targetEntity->ownerId() && $this->sourceEntity->id() != $this->targetEntity->id() && $ai->allowOwnEntities()) ||
                        // * source and target are from different users and target belongs to an user (so it's not a nebula for example) and the action allows any other player's planet as target
                        ($this->sourceEntity->ownerId() != $this->targetEntity->ownerId() && $this->targetEntity->ownerId() > 0 && $ai->allowPlayerEntities()) ||
                        // * target doesn't belong to an user and action allows that (e.g. crystal collection from nebulas)
                        ($this->targetEntity->ownerId() == 0 && $ai->allowNpcEntities()) ||
                        // * action allows only same-alliance users and source and target user belong to the same alliance (alliance >0 -> they have an alliance) OR same user for no alliance
                        //   this is used only for support, so in case different user there is also a check whether there are available support slots on the planet (checkDefNum)
                        ($ai->allowAllianceEntities && $this->sourceEntity->ownerAlliance() == $this->targetEntity->ownerAlliance() && ($this->sourceEntity->ownerId() == $this->targetEntity->ownerId() || ($this->sourceEntity->ownerAlliance() > 0 && ($supportPossible = $this->checkDefNum()))))) &&
                    (!$ai->allianceAction || $this->getAllianceSlots() > 0 || $allowed) //this last check, checks for every AllianceAction support, alliance if there is a empty slot
                ) {
                    //Check for exclusive Actions
                    $exclusiceAllowed = true;
                    if ($ai->exclusive()) {
                        foreach ($this->getShips() as $ship) {
                            if (!(in_array($ai->code(), $ship['actions'], true) || $ship['special'])) {
                                $exclusiceAllowed = false;
                                break;
                            }
                        }
                    }
                    if ($exclusiceAllowed) {
                        if ($this->targetEntity->ownerId() > 0) {
                            if (!$this->targetEntity->ownerHoliday() || $ai->allowOnHoliday()) {
                                if ($ai->attitude() > 1) {
                                    if (!$battleban) {
                                        if (
                                            $ai->allowActivePlayerEntities()
                                            || $this->targetEntity->owner->isInactivLong()
                                            || ($this->ownerId == $this->sourceEntity->lastUserCheck())
                                        ) {
                                            if ($this->owner->canAttackPlanet($this->targetEntity)) {
                                                if (strpos($ai, 'Bombardierung')) {
                                                    if ($allianceDiplomacyRepository->isAtWar($this->sourceEntity->owner->allianceId(), $this->targetEntity->ownerAlliance()))
                                                        $actionObjs[$i] = $ai;
                                                } else
                                                    $actionObjs[$i] = $ai;
                                            } else if (!$noobProtectionErrorAdded) {
                                                $this->error .= 'Der Besitzer des Ziels steht unter Anfängerschutz! '
                                                    . 'Die Punkte des Users müssen zwischen ' . (USER_ATTACK_PERCENTAGE * 100) . '% und '
                                                    . (100 / USER_ATTACK_PERCENTAGE) . '% von deinen Punkten liegen.<br />'
                                                    . 'Ausserdem müssen beide Spieler mindestens ' . (USER_ATTACK_MIN_POINTS)
                                                    . ' Punkte haben.<br />';
                                                // only add error message once, not for every action
                                                $noobProtectionErrorAdded = true;
                                            }
                                        } // if ($ai->allowActivePlayerEntities() || ($this->targetEntity->owner->isInactiv() && !$ai->allowActivePlayerEntities()))
                                    } // if (!$battleban)
                                } // if ($ai->attitude() > 1)
                                else {
                                    $actionObjs[$i] = $ai;
                                }
                            } // if (!$this->targetEntity->ownerHoliday() || $ai->allowOnHoliday())
                            else {
                                $this->error .= "Der Besitzer des Ziels ist im Urlaub; viele Aktionen sind deshalb nicht möglich!<br />";
                            }
                        } // if($this->targetEntity->ownerId()>0)
                        else {
                            $actionObjs[$i] = $ai;
                        }
                    } // if ($exclusiceAllowed)
                } // Permission checks
                // print error message if support slots check failed
                if (!$supportPossible) {
                    // Meldung ausgeben, dass Support nicht möglich ist
                    $this->error .= 'Support nicht m&ouml;glich, die Maximalzahl von ' .
                        $config->param1Int('alliance_fleets_max_players') .
                        ' Verteidigern ist auf diesem Planet bereits erreicht.<br />';
                    $supportPossible = true;
                }
            } // foreach ($actions as $i)
        } // else Flottensperre
        //echo dump($actionObjs);
        return $actionObjs;
    }

    function getSpeed()
    {
        return $this->speed * $this->sBonusSpeed * $this->speedPercent / 100;
    }

    function getShips()
    {
        return $this->ships;
    }

    function getCosts()
    {
        $this->costs = ceil($this->costsPerHundredAE / 100 * $this->distance * $this->speedPercent / 100) + ceil($this->costsPerHundredAE1 / 100 * $this->distance1 * $this->speedPercent1 / 100);
        $this->costs += $this->costsLaunchLand;
        $this->capacityFuelUsed = $this->costs;
        return $this->costs;
    }

    function getCostsFood()
    {
        global $app;
        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        $this->costsFood = ceil($this->getPilots() * $config->getInt('people_food_require') / 3600 * $this->getDuration());
        return $this->costsFood;
    }

    function getCostsPower()
    {
        return $this->costsPower;
    }

    function getDuration()
    {
        return $this->duration + $this->duration1;
    }

    function getSpeedPercent()
    {
        return $this->speedPercent;
    }

    function setSpeedPercent($perc)
    {
        $this->speedPercent = max(1, min(100, $perc));
        $this->duration = $this->distance / $this->getSpeed();    // Calculate duration
        $this->duration *= 3600;    // Convert to seconds
        $this->duration += $this->getTimeLaunchLand();    // Add launch and land time
        $this->duration = ceil($this->duration);
    }

    function getCostsPerHundredAE()
    {
        return ceil($this->costsPerHundredAE * $this->speedPercent / 100);
    }

    function getTimeLaunchLand()
    {
        return ceil($this->timeLaunchLand * (2 - $this->sBonusReadiness));
    }

    function getCostsLaunchLand()
    {
        return $this->costsLaunchLand;
    }

    function getCapacity()
    {
        return $this->getTotalCapacity() - $this->capacityResLoaded - $this->capacityFuelUsed - $this->costsFood - $this->supportCostsFood - $this->supportCostsFuel;
    }

    function getTotalCapacity()
    {
        return $this->capacityTotal * $this->sBonusCapacity;
    }

    function getPeopleCapacity()
    {
        return $this->capacityPeopleTotal - $this->capacityPeopleLoaded;
    }

    function getTotalPeopleCapacity()
    {
        return $this->capacityPeopleTotal;
    }

    private function calcResLoaded()
    {
        $this->capacityResLoaded = 0;
        foreach ($this->res as $i) {
            $this->capacityResLoaded += $i;
        }
    }

    function getLoadedRes($id)
    {
        return ($this->res[$id] > 0) ? $this->res[$id] : 0;
    }

    function loadResource($id, $ammount, $finalize = 0)
    {
        // $ammount = max(0,$ammount);
        $this->res[$id] = 0;
        $this->calcResLoaded();
        if ($ammount >= 0) {
            if ($id == 4) {
                $loaded = floor(min($ammount, $this->getCapacity(), $this->sourceEntity->getRes($id) - $this->getSupportFuel() - $this->getCosts()));
            } elseif ($id == 5) {
                $loaded = floor(min($ammount, $this->getCapacity(), $this->sourceEntity->getRes($id) - $this->getSupportFood() - $this->getCostsFood()));
            } else {
                $loaded = floor(min($ammount, $this->getCapacity(), $this->sourceEntity->getRes($id)));
            }
        } else {
            if ($id == 4) {
                $loaded = floor(min($this->getCapacity(), max(0, $this->sourceEntity->getRes($id) + $ammount - $this->getSupportFuel() - $this->getCosts())));
            } elseif ($id == 5) {
                $loaded = floor(min($this->getCapacity(), max(0, $this->sourceEntity->getRes($id) + $ammount - $this->getSupportFood() - $this->getCostsFood())));
            } else {
                $loaded = floor(min($this->getCapacity(), max(0, $this->sourceEntity->getRes($id) + $ammount)));
            }
        }
        $this->res[$id] = $loaded;
        $this->calcResLoaded();

        return $loaded;
    }

    // subtracts the payload ress (not support/flight fuel and food)
    function finalLoadResource()
    {
        global $app;

        /** @var PlanetRepository $planetRepository */
        $planetRepository = $app[PlanetRepository::class];

        $this->sourceEntity->reloadRes();
        $resources = new BaseResources();

        foreach (ResourceNames::NAMES as $rk => $rn) {
            $id = $rk + 1;
            if ($this->res[$id] >= 0) {
                $ammount = $this->res[$id];
            } else {
                if ($id == 4) {
                    $ammount = max(0, $this->sourceEntity->getRes($id) + $this->res[$id] - $this->getSupportFuel() - $this->getCosts());
                } elseif ($id == 5) {
                    $ammount = max(0, $this->sourceEntity->getRes($id) + $this->res[$id] - $this->getSupportFood() - $this->getCostsFood());
                } else
                    $ammount = max(0, $this->sourceEntity->getRes($id) + $this->res[$id]);
            }

            $this->res[$id] = 0;
            $this->calcResLoaded();
            if ($id == 4) {
                $loaded = (int) floor(min($ammount, $this->getCapacity(), $this->sourceEntity->getRes($id) - $this->getSupportFuel() - $this->getCosts()));
            } elseif ($id == 5) {
                $loaded = (int) floor(min($ammount, $this->getCapacity(), $this->sourceEntity->getRes($id) - $this->getSupportFood() - $this->getCostsFood()));
            } else {
                $loaded = (int) floor(min($ammount, $this->getCapacity(), $this->sourceEntity->getRes($id)));
            }
            $this->res[$id] = $loaded;
            $resources->set($rk, $loaded);
        }

        $this->calcResLoaded();

        $planetRepository->removeResources($this->sourceEntity->id(), $resources);
        $this->sourceEntity->reloadRes();
    }

    function loadPeople($ammount)
    {
        $ammount = max(0, $ammount);
        $this->capacityPeopleLoaded = floor(min($ammount, $this->capacityPeopleTotal, ($this->pilotsAvailable() - $this->getPilots())));

        return $this->capacityPeopleLoaded;
    }


    function fetchResource($id, $ammount)
    {
        $ammount = max(0, $ammount);
        $this->fetch[$id] = 0;
        $this->calcResLoaded();
        $loaded = floor($ammount);
        $this->fetch[$id] = $loaded;
        $this->calcResLoaded();

        return $loaded;
    }

    function resetSupport()
    {
        $this->supportTime = 0;
        $this->supportCostsFood = 0;
        $this->supportCostsFuel = 0;
    }

    function getSupportTime()
    {
        return $this->supportTime;
    }

    function setSupportTime($time)
    {
        $this->supportTime = $time;

        $this->supportCostsFood = ceil($time * $this->supportCostsFoodPerSec);
        $this->supportCostsFuel = ceil($time * $this->supportCostsFuelPerSec);
    }

    function getSupportFood()
    {
        return $this->supportCostsFood;
    }

    function getSupportFuel()
    {
        return $this->supportCostsFuel;
    }

    function getSupportMaxTime()
    {
        global $app;
        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        $this->supportCostsFuel = 0;
        $this->supportCostsFood = 0;

        $this->supportCostsFoodPerSec = $this->pilots * $config->getInt('people_food_require') / 36000;
        $this->supportCostsFuelPerSec = $this->costsPerHundredAE * $this->getSpeed() / $this->getSpeedPercent() / 3600000;

        $maxTime = $this->getCapacity() / ($this->supportCostsFuelPerSec + $this->supportCostsFoodPerSec);

        $supportTimeFuel = ($this->sourceEntity->getRes(4) - $this->getLoadedRes(4) - $this->getCosts()) / $this->supportCostsFuelPerSec;

        if ($this->supportCostsFoodPerSec > 0)
            $supportTimeFood = ($this->sourceEntity->getRes(5) - $this->getLoadedRes(5) - $this->getCostsFood()) / $this->supportCostsFoodPerSec;
        else
            $supportTimeFood = $supportTimeFuel;

        if ($supportTimeFuel > 0)
            $maxTime = min($maxTime, min($supportTimeFuel, $supportTimeFood));
        else
            $maxTime = min($maxTime, $supportTimeFood);

        return floor($maxTime);
    }

    function getSupport()
    {
        return "Supportkosten";
    }

    function setLeader($id)
    {
        $this->leaderId = $id;
    }

    function setFakeId($id)
    {
        $this->fakeId = $id;
    }

    function loadAllianceFleets()
    {
        global $app;

        $this->supportedAllianceEntities = array();
        $this->aFleets = array();
        if ($this->sourceEntity->ownerAlliance()) {
            /** @var FleetRepository $fleetRepository */
            $fleetRepository = $app[FleetRepository::class];
            $this->aFleets = array_reverse($fleetRepository->search(FleetSearch::create()->isLeader()->actionIn([\EtoA\Fleet\FleetAction::ALLIANCE])->nextId($this->sourceEntity->ownerAlliance())->status(FleetStatus::DEPARTURE)));

            $this->supportedAllianceEntities = $fleetRepository->getEntityToIds(FleetSearch::create()->actionIn([\EtoA\Fleet\FleetAction::SUPPORT])->statusIn([FleetStatus::DEPARTURE, FleetStatus::WAITING])->allianceId($this->sourceEntity->ownerAlliance()));
        }
    }

    function setAllianceSlots($num)
    {
        $this->allianceSlots = $num + 1;

        $this->loadAllianceFleets();
    }

    function getAllianceSlots()
    {
        if ($this->sourceEntity->ownerAlliance() && isset($this->allianceSlots)) {
            return $this->allianceSlots - count($this->aFleets) - count($this->supportedAllianceEntities);
        }
    }

    // Alliance attack already confirmed
    function checkAttNum($leaderid)
    {
        global $app;
        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        if (!$config->getBoolean('alliance_fleets_max_players')) {
            return true;
        }
        // Check number of users participating in the alliance attack
        /** @var FleetRepository $fleetRepository */
        $fleetRepository = $app[FleetRepository::class];
        $participatingUsers = $fleetRepository->getUserIds(FleetSearch::create()->leader($leaderid));
        if (count($participatingUsers) < $config->param1Int('alliance_fleets_max_players')) {
            return true;
        }

        return in_array((int) $this->ownerId, $participatingUsers, true);
    }

    function checkDefNum()
    {
        global $app;
        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        if (!$config->getBoolean('alliance_fleets_max_players')) {
            return true;
        }

        // check the number of supporters on that planet
        /** @var FleetRepository $fleetRepository */
        $fleetRepository = $app[FleetRepository::class];
        $participatingUsers = $fleetRepository->getUserIds(FleetSearch::create()->actionIn([\EtoA\Fleet\FleetAction::SUPPORT])->statusIn([FleetStatus::DEPARTURE, FleetStatus::WAITING])->entityTo($this->targetEntity->id())->notUser($this->targetEntity->ownerId()));
        // user id is guaranteed to not be the target owner, so the number is reduced
        // by one, because we always have one slot reserved for the planet's owner
        if (count($participatingUsers) < ($config->param1Int('alliance_fleets_max_players') - 1)) {
            return true;
        }
        // if the maximum of user slots is already reached, we check whether there
        // is already a support fleet from the same user

        // if the user already supports this planet with one fleet, he can
        // send even more fleets to support the same planet
        return in_array((int) $this->ownerId, $participatingUsers, true);
    }


    //
    // Getters
    //
    function ownerId()
    {
        return $this->ownerId;
    }
    function error()
    {
        return $this->error;
    }
    function raceSpeedFactor()
    {
        return $this->raceSpeedFactor;
    }
    function pilotsAvailable()
    {
        return $this->pilotsAvailable;
    }
    function possibleFleetStarts()
    {
        return $this->possibleFleetStarts;
    }
    function fleetSlotsUsed()
    {
        return $this->fleetSlotsUsed;
    }
    function fleetControlLevel()
    {
        return $this->fleetControlLevel;
    }

    function getDistance()
    {
        return $this->distance + $this->distance1;
    }

    function getShipCount()
    {
        return $this->shipCount;
    }

    function getPilots()
    {
        return $this->pilots * $this->sBonusPilots;
    }

    function getLeader()
    {
        return $this->leaderId;
    }
}
