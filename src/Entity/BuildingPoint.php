<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Building\BuildingPointRepository;

#[ORM\Entity(repositoryClass: BuildingPointRepository::class)]
#[ORM\Table(name: 'building_points')]
class BuildingPoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "bp_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "bp_building_id", type: "integer")]
    private int $buildingId;

    #[ORM\Column(name: "bp_level", type: "integer")]
    private int $level;

    #[ORM\Column(name: "bp_points", type: "float")]
    private float $points;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuildingId(): ?int
    {
        return $this->buildingId;
    }

    public function setBuildingId(int $buildingId): static
    {
        $this->buildingId = $buildingId;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(float $points): static
    {
        $this->points = $points;

        return $this;
    }
}
