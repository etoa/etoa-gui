<?php

namespace EtoA\Fleet;

use EtoA\Entity\Entity;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class FleetLaunch
{
    //
    // Variable definitions
    //
    private Planet $sourceEntity;
    private ?Entity $targetEntity = null;
    private User $owner;
    private ?Entity $wormholeEntryEntity = null;
    private ?Entity $wormholeExitEntity = null;
    private array $ships = [];
    private int $shipCount = 0;
    private bool $shipsFixed = false;
    private int $speed = 0;
    private int $speed1 = 0;
    private int $speedPercent = 100;
    private int $speedPercent1 = 0;
    private int $duration = 0;
    private int $duration1 = 0;
    private int $costsPerHundredAE = 0;
    private int $costsPerHundredAE1 = 0;
    private int $timeLaunchLand = 0;
    private int $costsLaunchLand = 0;
    private int $pilots = 0;
    private int $capacityTotal = 0;
    private int $capacityFuelUsed = 0;
    private int $capacityPeopleTotal = 0;
    private int $capacityPeopleLoaded = 0;

    private int $distance = 0;
    private int $distance1 = 0;

    private string $action = '';
    private string $error = '';
    private int $sBonusSpeed = 1;
    private bool $wormholeEnable = false;
    public array $aFleets = [];
    private array $supportedAllianceEntities = [];
    private int $allianceSlots = 0;
    private string $entityResourceLogStart = '';
    private int $possibleFleetStarts = 0;
    private int $fleetSlotsUsed = 0;
    private int $fleetControlLevel = 0;
    private int $sBonusReadiness = 1;
    private int $pilotsAvailable = 0;
    private int $capacityResLoaded = 0;
    private int $sBonusPilots = 1;
    private int $sBonusCapacity = 1;
    /**
     * @var array|int[]
     */
    private array $res = [0,0,0,0,0,0];
    /**
     * @var array|int[]
     */
    private array $fetch = [0,0,0,0,0,0];
    private int $costs = 0;
    private int $costsFood = 0;
    private int $costsPower = 0;
    private int $supportTime = 0;
    private int $supportCostsFood = 0;
    private int $supportCostsFuel = 0;
    private int $supportCostsFuelPerSec = 0;
    private int $supportCostsFoodPerSec = 0;
    private int $fakeId = 0;
    private array $shipActions = [];
    private array $factoredShipActions = [];
    /**
     * @var false
     */
    private bool $havenOk = false;
    /**
     * @var false
     */
    private bool $targetOk = false;
    /**
     * @var false
     */
    private bool $actionOk = false;
    private float $capacity = 0;
    private float $totalCapacity = 0;
    private float $peopleCapacity = 0;
    private float $totalPeopleCapacity = 0;
    private int $supportFood = 0;
    private int $supportFuel = 0;
    private string $support = '';
    private ?User $leader = null;


    /**
     * The constructor
     *
     * >> Step 1 <<
     */
    public function __construct(
    )
    {
        /*
        //Create targetentity
        if (isset($_SESSION['haven']['targetId'])) {
            $this->targetEntity = Entity::createFactoryById($_SESSION['haven']['targetId']);
        } elseif (isset($_SESSION['haven']['cellTargetId'])) {
            $this->targetEntity = Entity::createFactoryUnkownCell($_SESSION['haven']['cellTargetId']);
        }
*/
        //Wormhole enable?
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
        $this->ships = array();
        $this->shipActions = array();
        $this->res = array(0, 0, 0, 0, 0, 0);
        $this->fetch = array(0, 0, 0, 0, 0, 0);
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

    public function unsetWormhole(): void
    {
        $this->wormholeEntryEntity = NULL;
        $this->wormholeExitEntity = NULL;
        $this->costsPerHundredAE1 = 0;
        $this->speed1 = 0;
        $this->duration1 = 0;
        $this->speedPercent1 = 0;
    }



    function getSpeed(): float|int
    {
        return $this->speed * $this->sBonusSpeed * $this->speedPercent / 100;
    }

    function getShips(): array
    {
        return $this->ships;
    }

    public function setShips(array $ships): void
    {
        $this->ships = $ships;
    }

    public function setSpeed(int $speed): void
    {
        $this->speed = $speed;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function setCostsPerHundredAE(int $costsPerHundredAE): void
    {
        $this->costsPerHundredAE = $costsPerHundredAE;
    }

    public function setTimeLaunchLand(int $timeLaunchLand): void
    {
        $this->timeLaunchLand = $timeLaunchLand;
    }

    public function setCostsLaunchLand(int $costsLaunchLand): void
    {
        $this->costsLaunchLand = $costsLaunchLand;
    }

    public function setCosts(int $costs): void
    {
        $this->costs = $costs;
    }

    public function setCostsFood(int $costsFood): void
    {
        $this->costsFood = $costsFood;
    }

    public function setCostsPower(int $costsPower): void
    {
        $this->costsPower = $costsPower;
    }

    function getCosts(): int
    {
        $this->costs = ceil($this->costsPerHundredAE / 100 * $this->distance * $this->speedPercent / 100) + ceil($this->costsPerHundredAE1 / 100 * $this->distance1 * $this->speedPercent1 / 100);
        $this->costs += $this->costsLaunchLand;
        $this->capacityFuelUsed = $this->costs;
        return $this->costs;
    }



    function getCostsPower(): int
    {
        return $this->costsPower;
    }

    function getDuration(): int
    {
        return $this->duration + $this->duration1;
    }

    function getSpeedPercent(): int
    {
        return $this->speedPercent;
    }

    function setSpeedPercent($perc): void
    {
        $this->speedPercent = max(1, min(100, $perc));
        $this->duration = $this->distance / $this->getSpeed();    // Calculate duration
        $this->duration *= 3600;    // Convert to seconds
        $this->duration += $this->getTimeLaunchLand();    // Add launch and land time
        $this->duration = ceil($this->duration);
    }

    function getCostsPerHundredAE(): float
    {
        return ceil($this->costsPerHundredAE * $this->speedPercent / 100);
    }

    function getTimeLaunchLand(): float
    {
        return ceil($this->timeLaunchLand * (2 - $this->sBonusReadiness));
    }

    function getCostsLaunchLand(): int
    {
        return $this->costsLaunchLand;
    }

    public function getCapacity(): float|int
    {
        return $this->getTotalCapacity() - $this->capacityResLoaded - $this->capacityFuelUsed - $this->costsFood - $this->supportCostsFood - $this->supportCostsFuel;
    }

    public function setCapacity(float|int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function setPeopleCapacity(float $peopleCapacity): void
    {
        $this->peopleCapacity = $peopleCapacity;
    }

    public function setTotalPeopleCapacity(float $totalPeopleCapacity): void
    {
        $this->totalPeopleCapacity = $totalPeopleCapacity;
    }

    public function setSupportFood(int $supportFood): void
    {
        $this->supportFood = $supportFood;
    }

    public function setSupportFuel(int $supportFuel): void
    {
        $this->supportFuel = $supportFuel;
    }

    public function setSupport(string $support): void
    {
        $this->support = $support;
    }

    public function setTotalCapacity(int $totalCapacity): void
    {
        $this->totalCapacity = $totalCapacity;
    }

    function getTotalCapacity(): float|int
    {
        return $this->capacityTotal * $this->sBonusCapacity;
    }

    function getPeopleCapacity(): int
    {
        return $this->capacityPeopleTotal - $this->capacityPeopleLoaded;
    }

    function getTotalPeopleCapacity(): int
    {
        return $this->capacityPeopleTotal;
    }

    private function calcResLoaded(): void
    {
        $this->capacityResLoaded = 0;
        foreach ($this->res as $i) {
            $this->capacityResLoaded += $i;
        }
    }



    function loadResource($id, $ammount, $finalize = 0): float
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



    function loadPeople($ammount): float|int
    {
        $ammount = max(0, $ammount);
        $this->capacityPeopleLoaded = floor(min($ammount, $this->capacityPeopleTotal, ($this->pilotsAvailable() - $this->getPilots())));

        return $this->capacityPeopleLoaded;
    }


    function fetchResource($id, $ammount): float
    {
        $ammount = max(0, $ammount);
        $this->fetch[$id] = 0;
        $this->calcResLoaded();
        $loaded = floor($ammount);
        $this->fetch[$id] = $loaded;
        $this->calcResLoaded();

        return $loaded;
    }

    function resetSupport(): void
    {
        $this->supportTime = 0;
        $this->supportCostsFood = 0;
        $this->supportCostsFuel = 0;
    }

    function getSupportTime(): int
    {
        return $this->supportTime;
    }

    function setSupportTime($time): void
    {
        $this->supportTime = $time;

        $this->supportCostsFood = ceil($time * $this->supportCostsFoodPerSec);
        $this->supportCostsFuel = ceil($time * $this->supportCostsFuelPerSec);
    }

    function getSupportFood(): int
    {
        return $this->supportCostsFood;
    }

    function getSupportFuel(): int
    {
        return $this->supportCostsFuel;
    }



    function getSupport(): string
    {
        return "Supportkosten";
    }

    function setFakeId($id): void
    {
        $this->fakeId = $id;
    }

    function getAllianceSlots(): int
    {
        if ($this->sourceEntity->getUser()->getAlliance() && isset($this->allianceSlots)) {
            return $this->allianceSlots - count($this->aFleets) - count($this->supportedAllianceEntities);
        }
        return 0;
    }

    function setAllianceSlots(int $slots): void
    {
        $this->allianceSlots = $slots;
    }

    //
    // Getters and Setters
    //

    public function getSourceEntity(): Planet
    {
        return $this->sourceEntity;
    }

    public function setSourceEntity(Planet $sourceEntity): void
    {
        $this->sourceEntity = $sourceEntity;
    }

    public function getTargetEntity(): ?Entity
    {
        return $this->targetEntity;
    }

    public function setTargetEntity(?Entity $targetEntity): void
    {
        $this->targetEntity = $targetEntity;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    public function getWormholeEntryEntity(): ?Entity
    {
        return $this->wormholeEntryEntity;
    }

    public function setWormholeEntryEntity(?Entity $wormholeEntryEntity): void
    {
        $this->wormholeEntryEntity = $wormholeEntryEntity;
    }

    public function getWormholeExitEntity(): ?Entity
    {
        return $this->wormholeExitEntity;
    }

    public function setWormholeExitEntity(?Entity $wormholeExitEntity): void
    {
        $this->wormholeExitEntity = $wormholeExitEntity;
    }

    public function getShipCount(): int
    {
        return $this->shipCount;
    }

    public function setShipCount(int $shipCount): void
    {
        $this->shipCount = $shipCount;
    }

    public function isShipsFixed(): bool
    {
        return $this->shipsFixed;
    }

    public function setShipsFixed(bool $shipsFixed): void
    {
        $this->shipsFixed = $shipsFixed;
    }

    public function getSpeed1(): int
    {
        return $this->speed1;
    }

    public function setSpeed1(int $speed1): void
    {
        $this->speed1 = $speed1;
    }

    public function getSpeedPercent1(): int
    {
        return $this->speedPercent1;
    }

    public function setSpeedPercent1(int $speedPercent1): void
    {
        $this->speedPercent1 = $speedPercent1;
    }

    public function getDuration1(): int
    {
        return $this->duration1;
    }

    public function setDuration1(int $duration1): void
    {
        $this->duration1 = $duration1;
    }

    public function getCostsPerHundredAE1(): int
    {
        return $this->costsPerHundredAE1;
    }

    public function setCostsPerHundredAE1(int $costsPerHundredAE1): void
    {
        $this->costsPerHundredAE1 = $costsPerHundredAE1;
    }

    public function getPilots(): int
    {
        return $this->pilots;
    }

    public function setPilots(int $pilots): void
    {
        $this->pilots = $pilots;
    }

    public function getCapacityTotal(): int
    {
        return $this->capacityTotal;
    }

    public function setCapacityTotal(int $capacityTotal): void
    {
        $this->capacityTotal = $capacityTotal;
    }

    public function getCapacityFuelUsed(): int
    {
        return $this->capacityFuelUsed;
    }

    public function setCapacityFuelUsed(int $capacityFuelUsed): void
    {
        $this->capacityFuelUsed = $capacityFuelUsed;
    }

    public function getCapacityPeopleTotal(): int
    {
        return $this->capacityPeopleTotal;
    }

    public function setCapacityPeopleTotal(int $capacityPeopleTotal): void
    {
        $this->capacityPeopleTotal = $capacityPeopleTotal;
    }

    public function getCapacityPeopleLoaded(): int
    {
        return $this->capacityPeopleLoaded;
    }

    public function setCapacityPeopleLoaded(int $capacityPeopleLoaded): void
    {
        $this->capacityPeopleLoaded = $capacityPeopleLoaded;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): void
    {
        $this->distance = $distance;
    }

    public function getDistance1(): int
    {
        return $this->distance1;
    }

    public function setDistance1(int $distance1): void
    {
        $this->distance1 = $distance1;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function getSBonusSpeed(): int
    {
        return $this->sBonusSpeed;
    }

    public function setSBonusSpeed(int $sBonusSpeed): void
    {
        $this->sBonusSpeed = $sBonusSpeed;
    }

    public function isWormholeEnable(): bool
    {
        return $this->wormholeEnable;
    }

    public function setWormholeEnable(bool $wormholeEnable): void
    {
        $this->wormholeEnable = $wormholeEnable;
    }

    public function getAFleets(): array
    {
        return $this->aFleets;
    }

    public function setAFleets(array $aFleets): void
    {
        $this->aFleets = $aFleets;
    }

    public function getSupportedAllianceEntities(): array
    {
        return $this->supportedAllianceEntities;
    }

    public function setSupportedAllianceEntities(array $supportedAllianceEntities): void
    {
        $this->supportedAllianceEntities = $supportedAllianceEntities;
    }

    public function getEntityResourceLogStart(): string
    {
        return $this->entityResourceLogStart;
    }

    public function setEntityResourceLogStart(string $entityResourceLogStart): void
    {
        $this->entityResourceLogStart = $entityResourceLogStart;
    }

    public function getPossibleFleetStarts(): int
    {
        return $this->possibleFleetStarts;
    }

    public function setPossibleFleetStarts(int $possibleFleetStarts): void
    {
        $this->possibleFleetStarts = $possibleFleetStarts;
    }

    public function getFleetSlotsUsed(): int
    {
        return $this->fleetSlotsUsed;
    }

    public function setFleetSlotsUsed(int $fleetSlotsUsed): void
    {
        $this->fleetSlotsUsed = $fleetSlotsUsed;
    }

    public function getFleetControlLevel(): int
    {
        return $this->fleetControlLevel;
    }

    public function setFleetControlLevel(int $fleetControlLevel): void
    {
        $this->fleetControlLevel = $fleetControlLevel;
    }

    public function getSBonusReadiness(): int
    {
        return $this->sBonusReadiness;
    }

    public function setSBonusReadiness(int $sBonusReadiness): void
    {
        $this->sBonusReadiness = $sBonusReadiness;
    }

    public function getPilotsAvailable(): int
    {
        return $this->pilotsAvailable;
    }

    public function setPilotsAvailable(int $pilotsAvailable): void
    {
        $this->pilotsAvailable = $pilotsAvailable;
    }

    public function getCapacityResLoaded(): int
    {
        return $this->capacityResLoaded;
    }

    public function setCapacityResLoaded(int $capacityResLoaded): void
    {
        $this->capacityResLoaded = $capacityResLoaded;
    }

    public function getSBonusPilots(): int
    {
        return $this->sBonusPilots;
    }

    public function setSBonusPilots(int $sBonusPilots): void
    {
        $this->sBonusPilots = $sBonusPilots;
    }

    public function getSBonusCapacity(): int
    {
        return $this->sBonusCapacity;
    }

    public function setSBonusCapacity(int $sBonusCapacity): void
    {
        $this->sBonusCapacity = $sBonusCapacity;
    }

    public function getRes(): array
    {
        return $this->res;
    }

    public function setRes(array $res): void
    {
        $this->res = $res;
    }

    public function getFetch(): array
    {
        return $this->fetch;
    }

    public function setFetch(array $fetch): void
    {
        $this->fetch = $fetch;
    }

    public function getSupportCostsFood(): int
    {
        return $this->supportCostsFood;
    }

    public function setSupportCostsFood(int $supportCostsFood): void
    {
        $this->supportCostsFood = $supportCostsFood;
    }

    public function getSupportCostsFuel(): int
    {
        return $this->supportCostsFuel;
    }

    public function setSupportCostsFuel(int $supportCostsFuel): void
    {
        $this->supportCostsFuel = $supportCostsFuel;
    }

    public function getSupportCostsFuelPerSec(): int
    {
        return $this->supportCostsFuelPerSec;
    }

    public function setSupportCostsFuelPerSec(int $supportCostsFuelPerSec): void
    {
        $this->supportCostsFuelPerSec = $supportCostsFuelPerSec;
    }

    public function getSupportCostsFoodPerSec(): int
    {
        return $this->supportCostsFoodPerSec;
    }

    public function setSupportCostsFoodPerSec(int $supportCostsFoodPerSec): void
    {
        $this->supportCostsFoodPerSec = $supportCostsFoodPerSec;
    }

    public function getShipActions(): array
    {
        return $this->shipActions;
    }

    public function setShipActions(array $shipActions): void
    {
        $this->shipActions = $shipActions;
    }

    public function isHavenOk(): bool
    {
        return $this->havenOk;
    }

    public function setHavenOk(bool $havenOk): void
    {
        $this->havenOk = $havenOk;
    }

    public function isTargetOk(): bool
    {
        return $this->targetOk;
    }

    public function setTargetOk(bool $targetOk): void
    {
        $this->targetOk = $targetOk;
    }

    public function isActionOk(): bool
    {
        return $this->actionOk;
    }

    public function setActionOk(bool $actionOk): void
    {
        $this->actionOk = $actionOk;
    }

    public function getFactoredShipActions(): array
    {
        return $this->factoredShipActions;
    }

    public function setFactoredShipActions(array $factoredShipActions): void
    {
        $this->factoredShipActions = $factoredShipActions;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getCostsFood(): int
    {
        return $this->costsFood;
    }

    public function getFakeId(): int
    {
        return $this->fakeId;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getLeader(): ?User
    {
        return $this->leader;
    }

    public function setLeader(?User $leader): void
    {
        $this->leader = $leader;
    }
}