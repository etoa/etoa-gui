<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use ShipList;

class FleetService
{
    private PlanetRepository $planetRepository;
    private EntityRepository $entityRepository;
    private FleetRepository $fleetRepository;

    public function __construct(
        PlanetRepository $planetRepository,
        EntityRepository $entityRepository,
        FleetRepository $fleetRepository
    ) {
        $this->planetRepository = $planetRepository;
        $this->entityRepository = $entityRepository;
        $this->fleetRepository = $fleetRepository;
    }

    public function cancel(int $fleetId, bool $returning = false): void
    {
        $fleet = $this->fleetRepository->find($fleetId);
        if ($fleet === null) {
            throw new InvalidFleetParametersException('Invalid fleet.');
        }

        if ($fleet->status != FleetStatus::DEPARTURE) {
            throw new InvalidFleetParametersException('Cannot return or cancel non-departing fleet.');
        }

        $now = time();
        $timeFlown = $now - $fleet->launchTime;
        $landtime = $now + $timeFlown;
        $status = $returning ? FleetStatus::ARRIVAL : FleetStatus::CANCELLED;

        $this->fleetRepository->update($fleetId, $now, $landtime, $fleet->entityTo, $fleet->entityFrom, $status);
    }

    public function land(int $fleetId): void
    {
        $fleet = $this->fleetRepository->find($fleetId);
        if ($fleet === null) {
            throw new InvalidFleetParametersException('Invalid fleet.');
        }

        $targetEntity = $this->entityRepository->findIncludeCell($fleet->entityTo);
        if ($targetEntity === null || $targetEntity->code !== EntityType::PLANET) {
            throw new InvalidFleetParametersException('Invalid fleet target. Can only land on planets.');
        }

        $planet = $this->planetRepository->find($targetEntity->id);
        if ($planet->userId == 0) {
            throw new InvalidFleetParametersException('Cannot land on uninhabited planet.');
        }
        if ($fleet->userId != 0 && $fleet->userId != $planet->userId) {
            throw new InvalidFleetParametersException('Cannot land foreign fleet on planet.');
        }

        $sl = new ShipList($planet->id, $planet->userId);
        foreach ($this->fleetRepository->findAllShipsInFleet($fleet->id) as $shipEntry) {
            if ($shipEntry->shipId > 0) {
                $sl->add($shipEntry->shipId, $shipEntry->count);
            }
            $this->fleetRepository->removeShipsFromFleet($fleet->id, $shipEntry->shipId);
        }

        $this->planetRepository->addResources($planet->id, $fleet->resMetal, $fleet->resCrystal, $fleet->resPlastic, $fleet->resFuel, $fleet->resFood);
        // Note: $fleet->resPower is ignored for now as planets can not store power that way

        $this->planetRepository->addPeople($planet->id, $fleet->pilots + $fleet->resPeople);

        // Add halve of the resources used for the engines to the target,
        // if the action, for example, is colonize or position
        if ($fleet->status == FleetStatus::ARRIVAL) {
            $this->planetRepository->addResources($planet->id, 0, 0, 0, $fleet->usageFuel / 2, $fleet->usageFood / 2);
            // Note: $fleet->usagePower is ignored for now as planets can not store power that way
        }

        $this->fleetRepository->remove($fleetId);
    }
}
