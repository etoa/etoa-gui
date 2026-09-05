<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Entity\Fleet;
use EtoA\Entity\Planet;
use EtoA\Log\FleetLogRepository;
use EtoA\Ship\ShipId;
use EtoA\Ship\ShipListRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserPropertiesRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FleetService
{
    public function __construct(
        private readonly PlanetRepository         $planetRepository,
        private readonly EntityRepository         $entityRepository,
        private readonly FleetRepository          $fleetRepository,
        private readonly ShipListRepository       $shipListRepository,
        private readonly FleetLogRepository       $fleetLogRepository,
        private readonly FleetShipRepository      $fleetShipRepository,
        private readonly RequestStack             $requestStack,
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly FleetLaunchService       $fleetLaunchService,
        private readonly CellRepository           $cellRepository,
        private readonly UrlGeneratorInterface    $router,
    )
    {
    }

    public function cancel(Fleet $fleet, bool $returning = false): void
    {
        if ($fleet->getStatus() != FleetStatus::DEPARTURE->value) {
            throw new InvalidFleetParametersException('Cannot return or cancel non-departing fleet.');
        }

        $now = time();
        $timeFlown = $now - $fleet->getLaunchTime();
        $landtime = $now + $timeFlown;
        $status = $returning ? FleetStatus::ARRIVAL->value : FleetStatus::CANCELLED->value;

        $this->fleetRepository->update($fleet, $now, $landtime, $fleet->getEntityTo(), $fleet->getEntityFrom(), $status);
    }

    public function land(Fleet $fleet): void
    {
        $targetEntity = $fleet->getEntityTo();
        if ($targetEntity === null || $targetEntity->getCode() !== EntityType::PLANET) {
            throw new InvalidFleetParametersException('Invalid fleet target. Can only land on planets.');
        }

        $planet = $targetEntity->getPlanet();
        if (!$planet->getUser()) {
            throw new InvalidFleetParametersException('Cannot land on uninhabited planet.');
        }
        if ($fleet->getUser() && $fleet->getUser() !== $planet->getUser()) {
            throw new InvalidFleetParametersException('Cannot land foreign fleet on planet.');
        }

        foreach ($fleet->getFleetShips() as $shipEntry) {
            $this->shipListRepository->addShip($shipEntry->getShip(), $shipEntry->getCount(), $planet->getUser(), $planet);
            $this->fleetShipRepository->removeShipsFromFleet($fleet, $shipEntry->getShip());
        }

        $this->planetRepository->addResources($planet, $fleet->getResMetal(), $fleet->getResCrystal(), $fleet->getResPlastic(), $fleet->getResFuel(), $fleet->getResFood());
        // Note: $fleet->resPower is ignored for now as planets can not store power that way

        $this->planetRepository->addPeople($planet, $fleet->getPilots() + $fleet->getResPeople());

        // Add half of the resources used for the engines to the target,
        // if the action, for example, is colonizing or position
        if ($fleet->getStatus() == FleetStatus::ARRIVAL->value) {
            $this->planetRepository->addResources($planet, 0, 0, 0, $fleet->getUsageFuel() / 2, $fleet->getUsageFood() / 2);
            // Note: $fleet->usagePower is ignored for now as planets can not store power that way
        }

        $this->fleetRepository->remove($fleet);
        $this->fleetRepository->save();
    }

    /**
     * Cancels the flight, this means that it sets on a
     * return course with the cancelled status flag enabled.
     * This is only possible if the fleet hasn't reached it's destination
     */
    public function cancelFlight(Fleet $fleet, $alliance = false, $is_child = false): bool|string
    {
        if ($fleet->getStatus() == 0 || $fleet->getStatus() == 3) {
            if ($fleet->getLandTime() > time() || $is_child) {
                if ($fleet->getFleetAction()->cancelable()) {
                    if ($fleet->getLeader() !== null && $fleet->getId() === $fleet->getLeader()->getId()) {
                        if ($alliance) {
                            $fleets = $this->fleetRepository->findBy(['leader' => $fleet]);
                            foreach ($fleets as $fleetPart) {
                                $this->cancelFlight($fleetPart, false, true);
                            }
                        } else {
                            $allianceFleets = $this->fleetRepository->search(FleetSearch::create()->leader($fleet->getId())->nextId($fleet->getNextId())->status(FleetStatus::WAITING->value));
                            if (count($allianceFleets) > 0) {
                                $newLeaderFleet = $allianceFleets[0];
                                $this->fleetRepository->promoteNewAllianceFleetLeader($newLeaderFleet, $fleet, $fleet->getLandTime());
                            }
                        }
                    }

                    $resourceStart = new BaseResources();
                    $resourceStart->metal = $fleet->getResMetal();
                    $resourceStart->crystal = $fleet->getResCrystal();
                    $resourceStart->plastic = $fleet->getResPlastic();
                    $resourceStart->fuel = $fleet->getResFuel();
                    $resourceStart->food = $fleet->getResFood();
                    $resourceStart->people = $fleet->getResPeople();
                    $logLaunchTime = $fleet->getLaunchTime();
                    $logLandTime = $fleet->getLandTime();

                    // ### STATUS ###
                    // 0: Hinflug
                    // 1: Rückflug
                    // 2: Abgebrochen
                    // 3: Supporting

                    $time = time();
                    // how long is the fleet already flying
                    $difftime = 0; //time() - $this->launchTime;
                    // what is the total flight time (one-way plus supporting time)
                    $tottime = 0; //$this->landTime() - $this->launchTime + $this->nextActionTime;

                    // status 3 => supporting at target
                    if ($fleet->getAction() === FleetAction::SUPPORT && $fleet->getStatus() === 3) {
                        // time supporting plus single way from source to target
                        // (which is the same as target to source, thus nextActionTime)
                        $difftime = $time - $fleet->getLaunchTime() + $fleet->getNextActionTime();
                        // total support time plus single way from source to target
                        $tottime = $fleet->getLandTime() - $fleet->getLaunchTime() + $fleet->getNextActionTime();

                        $fleet->setLaunchTime($time);
                        $fleet->setLandTime($time + $fleet->getNextActionTime());

                        $fleet->setEntityTo($this->entityRepository->find($fleet->getNextId()));

                        $this->fleetRepository->removeSupportRes($fleet);
                    } else {
                        // how long is the fleet already flying on its way to target
                        $difftime = $time - $fleet->getLaunchTime();
                        if ($fleet->getAction() === FleetAction::SUPPORT) // support on its way to target
                        {
                            // total support time plus single way from source to target
                            $tottime = $fleet->getLandTime() - $fleet->getLaunchTime() + $fleet->getNextActionTime();
                            $this->fleetRepository->removeSupportRes($fleet);
                        } else {
                            // single way from source to target
                            $tottime = $fleet->getLandTime() - $fleet->getLaunchTime();
                        }

                        $fleet->setLaunchTime($time);
                        $fleet->setLandTime($time + $difftime);

                        $tmp = $fleet->getEntityTo();
                        $fleet->setEntityTo($fleet->getEntityFrom());
                        $fleet->setEntityFrom($tmp);
                    }

                    $fleet->setStatus(2);
                    // Detach from any alliance/support group it was part of; a fleet flying
                    // home on its own is its own leader (leader_id must never be NULL).
                    $fleet->setLeader($fleet);
                    $passed = $difftime / $tottime;
                    $returnFactor = 1 - $passed;

                    // Fleet gets unused costs back
                    $fleet->setResFuel($fleet->getResFuel() + (int)ceil($fleet->getUsageFuel() * $returnFactor));
                    $fleet->setResFood($fleet->getResFood() + (int)ceil($fleet->getUsageFood() * $returnFactor));
                    $fleet->setResPower($fleet->getResPower() + (int)ceil($fleet->getUsagePower() * $returnFactor));


                    $fleet->setUsageFuel((int)floor($fleet->getUsageFuel() * $passed));
                    $fleet->setUsageFood((int)floor($fleet->getUsageFood() * $passed));
                    $fleet->setUsagePower((int)floor($fleet->getUsagePower() * $passed));

                    $resourcesEnd = new BaseResources();
                    $resourcesEnd->metal = $fleet->getResMetal();
                    $resourcesEnd->crystal = $fleet->getResCrystal();
                    $resourcesEnd->plastic = $fleet->getResPlastic();
                    $resourcesEnd->fuel = $fleet->getResFuel();
                    $resourcesEnd->food = $fleet->getResFood();
                    $resourcesEnd->people = $fleet->getResPeople();
                    $this->fleetLogRepository->addCancel($fleet->getId(), $fleet->getUser()->getId(), $fleet->getEntityTo()->getId(), $fleet->getEntityFrom()->getId(), $logLaunchTime, $logLandTime, $fleet->getAction(), $fleet->getStatus(), $fleet->getPilots(), $fleet->getUsageFuel(), $fleet->getUsageFood(), $resourceStart, $resourcesEnd);

                    $this->fleetRepository->update($fleet, $fleet->getLaunchTime(), $fleet->getLandTime(), $fleet->getEntityFrom(), $fleet->getEntityTo(), $fleet->getStatus(), $resourcesEnd, $fleet->getUsageFuel(), $fleet->getUsageFood());
                    return true;

                } else {
                    return "Abbruch nicht erlaubt!";
                }
            } else
                return "Flotte ist bereits beim Ziel angekommen!";
        } else
            return "Flotte ist bereits auf dem Rückflug!";
    }

    public function launchExplorer(int $cellId): array
    {
        /** @var Planet $cp */
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $user = $cp->getUser();
        $error = true;

        $properties = $this->userPropertiesRepository->getOrCreateProperties($user);

        if ($properties->getExploreShip()) {
            $fleet = new FleetLaunch();
            $this->fleetLaunchService->setFleetLaunch($fleet);
            if ($this->fleetLaunchService->checkHaven()) {
                $item = $this->shipListRepository->findOneBy(['entity' => $cp, 'ship' => $properties->getExploreShip()]);
                if ($probeCount = $this->fleetLaunchService->addShip($item, $properties->getExploreShipCount())) {
                    if ($this->fleetLaunchService->fixShips()) {
                        $tc = $this->cellRepository->find($cellId);
                        if ($tc) {
                            $tce = $this->entityRepository->getEntities($tc);
                            if (isset($tce[0])) {
                                $ent = $tce[0];
                                if ($this->fleetLaunchService->setTarget($ent)) {
                                    if ($this->fleetLaunchService->checkTarget()) {
                                        if ($this->fleetLaunchService->setAction("explore")) {
                                            if ($flObj = $this->fleetLaunchService->launch()) {
                                                $str = "$probeCount Explorer unterwegs. Ankunft in " . StringUtils::formatTimespan($flObj->getRemainingTime());
                                                $error = false;
                                            } else {
                                                $str = $this->fleetLaunchService->error ?: $fleet->getError();
                                            }
                                        } else {
                                            $str = $this->fleetLaunchService->error ?: $fleet->getError();
                                        }
                                    } else {
                                        $str = $this->fleetLaunchService->error ?: $fleet->getError();
                                    }
                                } else {
                                    $str = $this->fleetLaunchService->error ?: $fleet->getError();
                                }
                            } else {
                                $str = "Problem beim Finden des Zielobjekts (Objekt 0)!";
                            }
                        } else {
                            $str = "Problem beim Finden des Zielobjekts (Zelle)!";
                        }
                    } else {
                        $str = $this->fleetLaunchService->error ?: $fleet->getError();
                    }
                } else {
                    $str = "Auf deinem Planeten befinden sich keine Explorer des <a href='" . $this->router->generate('game.config.game') . "'>gewählten</a> Typs!";
                }
            } else {
                $str = $this->fleetLaunchService->error ?: $fleet->getError();
            }
        } else {
            $str = "Du hast noch keinen Standard-Explorer gewählt, überprüfe bitte deine <a href='" . $this->router->generate('game.config.game') . "'>Spieleinstellungen</a>!";
        }
        return ['error' => $error, 'info' => $str];
    }
}
