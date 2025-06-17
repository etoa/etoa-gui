<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Missile\MissileRequirementRepository;

#[ORM\Entity(repositoryClass: MissileRequirementRepository::class)]
class MissileRequirements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'obj_id', referencedColumnName: 'missile_id')]
    #[ORM\ManyToOne(targetEntity: Missile::class)]
    private Missile $obj;

    #[ORM\JoinColumn(name: 'req_building_id', referencedColumnName: 'building_id')]
    #[ORM\ManyToOne(targetEntity: Building::class)]
    private ?Building $building = null;

    #[ORM\JoinColumn(name: 'req_tech_id', referencedColumnName: 'tech_id')]
    #[ORM\ManyToOne(targetEntity: Technology::class)]
    private ?string $tech = null;

    #[ORM\Column(name: 'req_level')]
    private ?int $level = null;

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObj(): ?Missile
    {
        return $this->obj;
    }

    public function setObj(?Missile $obj): static
    {
        $this->obj = $obj;

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

    public function getTech(): ?Technology
    {
        return $this->tech;
    }

    public function setTech(?Technology $tech): static
    {
        $this->tech = $tech;

        return $this;
    }
}
