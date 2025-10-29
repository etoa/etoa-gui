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

    #[ORM\JoinColumn(name: 'bp_building_id', referencedColumnName: 'building_id')]
    #[ORM\ManyToOne(targetEntity: Building::class, inversedBy: 'points')]
    private ?Building $building = null;

    #[ORM\Column(name: "bp_level", type: "integer")]
    private int $level;

    #[ORM\Column(name: "bp_points", type: "float")]
    private float $points;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }
}
