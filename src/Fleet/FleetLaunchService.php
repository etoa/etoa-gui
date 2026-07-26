<?php

namespace EtoA\Fleet;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Entity;
use EtoA\Entity\Fleet;
use EtoA\Entity\Planet;
use EtoA\Entity\Ship;
use EtoA\Entity\ShipListItem;
use EtoA\Entity\User;
use EtoA\Entity\Wormhole;
use EtoA\Log\FleetLogRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class FleetLaunchService
{
    public const FLEET_NOCONTROL_NUM = 1;

    public string $error = '';

    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly FleetRepository $fleetRepository,
        private FleetLaunch $fleetLaunch,
        private readonly Security $security,
        private readonly ShipRequirementRepository $shipRequirementRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly RequestStack $requestStack,
        private readonly PlanetRepository $planetRepository,
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly EntityService $entityService,
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly UserService $userService,
        private readonly ShipListRepository $shipListRepository,
        private readonly FleetShipRepository $fleetShipRepository,
        private readonly FleetLogRepository $fleetLogRepository,
        private readonly ShipRepository $shipRepository,
        private readonly PlanetService $planetService
    )
    {
    }

    public function getFleetLaunch():FleetLaunch
    {
        return $this->fleetLaunch;
    }

    public function setFleetLaunch(FleetLaunch $fleetLaunch): void
    {
        $this->fleetLaunch = $fleetLaunch;
    }

    /**
     * Checks main conditions on source planet and
     * returns true if they are ok.
     * The conditions are: Disabled flightban, enabled fleetcontrol, a free fleet slot
     *
     * >> Step 2 <<
     */
    public function checkHaven():bool
    {
        $this->fleetLaunch->setHavenOk(false);
        $this->fleetLaunch->setOwner($this->security->getUser()->getData());

        $request = $this->requestStack->getCurrentRequest();

        /** @var Planet $sourceEnt */
        $sourceEnt = $this->planetRepository->find($request->getSession()->get('cpid'));

        $this->fleetLaunch->setSourceEntity($sourceEnt);
        if(!$this->fleetLaunch->getTargetEntity()) {
            $this->fleetLaunch->setTargetEntity($sourceEnt->getEntity());
        }

        $this->fleetLaunch->setOwner($sourceEnt->getUser());

        //Wormhole enable?
        $this->fleetLaunch->setWormholeEnable($this->technologyListItemRepository->getTechnologyLevel($this->fleetLaunch->getOwner(), TechnologyId::WORMHOLE) > 0);

        if ($this->fleetLaunch->getOwner()->getAlliance() && $this->allianceBuildListRepository->getLevel($this->fleetLaunch->getOwner()->getAlliance(), AllianceBuildingId::MAIN->value) > 0) {
            $flvl = $this->allianceBuildListRepository->getLevel($this->fleetLaunch->getOwner()->getAlliance(), AllianceBuildingId::FLEET_CONTROL->value);
            $this->setAllianceSlots($flvl);
        }

        // Check if flights are possible
        if (
            !$this->configurationService->getBoolean('flightban')
            || $this->configurationService->param1Int('flightban_time') > time()
            || $this->configurationService->param2Int('flightban_time') < time()
        ) {

            /** @var BuildingListItem $fleetControl */
            $fleetControl = $this->buildingListItemRepository->getEntityBuilding($this->fleetLaunch->getOwner(), $this->fleetLaunch->getSourceEntity(), BuildingId::FLEET_CONTROL->value);
            // Check if haven is out of order
            if (!$fleetControl || $fleetControl->getCurrentLevel() === 0) {
                $this->fleetLaunch->setError("Der Raumschiffhafen ist noch nicht gebaut.");
            } elseif ($fleetControl->isDeactivated()) {
                $this->fleetLaunch->setError("Dieser Raumschiffhafen ist bis " . StringUtils::formatDate($fleetControl->getDeactivated()) . " deaktiviert.");
            } else {
                $this->fleetLaunch->setFleetSlotsUsed($this->fleetRepository->count(['user' => $this->fleetLaunch->getOwner(), 'entityFrom' => $this->fleetLaunch->getSourceEntity()->getEntity()]));

                $this->fleetLaunch->setFleetControlLevel($fleetControl->getCurrentLevel());
                $totalSlots = self::FLEET_NOCONTROL_NUM + $this->fleetLaunch->getFleetControlLevel();

                $specialist = $this->fleetLaunch->getOwner()->getSpecialist();
                if ($specialist) {
                    $totalSlots += $specialist->getFleetMax();
                }

                $this->fleetLaunch->setPossibleFleetStarts($totalSlots - $this->fleetLaunch->getFleetSlotsUsed());
                if ($this->fleetLaunch->getPossibleFleetStarts() > 0) {
                    $this->fleetLaunch->setPilotsAvailable(max(0, floor($this->fleetLaunch->getSourceEntity()->getPeople() - $this->buildingListItemRepository->getTotalPeopleWorking($this->fleetLaunch->getSourceEntity()))));
                    $this->fleetLaunch->setHavenOk(true);
                } else {
                    $this->fleetLaunch->setError("Von hier können keine weiteren Flotten starten, alle Slots (" . $totalSlots . ") sind belegt!");
                }
            }
        } else {
            $this->fleetLaunch->setError("Wegen einer Flottensperre können bis " . StringUtils::formatDate($this->configurationService->param2Int('flightban_time')) . " keine Flotten gestartet werden! " . $this->configurationService->param1('flightban'));
        }
        return $this->fleetLaunch->isHavenOk();
    }
    /**
     * Adds $cnt items of ship $sid to the fleet.
     * Returns the effective number of added ships or false if no ship
     * of that type was on the source entity
     *
     * >> Step 3 <<
     */
    public function addShip(?ShipListItem $shipListItem, int $cnt)
    {
        if ($this->fleetLaunch->isHavenOk()) {
            if (!$this->fleetLaunch->isShipsFixed()) {
                if ($shipListItem?->getCount() > 0) {
                    $ship = $shipListItem->getShip();
                    $specialist = $this->fleetLaunch->getOwner()->getSpecialist();

                    $timefactor = $this->fleetLaunch->getOwner()->getRace()->getFleetTime() + ($specialist?$specialist->getFleetSpeed() : 1) - 1;

                    $requirements = $this->shipRequirementRepository->getRequiredSpeedTechnologies($ship);
                    if (count($requirements) > 0) {
                        foreach ($requirements as $requirement) {
                            $level = $this->technologyListItemRepository->findOneBy(['user'=>$this->fleetLaunch->getOwner(),'technology'=>$requirement->getTech()])?->getCurrentLevel() ?? 0;
                            if ($level - $requirement->getLevel() <= 0) {
                                $timefactor += 0;
                            } else {
                                $timefactor += max(0, ($level - $requirement->getLevel()) * 0.1);
                            }
                        }
                    }
                    $cnt = min(StringUtils::parseFormattedNumber($cnt), $shipListItem->getCount());
                    $factorF = $this->configurationService->getFloat('flight_flight_time');
                    $factorS = $this->configurationService->getFloat('flight_start_time');
                    $factorL = $this->configurationService->getFloat('flight_land_time');

                    $ships = $this->fleetLaunch->getShips();
                    $ships[$ship->getId()] = array(
                        "count" => $cnt,
                        "speed" => ($ship->getSpeed() / $factorF) * $timefactor,
                        "fuel_use" => $ship->getFuelUse() * $cnt,
                        "fake" => strpos($ship->getActions(), "fakeattack"),
                        "name" => $ship->getName(),
                        "pilots" => $ship->getPilots() * $cnt,
                        "special" => $ship->isSpecial(),
                        "actions" => array_filter(explode(",", $ship->getActions())),
                    );

                    $this->fleetLaunch->setShips($ships);

                    if ($ship->isSpecial()) {
                        $this->fleetLaunch->setSBonusSpeed($shipListItem->getSpecialShipBonusSpeed() * $ship->getSpecialBonusSpeed()+$this->fleetLaunch->getSBonusSpeed());
                        $this->fleetLaunch->setSBonusReadiness( $shipListItem->getSpecialShipBonusReadiness() * $ship->getSpecialBonusReadiness()+$this->fleetLaunch->getSBonusReadiness());
                        $this->fleetLaunch->setSBonusPilots(max(0, $this->fleetLaunch->getSBonusPilots() - $shipListItem->getSpecialShipBonusPilots() * $ship->getSpecialBonusPilots()));
                        $this->fleetLaunch->setSBonusCapacity($shipListItem->getSpecialShipBonusCapacity() * $ship->getSpecialBonusCapacity()+$this->fleetLaunch->getSBonusCapacity());
                    }

                    $this->fleetLaunch->setShipActions(array_merge($this->fleetLaunch->getShipActions(), explode(",", $ship->getActions())));
                    $this->fleetLaunch->setShipActions(array_unique($this->fleetLaunch->getShipActions()));
                    $this->fleetLaunch->setFactoredShipActions(array_map(fn ($row) => FleetAction::createFactory($row)->__toString(), array_filter(explode(",", $ship->getActions()))));

                    // Set global speed
                    if ($this->fleetLaunch->getSpeed() <= 0) {
                        $this->fleetLaunch->setSpeed(($ship->getSpeed() / $factorF) * $timefactor);
                    } else {
                        $this->fleetLaunch->setSpeed(min($this->fleetLaunch->getSpeed(), ($ship->getSpeed() / $factorF) * $timefactor));
                    }

                    $this->fleetLaunch->setTimeLaunchLand(max($this->fleetLaunch->getTimeLaunchLand(), $ship->getTimeToLand() / $factorS + $ship->getTimeToStart() / $factorL));
                    $this->fleetLaunch->setCostsLaunchLand((2 * ($ship->getFuelUseLaunch() + $ship->getFuelUseLanding()) * $cnt)+$this->fleetLaunch->getCostsLaunchLand());
                    $this->fleetLaunch->setPilots(($ship->getPilots() * $cnt)+$this->fleetLaunch->getPilots());
                    $this->fleetLaunch->setCapacityTotal(($ship->getCapacity() * $cnt)+$this->fleetLaunch->getCapacityTotal());
                    $this->fleetLaunch->setCapacityTotal(($ship->getPeopleCapacity() * $cnt)+$this->fleetLaunch->getCapacityTotal());
                    $this->fleetLaunch->setShipCount($cnt+$this->fleetLaunch->getShipCount());

                    return $cnt;
                } else
                    $this->fleetLaunch->setError("Dieses Schiff ist hier nicht vorhanden!");
            } else
                $this->fleetLaunch->setError("Kann kein Schiff hinzufügen, die Flotte wurde bereits fertig zusammengestellt!");
        } else
            $this->fleetLaunch->setError("Kann kein Schiff hinzufügen, es liegt noch ein Problem mit der Flottenkontrolle vor.");
        return false;
    }

    /**
     * Fix ships, prevents the user from adding more ships
     * and calculates the final costs per ae
     *
     * >> Step 4 <<
     */
    public function fixShips(): bool
    {
        if ($this->fleetLaunch->isShipsFixed()) {
            $this->fleetLaunch->setCostsPerHundredAE(0);
            $this->fleetLaunch->setShipsFixed(false);
        }

        if ($this->fleetLaunch->getShipCount() > 0) {
            if ($this->fleetLaunch->getPilotsAvailable() >= $this->fleetLaunch->getPilots()) {

                // Calc Costs for all ships, based on regulated speed
                foreach ($this->fleetLaunch->getShips() as $sid => $sd) {
                    $cpae = $sd['fuel_use'] * $this->fleetLaunch->getSpeed() / $sd['speed'];

                    $ships = $this->fleetLaunch->getShips();
                    $this->fleetLaunch->setShips(array_replace($ships, [
                        $sid => array_merge($ships[$sid], ['costs_per_ae' => $cpae])
                    ]));

                    $this->fleetLaunch->setCostsPerHundredAE($this->fleetLaunch->getCostsPerHundredAE()+$cpae);
                }
                $this->fleetLaunch->setShipsFixed(true);
                $this->fleetLaunch->setError('');
                return $this->fleetLaunch->isShipsFixed();
            } else
                $this->fleetLaunch->setError("Es sind zuwenig Piloten für diese Flotte vorhanden.(" . $this->fleetLaunch->getPilotsAvailable() . " verfügbar, " . $this->fleetLaunch->getPilots() . " benötigt)");
        } else
            $this->fleetLaunch->setError("Kann Schiffauswahl nicht fertigstellen, es wurde keine Schiffe zur Flotte hinzugefügt.");

        return false;
    }

    /**
     * Set the wormhole entity
     *
     * >> Step 5.1
     */
    public function setWormhole(Entity $ent, int $speedPercent = 100): bool
    {
        if ($this->fleetLaunch->isWormholeEnable()) {
            $type = $ent->getType();
            if ($type instanceof Wormhole && $type->getTarget()?->getEntity() !== null) {
                // Snapshot the first flight leg (source -> wormhole entry) before the
                // parameters get recomputed for the second leg (wormhole exit -> target)
                $this->fleetLaunch->setWormholeEntryEntity($ent);
                $this->fleetLaunch->setWormholeExitEntity($type->getTarget()->getEntity());
                $this->fleetLaunch->setCostsPerHundredAE1((int) $this->fleetLaunch->getRawCostsPerHundredAE());
                $this->fleetLaunch->setSpeed1((int) $this->fleetLaunch->getRawSpeed());
                $this->fleetLaunch->setDuration1((int) ($this->fleetLaunch->getDuration() - $this->fleetLaunch->getTimeLaunchLand()));
                $this->fleetLaunch->setSpeedPercent1($this->fleetLaunch->getSpeedPercent());
                return true;
            }
            $this->fleetLaunch->setError("Ungültiges Zielobjekt");
        } else {
            $this->fleetLaunch->setError("Wurmlochforschung noch nicht erforscht");
        }
        return false;
    }

    /**
     * Sets the target entity
     *
     * >> Step 5 <<
     */
    public function setTarget(Entity $ent, $speedPercent = 100): bool
    {
        if ($this->fleetLaunch->isShipsFixed()) {
            $this->fleetLaunch->setTargetEntity($ent);
            if ($this->fleetLaunch->getWormholeEntryEntity()) {
                $this->fleetLaunch->setDistance($this->entityService->distanceByCoords($this->fleetLaunch->getWormholeExitEntity()->getCoordinates(), $this->fleetLaunch->getTargetEntity()->getCoordinates()));
                $this->fleetLaunch->setDistance1($this->entityService->distanceByCoords($this->fleetLaunch->getSourceEntity()->getEntity()->getCoordinates(), $this->fleetLaunch->getWormholeEntryEntity()->getCoordinates()));
            } else {
                $this->fleetLaunch->setDistance($this->entityService->distanceByCoords($this->fleetLaunch->getSourceEntity()->getEntity()->getCoordinates(), $this->fleetLaunch->getTargetEntity()->getCoordinates()));
                $this->fleetLaunch->setDistance1(0);
            }

            $this->fleetLaunch->setSpeedPercent($speedPercent);

            return true;
        }
        return false;
    }


    /**
     * Check if fleet can fly to this target
     *
     * >> Step 6 <<
     */
    public function checkTarget(): bool
    {
        if ($this->fleetLaunch->getSourceEntity()->getResFuel() >= $this->fleetLaunch->getCosts()) {
            if ($this->fleetLaunch->getSourceEntity()->getResFood() >= $this->fleetLaunch->getCostsFood()) {
                if ($this->fleetLaunch->getCapacity() >= 0) {
                    $this->fleetLaunch->setTargetOk(true);
                    return true;
                } else
                    $this->fleetLaunch->setError("Zu wenig Laderaum für soviel Treibstoff und Nahrung (" . StringUtils::formatNumber(abs($this->fleetLaunch->getCapacity())) . " zuviel)!");
            } else
                $this->fleetLaunch->setError("Zuwenig Nahrung! " . StringUtils::formatNumber($this->fleetLaunch->getSourceEntity()->getResFood()) . " t " . ResourceNames::FOOD . " vorhanden, " . StringUtils::formatNumber($this->getCostsFood()) . " t benötigt.");
        } else
            $this->fleetLaunch->setError("Zuwenig Treibstoff! " . StringUtils::formatNumber($this->fleetLaunch->getSourceEntity()->getResFuel()) . " t " . ResourceNames::FUEL . " vorhanden, " . StringUtils::formatNumber($this->fleetLaunch->getCosts()) . " t benötigt.");
        return false;
    }

    /**
     * Set the desired action
     *
     * >> Step 7 <<
     */
    public function setAction($actionCode): bool
    {
        if ($this->fleetLaunch->isTargetOk()) {
            $actions = $this->getAllowedActions();
            if (isset($actions[$actionCode])) {
                $this->fleetLaunch->setAction($actionCode);

                $this->fleetLaunch->setActionOk(true);
                return true;
            }
        }
        $this->fleetLaunch->setError("Es befindet sich kein Schiff in der Flotte, welches die Aktion ausführen kann.");
        return false;
    }


    public function launch(): bool|Fleet
    {
        if ($this->fleetLaunch->isActionOk()) {
            if ($this->checkHaven()) {
                $time = time();
                $this->getFleetLaunch()->setTimeLaunchLand($time + $this->fleetLaunch->getDuration());

                // Subtract ships from source
                $addcnt = 0;
                foreach ($this->getFleetLaunch()->getShips() as $sid => $sda) {
                    $shipListItem = $this->shipListRepository->findOneBy(['entity'=>$this->getFleetLaunch()->getSourceEntity(),'ship'=>$sid]);
                    $this->fleetLaunch->getShips()[$sid]['count'] = $this->shipListRepository->removeShips($shipListItem, (int) $sda['count']);
                    $addcnt += $this->getFleetLaunch()->getShips()[$sid]['count'];
                }

                if ($addcnt > 0) {

                    // Load resource (is needed because of the xajax use)
                    // subtracts payload ressources from source
                    $this->finalLoadResource();

                    // Subtract flight and support costs from source
                    $this->planetRepository->addResources($this->getFleetLaunch()->getSourceEntity(), 0, 0, 0, -$this->getFleetLaunch()->getCosts() - $this->getFleetLaunch()->getSupportFuel(), -$this->getCostsFood() - $this->fleetLaunch->getSupportFood(), - ($this->getFleetLaunch()->getPilots() + $this->getFleetLaunch()->getCapacityPeopleLoaded()));

                    if ($this->getFleetLaunch()->getAction() === "alliance" && $this->getFleetLaunch()->getLeader()) {
                        $status = 3;
                        $nextId = $this->getFleetLaunch()->getSourceEntity()->getEntity()->getOwner()?->getAlliance()?->getId() ?? 0;
                    } elseif ($this->getFleetLaunch()->getAction() === "support") {
                        $status = 0;
                        $nextId = $this->getFleetLaunch()->getSourceEntity()->getEntity()->getId();
                    } else {
                        $status = 0;
                        $nextId = 0;
                    }

                    // Create fleet record
                    $resources = new BaseResources();
                    $resources->metal = $this->getFleetLaunch()->getRes()[1];
                    $resources->crystal = $this->getFleetLaunch()->getRes()[2];
                    $resources->plastic = $this->getFleetLaunch()->getRes()[3];
                    $resources->fuel = $this->getFleetLaunch()->getRes()[4];
                    $resources->food = $this->getFleetLaunch()->getRes()[5];
                    $resources->people = $this->getFleetLaunch()->getCapacityPeopleLoaded();

                    $fetch = new BaseResources();
                    $fetch->metal = $this->getFleetLaunch()->getFetch()[1];
                    $fetch->crystal = $this->getFleetLaunch()->getFetch()[2];
                    $fetch->plastic = $this->getFleetLaunch()->getFetch()[3];
                    $fetch->fuel = $this->getFleetLaunch()->getFetch()[4];
                    $fetch->food = $this->getFleetLaunch()->getFetch()[5];
                    $fetch->people = $this->getFleetLaunch()->getCapacityPeopleLoaded();

                    // The leader is stored as the leader fleet's id; resolve it to the Fleet entity
                    $leaderFleet = $this->getFleetLaunch()->getLeader() ? $this->fleetRepository->find($this->getFleetLaunch()->getLeader()) : null;

                    $fid = $this->fleetRepository->add($this->getFleetLaunch()->getOwner(), $time, $this->getFleetLaunch()->getTimeLaunchLand(), $this->getFleetLaunch()->getSourceEntity()->getEntity(), $this->getFleetLaunch()->getTargetEntity(), $this->getFleetLaunch()->getAction(), $status, $resources, $fetch, $this->getFleetLaunch()->getPilots(), $this->getFleetLaunch()->getCosts(), $this->getCostsFood(), $this->getFleetLaunch()->getCostsPower(), $leaderFleet, $nextId, $this->getFleetLaunch()->getSupportTime(), $this->getFleetLaunch()->getSupportCostsFuel(), $this->getFleetLaunch()->getSupportCostsFood());

                    $shipLog = "";
                    foreach ($this->getFleetLaunch()->getShips() as $sid => $sda) {
                        $shipLog .= $sid . ":" . $sda['count'] . ",";
                        if ($sda['special']) {
                            $this->fleetShipRepository->addSpecialShipsToFleet($fid, $this->shipRepository->find($sid), $sda['count'], $sda['item']);
                        } elseif ($sda['fake'] !== false) {
                            $this->fleetShipRepository->addShipsToFleet($fid, $this->shipRepository->find($sid), $sda['count'], $this->getFleetLaunch()->getFakeId());
                        } else {
                            $this->fleetShipRepository->addShipsToFleet($fid, $this->shipRepository->find($sid), $sda['count']);
                        }
                    }

                    //add all the cool stuff to the fleetLog
                    $resources = new BaseResources();
                    $resources->metal = $this->getFleetLaunch()->getRes()[1];
                    $resources->crystal = $this->getFleetLaunch()->getRes()[2];
                    $resources->plastic = $this->getFleetLaunch()->getRes()[3];
                    $resources->fuel = $this->getFleetLaunch()->getRes()[4];
                    $resources->food = $this->getFleetLaunch()->getRes()[5];
                    $resources->people = $this->getFleetLaunch()->getCapacityPeopleLoaded();

                    $fetch = new BaseResources();
                    $fetch->metal = $this->getFleetLaunch()->getFetch()[1];
                    $fetch->crystal = $this->getFleetLaunch()->getFetch()[2];
                    $fetch->plastic = $this->getFleetLaunch()->getFetch()[3];
                    $fetch->fuel = $this->getFleetLaunch()->getFetch()[4];
                    $fetch->food = $this->getFleetLaunch()->getFetch()[5];
                    $fetch->people = $this->getFleetLaunch()->getCapacityPeopleLoaded();

                    $this->fleetLogRepository->addLaunch($fid, $this->getFleetLaunch()->getOwner(), $this->getFleetLaunch()->getSourceEntity()->getEntity(), $this->getFleetLaunch()->getTargetEntity(), $time, $this->getFleetLaunch()->getTimeLaunchLand(), $this->getFleetLaunch()->getAction(), $this->getFleetLaunch()->getPilots(), $this->getFleetLaunch()->getCosts() + $this->getFleetLaunch()->getSupportCostsFuel(), $this->getCostsFood() + $this->getFleetLaunch()->getSupportCostsFood(), $resources, $fetch, $shipLog, $this->getFleetLaunch()->getEntityResourceLogStart(), $this->getFleetLaunch()->getSourceEntity()->getResourceLog());

                    if ($this->getFleetLaunch()->getAction() === FleetAction::ALLIANCE && $this->getFleetLaunch()->getLeader() === null) {
                        $this->fleetRepository->markAsLeader($fid, $this->getFleetLaunch()->getSourceEntity()->getUser()->getAlliance()->getId());
                    }
                    return $fid;
                } else {
                    $this->error = "Konnte keine Schiffe zur Flotte hinzufügen da keine vorhanden sind!";
                }
            }
        } else {
            $this->error = "Aktion noch nicht festgelegt!";
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
    public function resetShips(): void
    {
        $this->fleetLaunch->setShips(array());
        $this->fleetLaunch->setShipActions(array());
        $this->fleetLaunch->setRes(array(0, 0, 0, 0, 0, 0));
        $this->fleetLaunch->setFetch(array(0, 0, 0, 0, 0, 0, 0));
        $this->fleetLaunch->setSpeed(0);
        $this->fleetLaunch->setDuration(0);
        $this->fleetLaunch->setCostsPerHundredAE(0);
        $this->fleetLaunch->setTimeLaunchLand(0);
        $this->fleetLaunch->setCostsLaunchLand(0);
        $this->fleetLaunch->setPilots(0) ;
        $this->fleetLaunch->setCapacityTotal(0);
        $this->fleetLaunch->setCapacityResLoaded(0);
        $this->fleetLaunch->setCapacityFuelUsed(0);
        $this->fleetLaunch->setCapacityPeopleTotal(0);
        $this->fleetLaunch->setCapacityPeopleLoaded(0);
        $this->fleetLaunch->setShipCount(0);
        $this->fleetLaunch->setDistance(0);
        $this->fleetLaunch->setShipsFixed(false);
        $this->fleetLaunch->setSBonusCapacity(1);
        $this->fleetLaunch->setSBonusPilots(1);
        $this->fleetLaunch->setSBonusSpeed(1);
        $this->fleetLaunch->setSBonusReadiness(1);
    }

    /**
     *
     */
    public function getAllowedActions(): array
    {
        $this->error = '';
        //$allowed =  ($this->sFleets && count($this->sFleets) && ( $this->leaderId>0 || in_array($this->targetEntity->id,$this->sFleets))) ? true : false;
        $allowed = true;
        // Get possible actions by intersecting ship actions and allowed target actions.
        // A Doctrine-hydrated Planet has no injected PlanetService, so its
        // getAllowedFleetActions() would return an empty list -> ask the service directly.
        $targetType = $this->fleetLaunch->getTargetEntity()->getType();
        $targetActions = $targetType instanceof Planet
            ? $this->planetService->getAllowedFleetActions($targetType)
            : $targetType->getAllowedFleetActions();
        $actions = array_intersect($this->fleetLaunch->getShipActions(), $targetActions);
        $actionObjs = array();
        $battleban = false;
        if ($this->configurationService->getBoolean("battleban") && $this->configurationService->param1Int("battleban_time") <= time() && $this->configurationService->param2Int("battleban_time") > time()) {
            $this->error = "Kampfsperre von " . StringUtils::formatDate($this->configurationService->param1Int("battleban_time")) . " bis " . StringUtils::formatDate($this->configurationService->param2Int("battleban_time")) . ". " . $this->configurationService->param1("battleban");
            $battleban = true;
        }

        if ($this->configurationService->getBoolean("flightban") && $this->configurationService->param1Int("flightban_time") <= time() && $this->configurationService->param2Int("flightban_time") > time()) {
            $this->error = "Flottensperre von " . StringUtils::formatDate($this->configurationService->param1Int("flightban_time")) . " bis " . StringUtils::formatDate($this->configurationService->param2Int("flightban_time")) . ". " . $this->configurationService->param1("flightban");
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
                    $this->fleetLaunch->getSourceEntity()->getUser() !== $this->fleetLaunch->getTargetEntity()->getOwner() &&
                    $ai->allianceAction() && (
                        // alliance battle system is disabled
                        !$this->configurationService->getBoolean("abs_enabled") || (
                            // or abs is enabled for alliances at war only
                            $this->configurationService->param1Boolean("abs_enabled") && (
                                (
                                    // and it is an agressive action
                                    $ai->attitude() == 3 &&
                                    // and the two alliances are not at war against each other
                                    !$this->allianceDiplomacyRepository->isAtWar($this->fleetLaunch->getSourceEntity()->getUser()->getAlliance(), $this->fleetLaunch->getTargetEntity()->getOwner()->getAlliance())) || (
                                    // or it is a defensive action
                                    $ai->attitude() == 1 &&
                                    // and the user's alliance is not at war
                                    !$this->allianceDiplomacyRepository->isAtWar($this->fleetLaunch->getOwner()->getAlliance())))))
                ) {
                    continue;
                }

                // Permission checks
                if (
                    // Action is allowed if:
                    (
                        // * Source and target are the same and the action allows that
                        ($this->fleetLaunch->getSourceEntity() === $this->fleetLaunch->getTargetEntity() && $ai->allowSourceEntity()) ||
                        // * source and target are different but belong to the same user and the action is possible for the same user (e.g. ok for transport, not ok for attack)
                        ($this->fleetLaunch->getSourceEntity()->getEntity()->getOwner() === $this->fleetLaunch->getTargetEntity()->getOwner() && $this->fleetLaunch->getSourceEntity() !== $this->fleetLaunch->getTargetEntity() && $ai->allowOwnEntities()) ||
                        // * source and target are from different users and target belongs to an user (so it's not a nebula for example) and the action allows any other player's planet as target
                        ($this->fleetLaunch->getTargetEntity()->getOwner() && $this->fleetLaunch->getSourceEntity()->getEntity()->getOwner() !== $this->fleetLaunch->getTargetEntity()->getOwner() && $ai->allowPlayerEntities()) ||
                        // * target doesn't belong to an user and action allows that (e.g. crystal collection from nebulas)
                        (!$this->fleetLaunch->getTargetEntity()->getOwner() && $ai->allowNpcEntities()) ||
                        // * action allows only same-alliance users and source and target user belong to the same alliance (alliance >0 -> they have an alliance) OR same user for no alliance
                        //   this is used only for support, so in case different user there is also a check whether there are available support slots on the planet (checkDefNum)
                        ($ai->allowAllianceEntities() &&
                            $this->fleetLaunch->getSourceEntity()->getEntity()?->getOwner()?->getAlliance() === $this->fleetLaunch->getTargetEntity()?->getOwner()->getAlliance() &&
                            ($this->fleetLaunch->getSourceEntity()->getEntity()->getOwner() === $this->fleetLaunch->getOwner()
                                || ($this->fleetLaunch->getSourceEntity()->getEntity()->getOwner()->getAlliance()
                                    && ($supportPossible = $this->checkDefNum()))
                            ))) &&
                    (!$ai->allianceAction() || $this->fleetLaunch->getAllianceSlots() > 0 || $allowed) //this last check, checks for every AllianceAction support, alliance if there is a empty slot
                ) {
                    //Check for exclusive Actions
                    $exclusiceAllowed = true;
                    if ($ai->exclusive()) {
                        foreach ($this->fleetLaunch->getShips() as $ship) {
                            if (!(in_array($ai->code(), $ship['actions'], true) || $ship['special'])) {
                                $exclusiceAllowed = false;
                                break;
                            }
                        }
                    }
                    if ($exclusiceAllowed) {
                        if ($this->fleetLaunch->getTargetEntity()->getOwner()) {
                            if (!$this->fleetLaunch->getTargetEntity()->getOwner()->getHmodTo() || $ai->allowOnHoliday()) {
                                if ($ai->attitude() > 1) {
                                    if (!$battleban) {
                                        if (
                                            $ai->allowActivePlayerEntities()
                                            || $this->fleetLaunch->getTargetEntity()->getOwner()->getLastOnline() < ((time() -  $this->configurationService->param2Int('user_inactive_days')) * 86400)
                                            || ($this->fleetLaunch->getOwner() === $this->fleetLaunch->getSourceEntity()->getLastUser())
                                        ) {
                                            if ($this->userService->canAttackUser($this->fleetLaunch->getTargetEntity()->getOwner())) {
                                                if (strpos($ai, 'Bombardierung')) {
                                                    if ($this->allianceDiplomacyRepository->isAtWar($this->fleetLaunch->getSourceEntity()->getUser()->getAlliance(), $this->fleetLaunch->getTargetEntity()->getOwner()->getAlliance()))
                                                        $actionObjs[$i] = $ai;
                                                } else
                                                    $actionObjs[$i] = $ai;
                                            } else if (!$noobProtectionErrorAdded) {
                                                $this->error .= 'Der Besitzer des Ziels steht unter Anfängerschutz! '
                                                    . 'Die Punkte des Users müssen zwischen ' . ($this->configurationService->getFloat('user_attack_percentage') * 100) . '% und '
                                                    . (100 / $this->configurationService->getFloat('user_attack_percentage')) . '% von deinen Punkten liegen.<br />'
                                                    . 'Ausserdem müssen beide Spieler mindestens ' . ($this->configurationService->getInt('user_attack_min_points'))
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
                        $this->configurationService->param1Int('alliance_fleets_max_players') .
                        ' Verteidigern ist auf diesem Planet bereits erreicht.<br />';
                    $supportPossible = true;
                }
            } // foreach ($actions as $i)
        } // else Flottensperre
        //echo dump($actionObjs);
        return $actionObjs;
    }

    function getCostsFood(): float|int
    {
        return ceil($this->fleetLaunch->getPilots() * $this->configurationService->getInt('people_food_require') / 3600 * $this->fleetLaunch->getDuration());
    }

    /**
     * Computes a ship's effective speed and the bonus breakdown (race, specialist, speed
     * technologies) for the ship-selection tooltip, mirroring the legacy havenShowShips logic.
     *
     * @return array{baseSpeed: float, displaySpeed: float, timefactor: float, lines: array<array{label: string, factor: float}>}
     */
    public function getShipSpeedBreakdown(Ship $ship, User $owner): array
    {
        $specialist = $owner->getSpecialist();
        $raceFactor = $owner->getRace()?->getFleetTime() ?? 1;
        $specFactor = $specialist ? ($specialist->getFleetSpeed() ?? 1) : 1;

        $timefactor = $raceFactor + $specFactor - 1;
        $lines = [];
        if ($raceFactor != 1) {
            $lines[] = ['label' => 'Rasse', 'factor' => $raceFactor];
        }
        if ($specFactor != 1) {
            $lines[] = ['label' => 'Spezialist', 'factor' => $specFactor];
        }

        foreach ($this->shipRequirementRepository->getRequiredSpeedTechnologies($ship) as $requirement) {
            $level = $this->technologyListItemRepository->findOneBy(['user' => $owner, 'technology' => $requirement->getTech()])?->getCurrentLevel() ?? 0;
            $diff = $level - $requirement->getLevel();
            if ($diff > 0) {
                $timefactor += $diff * 0.1;
                $lines[] = [
                    'label' => $requirement->getTech()?->getName() . ' ' . $level,
                    'factor' => ($diff / 10) + 1,
                ];
            }
        }

        $baseSpeed = $ship->getSpeed() / $this->configurationService->getFloat('flight_flight_time');

        return [
            'baseSpeed' => $baseSpeed,
            'displaySpeed' => $baseSpeed * $timefactor,
            'timefactor' => $timefactor,
            'lines' => $lines,
        ];
    }

    // subtracts the payload ress (not support/flight fuel and food)
    function finalLoadResource(): void
    {
        $resources = new BaseResources();

        foreach (ResourceNames::NAMES as $rk => $rn) {
            $id = $rk + 1;
            if ($this->getFleetLaunch()->getRes()[$id] >= 0) {
                $ammount = $this->getFleetLaunch()->getRes()[$id];
            } else {
                switch ($id) {
                    case 4: $ammount = max(0, $this->getFleetLaunch()->getSourceEntity()->getResFuel() + $this->getFleetLaunch()->getRes()[$id] - $this->getFleetLaunch()->getSupportFuel() - $this->getFleetLaunch()->getCosts());break;
                    case 5: $ammount = max(0, $this->getFleetLaunch()->getSourceEntity()->getResFood() + $this->getFleetLaunch()->getRes()[$id] - $this->getFleetLaunch()->getSupportFood() - $this->getCostsFood());break;
                    case 1: $ammount = max(0, $this->getFleetLaunch()->getSourceEntity()->getResMetal() + $this->getFleetLaunch()->getRes()[$id]);break;
                    case 2: $ammount = max(0, $this->getFleetLaunch()->getSourceEntity()->getResCrystal() + $this->getFleetLaunch()->getRes()[$id]);break;
                    case 3: $ammount = max(0, $this->getFleetLaunch()->getSourceEntity()->getResPlastic() + $this->getFleetLaunch()->getRes()[$id]);break;
                }
            }

            $this->calcResLoaded();

            switch ($id) {
                case 4: $loaded = (int) floor(min($ammount, $this->getCapacity(), $this->getFleetLaunch()->getSourceEntity()->getResFuel() - $this->getFleetLaunch()->getSupportFuel() - $this->getFleetLaunch()->getCosts())); break;
                case 5: $loaded = (int) floor(min($ammount, $this->getCapacity(), $this->getFleetLaunch()->getSourceEntity()->getResFuel() - $this->getFleetLaunch()->getSupportFood() - $this->getFleetLaunch()->getCostsFood())); break;
                case 1: $loaded = (int) floor(min($ammount, $this->getCapacity(), $this->getFleetLaunch()->getSourceEntity()->getResMetal()));break;
                case 2: $loaded = (int) floor(min($ammount, $this->getCapacity(), $this->getFleetLaunch()->getSourceEntity()->getResCrystal()));break;
                case 3: $loaded = (int) floor(min($ammount, $this->getCapacity(), $this->getFleetLaunch()->getSourceEntity()->getResPlastic()));break;
            }

            $res = $this->getFleetLaunch()->getRes();
            $res[$id] = $loaded;
            $this->getFleetLaunch()->setRes($res);

            $resources->set($rk, $loaded);
        }

        $this->calcResLoaded();

        $this->planetRepository->removeResources($this->getFleetLaunch()->getSourceEntity(), $resources);
    }

    public function getSupportMaxTime(): float
    {
        $this->fleetLaunch->setSupportCostsFuel(0);
        $this->fleetLaunch->setSupportCostsFood(0);

        $foodPerSec = $this->fleetLaunch->getPilots() * $this->configurationService->getInt('people_food_require') / 36000;
        $fuelPerSec = $this->fleetLaunch->getRawCostsPerHundredAE() * $this->fleetLaunch->getSpeed() / max(1, $this->fleetLaunch->getSpeedPercent()) / 3600000;

        $this->fleetLaunch->setSupportCostsFoodPerSec($foodPerSec);
        $this->fleetLaunch->setSupportCostsFuelPerSec($fuelPerSec);

        if ($fuelPerSec + $foodPerSec <= 0) {
            return 0;
        }

        $maxTime = $this->getCapacity() / ($fuelPerSec + $foodPerSec);

        $supportTimeFuel = $fuelPerSec > 0
            ? ($this->fleetLaunch->getSourceEntity()->getResFuel() - $this->fleetLaunch->getLoadedRes(4) - $this->fleetLaunch->getCosts()) / $fuelPerSec
            : 0;

        if ($foodPerSec > 0) {
            $supportTimeFood = ($this->fleetLaunch->getSourceEntity()->getResFood() - $this->fleetLaunch->getLoadedRes(5) - $this->getCostsFood()) / $foodPerSec;
        } else {
            $supportTimeFood = $supportTimeFuel;
        }

        if ($supportTimeFuel > 0) {
            $maxTime = min($maxTime, min($supportTimeFuel, $supportTimeFood));
        } else {
            $maxTime = min($maxTime, $supportTimeFood);
        }

        return floor($maxTime);
    }

    public function loadAllianceFleets(): void
    {
        $this->fleetLaunch->setSupportedAllianceEntities([]);
        $this->fleetLaunch->setAFleets([]);

        $alliance = $this->fleetLaunch->getSourceEntity()->getUser()?->getAlliance();
        if ($alliance !== null) {
            $this->fleetLaunch->setAFleets(array_reverse($this->fleetRepository->search(
                FleetSearch::create()
                    ->isLeader()
                    ->actionIn([FleetAction::ALLIANCE])
                    ->nextId($alliance->getId())
                    ->status(FleetStatus::DEPARTURE->value)
            )));

            $this->fleetLaunch->setSupportedAllianceEntities($this->fleetRepository->getEntityToIds(
                FleetSearch::create()
                    ->actionIn([FleetAction::SUPPORT])
                    ->statusIn([FleetStatus::DEPARTURE->value, FleetStatus::WAITING->value])
                    ->alliance($alliance)
            ));
        }
    }

    function setAllianceSlots($num): void
    {
        $this->fleetLaunch->setAllianceSlots($num + 1);

        $this->loadAllianceFleets();
    }

    // Alliance attack already confirmed
    public function checkAttNum(int $leaderId): bool
    {
        if (!$this->configurationService->getBoolean('alliance_fleets_max_players')) {
            return true;
        }
        // Check number of users participating in the alliance attack
        $participatingUsers = $this->fleetRepository->getUserIds(FleetSearch::create()->leader($leaderId));
        if (count($participatingUsers) < $this->configurationService->param1Int('alliance_fleets_max_players')) {
            return true;
        }

        return in_array($this->fleetLaunch->getOwner()->getId(), $participatingUsers, true);
    }

    public function checkDefNum(): bool
    {
        if (!$this->configurationService->getBoolean('alliance_fleets_max_players')) {
            return true;
        }

        // check the number of supporters on that planet
        $search = FleetSearch::create()
            ->actionIn([FleetAction::SUPPORT])
            ->statusIn([FleetStatus::DEPARTURE->value, FleetStatus::WAITING->value])
            ->entityTo($this->fleetLaunch->getTargetEntity());

        $targetOwner = $this->fleetLaunch->getTargetEntity()->getOwner();
        if ($targetOwner !== null) {
            $search->notUser($targetOwner);
        }

        $participatingUsers = $this->fleetRepository->getUserIds($search);
        // user id is guaranteed to not be the target owner, so the number is reduced
        // by one, because we always have one slot reserved for the planet's owner
        if (count($participatingUsers) < ($this->configurationService->param1Int('alliance_fleets_max_players') - 1)) {
            return true;
        }
        // if the user already supports this planet with one fleet, he can
        // send even more fleets to support the same planet
        return in_array($this->fleetLaunch->getOwner()->getId(), $participatingUsers, true);
    }

    private function getCapacity(): float|int
    {
        return $this->getFleetLaunch()->getTotalCapacity() - $this->getFleetLaunch()->getCapacityResLoaded() - $this->getFleetLaunch()->getCapacityFuelUsed() - $this->getFleetLaunch()->getCostsFood() - $this->getFleetLaunch()->getSupportCostsFood() - $this->getFleetLaunch()->getSupportCostsFuel();
    }

    private function calcResLoaded(): void
    {
        $capacityResLoaded = 0;
        foreach ($this->getFleetLaunch()->getRes() as $i) {
            $capacityResLoaded += $i;
        }

        $this->getFleetLaunch()->setCapacityResLoaded($capacityResLoaded);
    }
}