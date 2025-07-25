<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Entity\Fleet;
use EtoA\Log\FleetLogRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;

class FleetService
{
    public function __construct(
        private readonly PlanetRepository $planetRepository,
        private readonly EntityRepository $entityRepository,
        private readonly FleetRepository $fleetRepository,
        private readonly ShipRepository $shipRepository,
        private readonly FleetLogRepository $fleetLogRepository
    ) {}

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
        $targetEntity = $this->entityRepository->findIncludeCell($fleet->getEntityTo());
        if ($targetEntity === null || $targetEntity->getCode() !== EntityType::PLANET) {
            throw new InvalidFleetParametersException('Invalid fleet target. Can only land on planets.');
        }

        $planet = $this->planetRepository->find($targetEntity->getId());
        if ($planet->userId == 0) {
            throw new InvalidFleetParametersException('Cannot land on uninhabited planet.');
        }
        if ($fleet->getUser() && $fleet->getUser() != $planet->getUser()) {
            throw new InvalidFleetParametersException('Cannot land foreign fleet on planet.');
        }

        foreach ($this->fleetRepository->findAllShipsInFleet($fleet->id) as $shipEntry) {
            if ($shipEntry->shipId > 0) {
                $this->shipRepository->addShip($shipEntry->shipId, $shipEntry->count, $planet->userId, $planet->id);
            }
            $this->fleetRepository->removeShipsFromFleet($fleet->id, $shipEntry->shipId);
        }

        $this->planetRepository->addResources($planet->id, $fleet->resMetal, $fleet->resCrystal, $fleet->resPlastic, $fleet->resFuel, $fleet->resFood);
        // Note: $fleet->resPower is ignored for now as planets can not store power that way

        $this->planetRepository->addPeople($planet->id, $fleet->pilots + $fleet->resPeople);

        // Add halve of the resources used for the engines to the target,
        // if the action, for example, is colonize or position
        if ($fleet->status == FleetStatus::ARRIVAL->value) {
            $this->planetRepository->addResources($planet->id, 0, 0, 0, $fleet->usageFuel / 2, $fleet->usageFood / 2);
            // Note: $fleet->usagePower is ignored for now as planets can not store power that way
        }

        $this->fleetRepository->remove($fleet);
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
                    if ($fleet->getId() === $fleet->getLeader()->getId()) {
                        if ($alliance) {
                            $fleets = $this->fleetRepository->findBy(['leader'=>$fleet]);
                            foreach ($fleets as $fleetPart) {
                                $this->cancelFlight($fleetPart,false, true);
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
                    $fleet->setLeader(null);
                    $passed = $difftime / $tottime;
                    $returnFactor = 1 - $passed;

                    // Fleet gets unused costs back
                    $fleet->setResFuel($fleet->getResFuel()+(int) ceil($fleet->getUsageFuel() * $returnFactor));
                    $fleet->setResFood($fleet->getResFood()+(int) ceil($fleet->getUsageFood() * $returnFactor));
                    $fleet->setResPower($fleet->getResPower()+(int) ceil($fleet->getUsagePower() * $returnFactor));


                    $fleet->setUsageFuel((int) floor($fleet->getUsageFuel() * $passed));
                    $fleet->setUsageFood((int) floor($fleet->getUsageFood() * $passed));
                    $fleet->setUsagePower((int) floor($fleet->getUsagePower() * $passed));

                    $resourcesEnd = new BaseResources();
                    $resourcesEnd->metal = $fleet->getResMetal();
                    $resourcesEnd->crystal = $fleet->getResCrystal();
                    $resourcesEnd->plastic = $fleet->getResPlastic();
                    $resourcesEnd->fuel = $fleet->getResFuel();
                    $resourcesEnd->food = $fleet->getResFood();
                    $resourcesEnd->people = $fleet->getResPeople();
                    $this->fleetLogRepository->addCancel($fleet->getId(), $fleet->getUser()->getId(), $fleet->getEntityTo()->getId(), $fleet->getEntityFrom()->getId(), $logLaunchTime, $logLandTime, $fleet->getAction(), $fleet->getStatus(), $fleet->getPilots(), $fleet->getUsageFuel(), $fleet->getUsageFood(), $resourceStart, $resourcesEnd);

                    $this->fleetRepository->update($fleet, $fleet->getLaunchTime(), $fleet->getLandTime(), $fleet->getEntityFrom(), $fleet->getEntityTo(), $fleet->getStatus(), $fleet->getLeader(), $resourcesEnd, $fleet->getUsageFuel(), $fleet->getUsageFood());
                    return true;

                } else {
                    return "Abbruch nicht erlaubt!";
                }
            } else
                return "Flotte ist bereits beim Ziel angekommen!";
        } else
            return "Flotte ist bereits auf dem Rückflug!";
    }
}
