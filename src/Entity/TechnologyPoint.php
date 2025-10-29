<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Technology\TechnologyPointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnologyPointRepository::class)]
#[ORM\Table(name: 'tech_points')]
class TechnologyPoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "bp_id", type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'bp_tech_id', referencedColumnName: 'tech_id')]
    #[ORM\ManyToOne(targetEntity: Technology::class, inversedBy: 'points')]
    private ?Technology $technology = null;

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

    public function getTechnology(): ?Technology
    {
        return $this->technology;
    }

    public function setTechnology(?Technology $technology): static
    {
        $this->technology = $technology;

        return $this;
    }
}
