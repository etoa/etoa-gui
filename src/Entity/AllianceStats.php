<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AllianceStatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceStatsRepository::class)]
class AllianceStats
{
    #[ORM\Id]
    #[ORM\OneToOne(mappedBy: "allianceStats", targetEntity: Alliance::class)]
    #[ORM\JoinColumn(name: 'alliance_id', referencedColumnName: 'alliance_id')]
    private Alliance $alliance;

    #[ORM\Column]
    private string $allianceTag;

    #[ORM\Column]
    private string $allianceName;

    #[ORM\Column(name: "cnt")]
    private int $count;

    #[ORM\Column]
    private int $points;

    #[ORM\Column(name: "apoints")]
    private int $alliancePoints;

    #[ORM\Column(name: "upoints")]
    private int $userPoints;

    #[ORM\Column(name: "bpoints")]
    private int $buildingPoints;

    #[ORM\Column(name: "tpoints")]
    private int $technologyPoints;

    #[ORM\Column(name: "spoints")]
    private int $shipPoints;

    #[ORM\Column(name: "uavg")]
    private int $userAverage;

    #[ORM\Column(name: "alliance_rank_current")]
    private int $currentRank;

    #[ORM\Column(name: "alliance_rank_last")]
    private int $lastRank;

    public function getAllianceTag(): ?string
    {
        return $this->allianceTag;
    }

    public function setAllianceTag(string $allianceTag): static
    {
        $this->allianceTag = $allianceTag;

        return $this;
    }

    public function getAllianceName(): ?string
    {
        return $this->allianceName;
    }

    public function setAllianceName(string $allianceName): static
    {
        $this->allianceName = $allianceName;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getAlliancePoints(): ?int
    {
        return $this->alliancePoints;
    }

    public function setAlliancePoints(int $alliancePoints): static
    {
        $this->alliancePoints = $alliancePoints;

        return $this;
    }

    public function getUserPoints(): ?int
    {
        return $this->userPoints;
    }

    public function setUserPoints(int $userPoints): static
    {
        $this->userPoints = $userPoints;

        return $this;
    }

    public function getBuildingPoints(): ?int
    {
        return $this->buildingPoints;
    }

    public function setBuildingPoints(int $buildingPoints): static
    {
        $this->buildingPoints = $buildingPoints;

        return $this;
    }

    public function getTechnologyPoints(): ?int
    {
        return $this->technologyPoints;
    }

    public function setTechnologyPoints(int $technologyPoints): static
    {
        $this->technologyPoints = $technologyPoints;

        return $this;
    }

    public function getShipPoints(): ?int
    {
        return $this->shipPoints;
    }

    public function setShipPoints(int $shipPoints): static
    {
        $this->shipPoints = $shipPoints;

        return $this;
    }

    public function getUserAverage(): ?int
    {
        return $this->userAverage;
    }

    public function setUserAverage(int $userAverage): static
    {
        $this->userAverage = $userAverage;

        return $this;
    }

    public function getCurrentRank(): ?int
    {
        return $this->currentRank;
    }

    public function setCurrentRank(int $currentRank): static
    {
        $this->currentRank = $currentRank;

        return $this;
    }

    public function getLastRank(): ?int
    {
        return $this->lastRank;
    }

    public function setLastRank(int $lastRank): static
    {
        $this->lastRank = $lastRank;

        return $this;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }
}
