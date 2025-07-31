<?php

namespace EtoA\Fleet;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Planet;
use EtoA\Entity\ShipListItem;
use EtoA\Entity\User;
use EtoA\Support\StringUtils;
use Symfony\Bundle\SecurityBundle\Security;

class FleetLaunchService
{
    public const FLEET_NOCONTROL_NUM = 1;

    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly FleetRepository $fleetRepository
    )
    {
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
        $this->havenOk = false;

        // Check if flights are possible
        if (
            !$this->configurationService->getBoolean('flightban')
            || $this->configurationService->param1Int('flightban_time') > time()
            || $this->configurationService->param2Int('flightban_time') < time()
        ) {

            /** @var BuildingListItem $fleetControl */
            $fleetControl = $this->buildingListItemRepository->getEntityBuilding($this->owner, $this->sourceEntity, BuildingId::FLEET_CONTROL);
            // Check if haven is out of order
            if (!$fleetControl || $fleetControl->getCurrentLevel() === 0) {
                $this->error = "Der Raumschiffhafen ist noch nicht gebaut.";
            } elseif ($fleetControl->isDeactivated()) {
                $this->error = "Dieser Raumschiffhafen ist bis " . StringUtils::formatDate($fleetControl->getDeactivated()) . " deaktiviert.";
            } else {
                $this->fleetSlotsUsed = $this->fleetRepository->count(['user' => $this->owner, 'entityFrom' => $this->sourceEntity->getEntity()]);

                $this->fleetControlLevel = $fleetControl->getCurrentLevel();
                $totalSlots = self::FLEET_NOCONTROL_NUM + $this->fleetControlLevel;

                $specialist = $this->owner->getSpecialist();
                if ($specialist) {
                    $totalSlots += $specialist->getFleetMax();
                }

                $this->possibleFleetStarts = $totalSlots - $this->fleetSlotsUsed;
                if ($this->possibleFleetStarts > 0) {
                    $this->pilotsAvailable = max(0, floor($this->sourceEntity->getPeople() - $this->buildingListItemRepository->getTotalPeopleWorking($this->sourceEntity)));
                    $this->havenOk = true;
                } else {
                    $this->error = "Von hier können keine weiteren Flotten starten, alle Slots (" . $totalSlots . ") sind belegt!";
                }
            }
        } else {
            $this->error = "Wegen einer Flottensperre können bis " . StringUtils::formatDate($this->configurationService->param2Int('flightban_time')) . " keine Flotten gestartet werden! " . $this->configurationService->param1('flightban');
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
    public function addShip(ShipListItem $shipListItem, int $cnt)
    {
        if ($this->havenOk) {
            if (!$this->shipsFixed) {
                if ($shipListItem->getCount() > 0) {
                    $ship = $shipListItem->getShip();
                    $specialist = $this->owner->getSpecialist();

                    $timefactor = $this->raceSpeedFactor() + ($specialist?$specialist->getFleetSpeed() : 1) - 1;

                    $requirements = $this->shipRequirementRepository->getRequiredSpeedTechnologies($ship);
                    if (count($requirements) > 0) {
                        foreach ($requirements as $requirement) {
                            $level = $this->technologyListItemRepository->findOneBy(['user'=>$this->owner,'technology'=>$requirement->getTech()])?->getCurrentLevel() ?? 0;
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

                    $this->ships[$ship->getId()] = array(
                        "count" => $cnt,
                        "speed" => ($ship->getSpeed() / $factorF) * $timefactor,
                        "fuel_use" => $ship->getFuelUse() * $cnt,
                        "fake" => strpos($ship->getActions(), "fakeattack"),
                        "name" => $ship->getName(),
                        "pilots" => $ship->getPilots() * $cnt,
                        "special" => $ship->isSpecial(),
                        "actions" => array_filter(explode(",", $ship->getActions())),
                        'item' => $shipListItem,
                    );

                    if ($ship->isSpecial()) {
                        $this->sBonusSpeed += $shipListItem->getSpecialShipBonusSpeed() * $ship->getSpecialBonusSpeed();
                        $this->sBonusReadiness += $shipListItem->getSpecialShipBonusReadiness() * $ship->getSpecialBonusReadiness();
                        $this->sBonusPilots = max(0, $this->sBonusPilots - $shipListItem->getSpecialShipBonusPilots() * $ship->getSpecialBonusPilots());
                        $this->sBonusCapacity += $shipListItem->getSpecialShipBonusCapacity() * $ship->getSpecialBonusCapacity();
                    }

                    $this->shipActions = array_merge($this->shipActions, explode(",", $ship->getActions()));
                    $this->shipActions = array_unique($this->shipActions);

                    // Set global speed
                    if ($this->speed <= 0) {
                        $this->speed = ($ship->getSpeed() / $factorF) * $timefactor;
                    } else {
                        $this->speed = min($this->speed, ($ship->getSpeed() / $factorF) * $timefactor);
                    }

                    $this->timeLaunchLand = max($this->timeLaunchLand, $ship->getTimeToLand() / $factorS + $ship->getTimeToStart() / $factorL);
                    $this->costsLaunchLand += 2 * ($ship->getFuelUseLaunch() + $ship->getFuelUseLanding()) * $cnt;
                    $this->pilots += $ship->getPilots() * $cnt;
                    $this->capacityTotal += $ship->getCapacity() * $cnt;
                    $this->capacityPeopleTotal += $ship->getPeopleCapacity() * $cnt;
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
    public function fixShips(): bool
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

        return false;
    }
}