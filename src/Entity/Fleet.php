<?php

declare(strict_types=1);

namespace EtoA\Entity;

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

    #[ORM\Column(type: "integer")]
    protected int $userId;

    #[ORM\Column(type: "integer")]
    protected int $leaderId;

    #[ORM\Column(type: "integer")]
    protected int $entityFrom;

    #[ORM\Column(type: "integer")]
    protected int $entityTo;

    #[ORM\Column(type: "integer")]
    protected int $nextId;

    #[ORM\Column(type: "integer")]
    protected int $launchTime;

    #[ORM\Column(type: "integer")]
    protected int $landTime;

    #[ORM\Column(type: "integer")]
    protected int $nextActionTime;

    #[ORM\Column(type: "string")]
    protected string $action;

    #[ORM\Column(type: "integer")]
    protected int $status;

    #[ORM\Column(type: "integer")]
    protected int $pilots;

    #[ORM\Column(type: "integer")]
    protected int $usageFuel;

    #[ORM\Column(type: "integer")]
    protected int $usageFood;

    #[ORM\Column(type: "integer")]
    protected int $usagePower;

    #[ORM\Column(type: "integer")]
    protected int $supportUsageFuel;

    #[ORM\Column(type: "integer")]
    protected int $supportUsageFood;

    #[ORM\Column(type: "integer")]
    protected int $resMetal;

    #[ORM\Column(type: "integer")]
    protected int $resCrystal;

    #[ORM\Column(type: "integer")]
    protected int $resPlastic;

    #[ORM\Column(type: "integer")]
    protected int $resFuel;

    #[ORM\Column(type: "integer")]
    protected int $resFood;

    #[ORM\Column(type: "integer")]
    protected int $resPower;

    #[ORM\Column(type: "integer")]
    protected int $resPeople;

    #[ORM\Column(type: "integer")]
    protected int $fetchMetal;

    #[ORM\Column(type: "integer")]
    protected int $fetchCrystal;

    #[ORM\Column(type: "integer")]
    protected int $fetchPlastic;

    #[ORM\Column(type: "integer")]
    protected int $fetchFuel;

    #[ORM\Column(type: "integer")]
    protected int $fetchFood;

    #[ORM\Column(type: "integer")]
    protected int $fetchPower;

    #[ORM\Column(type: "integer")]
    protected int $fetchPeople;

    #[ORM\Column(type: "integer")]
    protected int $flag;

    public function getRemainingTime(): int
    {
        return max(0, $this->landTime - time());
    }

    public function isStatusDeparture(): bool
    {
        return $this->status === FleetStatus::DEPARTURE;
    }

    public static function empty(): Fleet
    {
        return new Fleet();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getLeaderId(): ?int
    {
        return $this->leaderId;
    }

    public function setLeaderId(int $leaderId): static
    {
        $this->leaderId = $leaderId;

        return $this;
    }

    public function getEntityFrom(): ?int
    {
        return $this->entityFrom;
    }

    public function setEntityFrom(int $entityFrom): static
    {
        $this->entityFrom = $entityFrom;

        return $this;
    }

    public function getEntityTo(): ?int
    {
        return $this->entityTo;
    }

    public function setEntityTo(int $entityTo): static
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
}
