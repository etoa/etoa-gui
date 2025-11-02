<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FleetRepository::class)]
class Fleet
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    protected int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'fleets')]
    protected User $user;

    #[ORM\JoinColumn(name: 'leader_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    protected ?Fleet $leader = null;

    #[ORM\JoinColumn(name: 'entity_from', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    protected Entity $entityFrom;

    #[ORM\JoinColumn(name: 'entity_to', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    protected Entity $entityTo;

    #[ORM\Column(type: "integer")]
    protected int $nextId = 0;

    #[ORM\Column(name:"launchtime", type: "integer")]
    protected int $launchTime = 0;

    #[ORM\Column(name:"landtime", type: "integer")]
    protected int $landTime = 0;

    #[ORM\Column(name:"nextactiontime", type: "integer")]
    protected int $nextActionTime = 0;

    #[ORM\Column(type: "string")]
    protected string $action = '';

    #[ORM\Column(type: "integer")]
    protected int $status = 0;

    #[ORM\Column(type: "integer")]
    protected int $pilots = 0;

    #[ORM\Column(type: "integer")]
    protected int $usageFuel = 0;

    #[ORM\Column(type: "integer")]
    protected int $usageFood = 0;

    #[ORM\Column(type: "integer")]
    protected int $usagePower = 0;

    #[ORM\Column(type: "integer")]
    protected int $supportUsageFuel = 0;

    #[ORM\Column(type: "integer")]
    protected int $supportUsageFood = 0;

    #[ORM\Column(type: "integer")]
    protected int $resMetal = 0;

    #[ORM\Column(type: "integer")]
    protected int $resCrystal = 0;

    #[ORM\Column(type: "integer")]
    protected int $resPlastic = 0;

    #[ORM\Column(type: "integer")]
    protected int $resFuel = 0;

    #[ORM\Column(type: "integer")]
    protected int $resFood = 0;

    #[ORM\Column(type: "integer")]
    protected int $resPower = 0;

    #[ORM\Column(type: "integer")]
    protected int $resPeople = 0;

    #[ORM\Column(type: "integer")]
    protected int $fetchMetal = 0;

    #[ORM\Column(type: "integer")]
    protected int $fetchCrystal = 0;

    #[ORM\Column(type: "integer")]
    protected int $fetchPlastic = 0;

    #[ORM\Column(type: "integer")]
    protected int $fetchFuel = 0;

    #[ORM\Column(type: "integer")]
    protected int $fetchFood = 0;

    #[ORM\Column(type: "integer")]
    protected int $fetchPower = 0;

    #[ORM\Column(type: "integer")]
    protected int $fetchPeople = 0;

    #[ORM\Column(type: "integer")]
    protected int $flag = 0;

    protected int $peopleCapacity = 0;

    protected int $capacity = 0;

    #[ORM\OneToMany(mappedBy: 'fleet', targetEntity: FleetShip::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $fleetShips;

    protected FleetAction $fleetAction;

    /**
     * @var Collection<int, Ship>
     */
    #[ORM\JoinTable(name: 'fleet_ships')]
    #[ORM\JoinColumn(name: 'fs_fleet_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'fs_ship_id', referencedColumnName: 'ship_id')]
    #[ORM\ManyToMany(targetEntity: Ship::class)]
    protected Collection $ships;

    public function __construct()
    {
        $this->ships = new ArrayCollection();
        $this->fleetShips = new ArrayCollection();
    }

    public function getStatusCode():string
    {
        return FleetAction::$statusCode[$this->status];
    }

    public function getFleetAction():FleetAction
    {
        return FleetAction::createFactory($this->getAction());
    }

    public function getRemainingTime(): int
    {
        return max(0, $this->landTime - time());
    }

    public function isStatusDeparture(): bool
    {
        return $this->status === FleetStatus::DEPARTURE->value;
    }

    public static function empty(): Fleet
    {
        return new Fleet();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityFrom(): ?Entity
    {
        return $this->entityFrom;
    }

    public function setEntityFrom(Entity $entityFrom): static
    {
        $this->entityFrom = $entityFrom;

        return $this;
    }

    public function getEntityTo(): ?Entity
    {
        return $this->entityTo;
    }

    public function setEntityTo(Entity $entityTo): static
    {
        $this->entityTo = $entityTo;

        return $this;
    }

    public function getNextId(): ?int
    {
        return $this->nextId;
    }

    public function setNextId(int $nextId): static
    {
        $this->nextId = $nextId;

        return $this;
    }

    public function getLaunchTime(): ?int
    {
        return $this->launchTime;
    }

    public function setLaunchTime(int $launchTime): static
    {
        $this->launchTime = $launchTime;

        return $this;
    }

    public function getLandTime(): ?int
    {
        return $this->landTime;
    }

    public function setLandTime(int $landTime): static
    {
        $this->landTime = $landTime;

        return $this;
    }

    public function getNextActionTime(): ?int
    {
        return $this->nextActionTime;
    }

    public function setNextActionTime(int $nextActionTime): static
    {
        $this->nextActionTime = $nextActionTime;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPilots(): ?int
    {
        return $this->pilots;
    }

    public function setPilots(int $pilots): static
    {
        $this->pilots = $pilots;

        return $this;
    }

    public function getUsageFuel(): ?int
    {
        return $this->usageFuel;
    }

    public function setUsageFuel(int $usageFuel): static
    {
        $this->usageFuel = $usageFuel;

        return $this;
    }

    public function getUsageFood(): ?int
    {
        return $this->usageFood;
    }

    public function setUsageFood(int $usageFood): static
    {
        $this->usageFood = $usageFood;

        return $this;
    }

    public function getUsagePower(): ?int
    {
        return $this->usagePower;
    }

    public function setUsagePower(int $usagePower): static
    {
        $this->usagePower = $usagePower;

        return $this;
    }

    public function getSupportUsageFuel(): ?int
    {
        return $this->supportUsageFuel;
    }

    public function setSupportUsageFuel(int $supportUsageFuel): static
    {
        $this->supportUsageFuel = $supportUsageFuel;

        return $this;
    }

    public function getSupportUsageFood(): ?int
    {
        return $this->supportUsageFood;
    }

    public function setSupportUsageFood(int $supportUsageFood): static
    {
        $this->supportUsageFood = $supportUsageFood;

        return $this;
    }

    public function getResMetal(): ?int
    {
        return $this->resMetal;
    }

    public function setResMetal(int $resMetal): static
    {
        $this->resMetal = $resMetal;

        return $this;
    }

    public function getResCrystal(): ?int
    {
        return $this->resCrystal;
    }

    public function setResCrystal(int $resCrystal): static
    {
        $this->resCrystal = $resCrystal;

        return $this;
    }

    public function getResPlastic(): ?int
    {
        return $this->resPlastic;
    }

    public function setResPlastic(int $resPlastic): static
    {
        $this->resPlastic = $resPlastic;

        return $this;
    }

    public function getResFuel(): ?int
    {
        return $this->resFuel;
    }

    public function setResFuel(int $resFuel): static
    {
        $this->resFuel = $resFuel;

        return $this;
    }

    public function getResFood(): ?int
    {
        return $this->resFood;
    }

    public function setResFood(int $resFood): static
    {
        $this->resFood = $resFood;

        return $this;
    }

    public function getResPower(): ?int
    {
        return $this->resPower;
    }

    public function setResPower(int $resPower): static
    {
        $this->resPower = $resPower;

        return $this;
    }

    public function getResPeople(): ?int
    {
        return $this->resPeople;
    }

    public function setResPeople(int $resPeople): static
    {
        $this->resPeople = $resPeople;

        return $this;
    }

    public function getFetchMetal(): ?int
    {
        return $this->fetchMetal;
    }

    public function setFetchMetal(int $fetchMetal): static
    {
        $this->fetchMetal = $fetchMetal;

        return $this;
    }

    public function getFetchCrystal(): ?int
    {
        return $this->fetchCrystal;
    }

    public function setFetchCrystal(int $fetchCrystal): static
    {
        $this->fetchCrystal = $fetchCrystal;

        return $this;
    }

    public function getFetchPlastic(): ?int
    {
        return $this->fetchPlastic;
    }

    public function setFetchPlastic(int $fetchPlastic): static
    {
        $this->fetchPlastic = $fetchPlastic;

        return $this;
    }

    public function getFetchFuel(): ?int
    {
        return $this->fetchFuel;
    }

    public function setFetchFuel(int $fetchFuel): static
    {
        $this->fetchFuel = $fetchFuel;

        return $this;
    }

    public function getFetchFood(): ?int
    {
        return $this->fetchFood;
    }

    public function setFetchFood(int $fetchFood): static
    {
        $this->fetchFood = $fetchFood;

        return $this;
    }

    public function getFetchPower(): ?int
    {
        return $this->fetchPower;
    }

    public function setFetchPower(int $fetchPower): static
    {
        $this->fetchPower = $fetchPower;

        return $this;
    }

    public function getFetchPeople(): ?int
    {
        return $this->fetchPeople;
    }

    public function setFetchPeople(int $fetchPeople): static
    {
        $this->fetchPeople = $fetchPeople;

        return $this;
    }

    public function getFlag(): ?int
    {
        return $this->flag;
    }

    public function setFlag(int $flag): static
    {
        $this->flag = $flag;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getLeader(): ?Fleet
    {
        return $this->leader;
    }

    public function setLeader(?Fleet $leader): static
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * @return Collection<int, Ship>
     */
    public function getShips(): Collection
    {
        return $this->ships;
    }

    public function addShip(Ship $ship): static
    {
        if (!$this->ships->contains($ship)) {
            $this->ships->add($ship);
        }

        return $this;
    }

    public function removeShip(Ship $ship): static
    {
        $this->ships->removeElement($ship);

        return $this;
    }

    /**
     * Returns the full passenger capacity
     */
    public function getPeopleCapacity():int
    {
        $this->peopleCapacity = 0;
        foreach ($this->getShips() as $ship) {
            $this->peopleCapacity += $ship->getPeopleCapacity();
        }
        return $this->peopleCapacity;
    }

    /**
     * Returns the free space for passengers
     */
    public function getFreePeopleCapacity():int
    {
        return $this->getPeopleCapacity() - $this->resPeople;
    }

    /**
     * Returns the full storage capacity
     */
    public function getCapacity():float
    {
        $this->capacity = 0;
        $BCapa = 1;
        foreach ($this->getShips() as $ship) {
            $this->capacity += $ship->getCapacity();
            $BCapa += $ship->getSpecialBonusCapacity() * 10;
        }

        return $this->capacity * $BCapa;
    }

    /**
     * Returns the free storage capacity
     */
    public function getFreeCapacity():float
    {
        return $this->getCapacity()
            - $this->usageFuel
            - $this->usageFood
            - $this->usagePower
            - $this->resMetal
            - $this->resCrystal
            - $this->resPlastic
            - $this->resFuel
            - $this->resFood
            - $this->resPower;
    }

    /**
     * @return Collection<int, FleetShip>
     */
    public function getFleetShips(): Collection
    {
        return $this->fleetShips;
    }

    public function addFleetShip(FleetShip $fleetShip): static
    {
        if (!$this->fleetShips->contains($fleetShip)) {
            $this->fleetShips->add($fleetShip);
            $fleetShip->setFleet($this);
        }

        return $this;
    }

    public function removeFleetShip(FleetShip $fleetShip): static
    {
        if ($this->fleetShips->removeElement($fleetShip)) {
            // set the owning side to null (unless already changed)
            if ($fleetShip->getFleet() === $this) {
                $fleetShip->setFleet(null);
            }
        }

        return $this;
    }
}
