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
use EtoA\Entity\ShipListItem;
use EtoA\Log\FleetLogRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class FleetLaunchService
{
    public const FLEET_NOCONTROL_NUM = 1;

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
        private readonly ShipRepository $shipRepository
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
    public function addShip(ShipListItem $shipListItem, int $cnt)
    {
        if ($this->fleetLaunch->isHavenOk()) {
            if (!$this->fleetLaunch->isShipsFixed()) {
                if ($shipListItem->getCount() > 0) {
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
    public function setWormhole(&$ent, $speedPercent = 100)
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
                        $nextId = $this->getFleetLaunch()->getSourceEntity()->getEntity()->getOwner()->getAlliance();
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
                    $fetch->people = $this->getFleetLaunch()->getFetch()[6];

                    $fid = $this->fleetRepository->add($this->getFleetLaunch()->getOwner(), $time, $this->getFleetLaunch()->getTimeLaunchLand(), $this->getFleetLaunch()->getSourceEntity()->getEntity(), $this->getFleetLaunch()->getTargetEntity(), $this->getFleetLaunch()->getAction(), $status, $resources, $fetch, $this->getFleetLaunch()->getPilots(), $this->getFleetLaunch()->getCosts(), $this->getCostsFood(), $this->getFleetLaunch()->getCostsPower(), $this->getFleetLaunch()->getLeader(), $nextId, $this->getFleetLaunch()->getSupportTime(), $this->getFleetLaunch()->getSupportCostsFuel(), $this->getFleetLaunch()->getSupportCostsFood());

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
                    $fetch->people = $this->getFleetLaunch()->getFetch()[6];

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
        // Get possible actions by intersecting ship actions and allowed target actions
        $actions = array_intersect($this->fleetLaunch->getShipActions(), $this->fleetLaunch->getTargetEntity()->getType()->getAllowedFleetActions());
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
                    $ai->allianceAction && (
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
                        ($ai->allowAllianceEntities &&
                            $this->fleetLaunch->getSourceEntity()->getEntity()?->getOwner()?->getAlliance() === $this->fleetLaunch->getTargetEntity()?->getOwner()->getAlliance() &&
                            ($this->fleetLaunch->getSourceEntity()->getEntity()->getOwner() === $this->fleetLaunch->getOwner()
                                || ($this->fleetLaunch->getSourceEntity()->getEntity()->getOwner()->getAlliance()
                                    && ($supportPossible = $this->checkDefNum()))
                            ))) &&
                    (!$ai->allianceAction || $this->fleetLaunch->getAllianceSlots() > 0 || $allowed) //this last check, checks for every AllianceAction support, alliance if there is a empty slot
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
                            if (!$this->fleetLaunch->getOwner()->getHmodTo() || $ai->allowOnHoliday()) {
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

    function getSupportMaxTime(): float
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

    function loadAllianceFleets(): void
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

    function setAllianceSlots($num): void
    {
        $this->fleetLaunch->setAllianceSlots($num + 1);

        $this->loadAllianceFleets();
    }

    // Alliance attack already confirmed
    function checkAttNum($leaderid): bool
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

    function checkDefNum(): bool
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

    /**
    * Verify wormhole and show target selector
    */
    function havenShowWormhole($form)
    {
        // TODO
        global $app;

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];
        /** @var EntityRepository $entityRepository */
        $entityRepository = $app[EntityRepository::class];
        /** @var PlanetRepository $planetRepository */
        $planetRepository = $app[PlanetRepository::class];
        /** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
        $userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];
        /** @var LogRepository $logRepository */
        $logRepository = $app[LogRepository::class];
        /** @var BookmarkRepository $bookmarkRepository */
        $bookmarkRepository = $app[BookmarkRepository::class];

        /** @var UserRepository $userRepository */
        $userRepository = $app[UserRepository::class];

        $response = new xajaxResponse();

        // Do some checks
        if (count($form) > 0) {
            // Get fleet object
            /** @var FleetLaunch $fleet */
            if ($this->fleetLaunch->getWormholeEntryEntity()) {
                $owner = $this->fleetLaunch->getOwner();
                $absX = (($form['csx'] - 1) * $config->param1Int('num_of_cells')) + $form['ccx'];
                $absY = (($form['csy'] - 1) * $config->param2Int('num_of_cells')) + $form['ccy'];
                $code = $userUniverseDiscoveryService->discovered($owner, $absX, $absY) == 0 ? 'u' : '';

                $entity = $entityRepository->findByCoordinates(new EntityCoordinates($form['csx'], $form['csy'], $form['ccx'], $form['ccy'], $form['psp']));
                if ($entity) {
                    //Info Feld des ersten Teiles des Fluges, Tabelle muss vor setWormhole stehen!!
                    ob_start();
                    tableStart("Flug bis zum Wurmloch");
                    echo "<tr><th width=\"25%\"><b>Startplanet:</b></th>
                            <td style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                                <img src=\"" . $fleet->sourceEntity->imagePath() . "\" style=\"float:left;\" >
                                <br/>&nbsp;&nbsp; " . $fleet->sourceEntity . " (" . $fleet->sourceEntity->entityCodeString() . ", Besitzer: " . $fleet->sourceEntity->owner() . ")
                            </td></tr>
                        <tr><th width=\"25%\"><b>Wurmloch-Eintrittspunkt:</b></th>
                            <td style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                                <img src=\"" . $fleet->targetEntity->imagePath() . "\" style=\"float:left;\" >
                                <br/>&nbsp;&nbsp; " . $fleet->targetEntity . " (" . $fleet->targetEntity->entityCodeString() . ", Besitzer: " . $fleet->targetEntity->owner() . ")
                            </td></tr>
                        <tr><th width=\"25%\"><b>Entfernung:</b></th><td>" . StringUtils::formatNumber($fleet->getDistance()) . " AE" . "</td>
                        <tr><th width=\"25%\"><b>Kosten/100 AE:</b></th><td>" . StringUtils::formatNumber($fleet->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL . "</td>";
                    $speedString = StringUtils::formatNumber($fleet->getSpeed()) . " AE/h";
                    if ($fleet->sBonusSpeed > 1)
                        $speedString .= " (inkl. " . StringUtils::formatPercentString($fleet->sBonusSpeed, true) . " Mysticum-Bonus)";
                    echo "<tr><th width=\"25%\"><b>Geschwindigkeit:</b></th><td>" . $speedString . "</td>
                        <tr><th width=\"25%\"><b>Dauer:</b></th><td>" . StringUtils::formatTimespan($fleet->getDuration()) . " (inkl. Start- und Landezeit von " . StringUtils::formatTimespan($fleet->getTimeLaunchLand()) . ")</td>
                        <tr><th width=\"25%\"><b>Treibstoff:</b></th><td>" . StringUtils::formatNumber($fleet->getCosts()) . " t " . ResourceNames::FUEL . "  (inkl. Start- und Landeverbrauch von " . StringUtils::formatNumber($fleet->getCostsLaunchLand()) . " " . ResourceNames::FUEL . ")</td>
                        <tr><th width=\"25%\"><b>Nahrung:</b></th><td>" . StringUtils::formatNumber($fleet->getCostsFood()) . " t " . ResourceNames::FOOD . "</td>
                        <tr><th width=\"25%\"><b>Piloten:</b></th><td>" . StringUtils::formatNumber($fleet->getPilots()) . "</td>";

                    $response->assign("havenContentTarget", "innerHTML", ob_get_contents());

                    ob_end_clean();

                    if ($this->setWormhole($ent, $form['speed'])) {
                        ob_start();
                        echo "<form id=\"targetForm\" onsubmit=\"xajax_havenShowAction(xajax.getFormValues('targetForm'));return false;\" >";
                        tableStart("Zielwahl nach dem Wurmlochsprung wählen");

                        $csx = $this->fleetLaunch->getSourceEntity()->getEntity()->getCell()->getSx();
                        $csy = $this->fleetLaunch->getSourceEntity()->getEntity()->getCell()->getSy();
                        $ccx = $this->fleetLaunch->getSourceEntity()->getEntity()->getCell()->getCx();
                        $ccy = $this->fleetLaunch->getSourceEntity()->getEntity()->getCell()->getCy();
                        $psp = $this->fleetLaunch->getSourceEntity()->getEntity()->getPos();

                        //Wurmlochaustritt
                        echo "<tr><th width=\"25%\"><b>Wurmloch-Austrittspunkt:</b></th>
                                <td style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                                    <img src=\"" . $fleet->wormholeExitEntity->imagePath() . "\" style=\"float:left;\" >
                                    <br/>&nbsp;&nbsp; " . $fleet->wormholeExitEntity . " (" . $fleet->wormholeExitEntity->entityCodeString() . ", Besitzer: " . $fleet->wormholeExitEntity->owner() . ")
                                </td></tr>";
                        // Manuelle Auswahl
                        echo "<tr><th width=\"25%\">Manuelle Eingabe:</th><td width=\"75%\">";
                        echo "<input type=\"text\"
                                                    id=\"man_sx\"
                                                    name=\"man_sx\"
                                                    size=\"1\"
                                                    maxlength=\"1\"
                                                    value=\"$csx\"
                                                    title=\"Sektor X-Koordinate\"
                                                    tabindex=\"1\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t1');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t1')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        />&nbsp;/&nbsp;";
                        echo "<input type=\"text\"
                                                    id=\"man_sy\"
                                                    name=\"man_sy\"
                                                    size=\"1\"
                                                    maxlength=\"1\"
                                                    value=\"$csy\"
                                                    title=\"Sektor Y-Koordinate\"
                                                    tabindex=\"2\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t2');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t2')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        />&nbsp;&nbsp;:&nbsp;&nbsp;";
                        echo "<input type=\"text\"
                                                    id=\"man_cx\"
                                                    name=\"man_cx\"
                                                    size=\"2\"
                                                    maxlength=\"2\"
                                                    value=\"$ccx\"
                                                    title=\"Zelle X-Koordinate\"
                                                    tabindex=\"3\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t3');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t3')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        />&nbsp;/&nbsp;";
                        echo "<input type=\"text\"
                                                    id=\"man_cy\"
                                                    name=\"man_cy\"
                                                    size=\"2\"
                                                    maxlength=\"2\"
                                                    value=\"$ccy\"
                                                    tabindex=\"4\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t4');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t4')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        />&nbsp;&nbsp;:&nbsp;&nbsp;";
                        echo "<input type=\"text\"
                                                    id=\"man_p\"
                                                    name=\"man_p\"
                                                    size=\"2\"
                                                    maxlength=\"2\"
                                                    value=\"$psp\"
                                                    title=\"Position des Planeten im Sonnensystem\"
                                                    tabindex=\"5\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t5');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t5')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        /></td></tr>";

                        echo "<tr id=\"bookmarkselect\"><th width=\"25%\">Zielfavoriten:</th><td width=\"75%\" align=\"left\">";
                        echo "<select name=\"bookmarks\"
                                                id=\"bookmarks\"
                                                onchange=\"showLoader('submitbutton');xajax_havenBookmark(xajax.getFormValues('targetForm'));\"
                                                tabindex=\"6\"
                                >\n";
                        echo "<option value=\"0\"";
                        echo ">Wählen...</option>";

                        $userPlanets = $planetRepository->getUserPlanetsWithCoordinates($fleet->ownerId());
                        foreach ($userPlanets as $userPlanet) {
                            echo "<option value=\"" . $userPlanet->id . "\"";
                            echo ">Eigener Planet: " . $userPlanet->toString() . "</option>\n";
                        }

                        $bookmarkedEntities = $bookmarkRepository->getBookmarkedEntities($fleet->ownerId());
                        if (count($bookmarkedEntities) > 0) {
                            echo "<option value=\"0\"";
                            echo ">-------------------------------</option>\n";

                            foreach ($bookmarkedEntities as $bookmarkedEntity) {
                                echo "<option value=\"" . $bookmarkedEntity->id . "\"";
                                echo ">" . $bookmarkedEntity->toString() . "</option>\n";
                            }
                        }
                        echo "</select>";

                        echo "</td></tr>";

                        // Speedfaktor
                        echo "<tr id=\"speedselect\">
                            <th width=\"25%\">Speedfaktor:</th>
                            <td width=\"75%\" align=\"left\">";
                        echo "<select name=\"speed_percent\"
                                                id=\"duration_percent\"
                                                onchange=\"showLoader('submitbutton');showLoader('duration');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
                                                tabindex=\"6\"
                                >\n";
                        for ($x = 100; $x > 0; $x -= 1) {
                            echo "<option value=\"$x\"";
                            if ($fleet->getSpeedPercent() == $x) echo " selected=\"selected\"";
                            echo ">" . $x . "</option>\n";
                        }
                        echo "</select> %";

                        echo "</td></tr>";

                        // Daten anzeigen
                        echo "<tr><th id=\"targettitle\" width=\"25%\"><b>Ziel-Informationen:</b></th>
                            <td id=\"targetinfo\" style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                                <img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...
                            </td></tr>";
                        echo "<tr><th>Entfernung:</th>
                            <td id=\"distance\">-</td></tr>";
                        echo "<tr><th width=\"25%\">Kosten/100 AE:</th>
                            <td id=\"costae\">" . StringUtils::formatNumber($fleet->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL . "</td></tr>";
                        echo "<tr><th>Geschwindigkeit:</th>
                            <td id=\"speed\">" . StringUtils::formatNumber($fleet->getSpeed()) . " AE/h";
                        if ($fleet->sBonusSpeed > 1)
                            echo " (inkl. " . StringUtils::formatPercentString($fleet->sBonusSpeed, true) . " Mysticum-Bonus)";
                        echo "</td></tr>";
                        echo "<tr><th>Dauer:</th>
                            <td><span id=\"duration\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landezeit von " . StringUtils::formatTimespan($fleet->getTimeLaunchLand()) . ")</td></tr>";
                        echo "<tr><th>Treibstoff:</th>
                            <td><span id=\"costs\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landeverbrauch von " . StringUtils::formatNumber($fleet->getCostsLaunchLand()) . " " . ResourceNames::FUEL . ")</td></tr>";
                        echo "<tr><th>Nahrung:</th>
                            <td><span id=\"food\"  style=\"font-weight:bold;\">-</span></td></tr>";
                        echo "<tr><th>Piloten:</th>
                            <td>" . StringUtils::formatNumber($fleet->getPilots());
                        if ($fleet->sBonusPilots != 1)
                            echo " (inkl. " . StringUtils::formatPercentString($fleet->sBonusPilots, true, true) . " Mysticum-Bonus)";
                        echo "</td></tr>";
                        echo "<tr><th>Bemerkungen:</th>
                            <td id=\"comment\">-</td></tr>";
                        echo "<tr id=\"allianceAttacks\" style=\"display: none;\"><th>Allianzangriffe:</th><td id=\"alliance\">-</td></tr>";
                        tableEnd();

                        echo "<div id=\"submitbutton\"></div>
                                </form>";


                        $response->assign("havenContentWormhole", "innerHTML", ob_get_contents());
                        $response->assign("havenContentWormhole", "style.display", '');

                        $response->script("document.getElementById('man_sx').focus();");
                        $response->script("xajax_havenTargetInfo(xajax.getFormValues('targetForm'))");

                        ob_end_clean();
                    } else {
                        $response->alert($fleet->error());
                    }
                } else {
                    $response->alert("Ungültiges Ziel!");
                }
            } else {
                include_once(getcwd() . '/inc/bootstrap.inc.php');
                $logRepository->add(
                    LogFacility::ILLEGALACTION,
                    LogSeverity::INFO,
                    'Der User ' . $_SESSION['user_nick'] . ' versuchte, ein zweites Wurmloch zu &ouml;ffnen' . "\n"
                    . 'Bereits gesetztes Wurmloch: ' . $fleet->wormholeEntryEntity . ' mit Austrittspunkt ' . $fleet->wormholeExitEntity . "\n"
                    . 'Zweites Wumloch: ' . $form['man_sx'] . ' / ' . $form['man_sy'] . ' : ' . $form['man_cx'] . ' / ' . $form['man_cy'] . ' : ' . $form['man_p'] . '.'
                );
                $response->alert("Wurmloch wurde bereits gesetzt!");
            }


            $_SESSION['haven']['fleetObj'] = serialize($fleet);
        } else {
            $response->alert("Fehler! Es wurden keine Ziel gewählt!");
        }
        return $response;
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