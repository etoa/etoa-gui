<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Technology\TechnologyRequirementRepository;

#[ORM\Entity(repositoryClass: TechnologyRequirementRepository::class)]
#[ORM\Table(name: 'tech_requirements')]

class TechnologyRequirement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\JoinColumn(name: 'obj_id', referencedColumnName: 'tech_id')]
    #[ORM\ManyToOne(targetEntity: Technology::class, inversedBy: 'objectRequirements')]
    private Technology $obj;

    #[ORM\JoinColumn(name: 'req_building_id', referencedColumnName: 'building_id')]
    #[ORM\ManyToOne(targetEntity: Building::class)]
    private ?Building $building = null;

    #[ORM\JoinColumn(name: 'req_tech_id', referencedColumnName: 'tech_id')]
    #[ORM\ManyToOne(targetEntity: Technology::class)]
    private ?Technology $tech = null;

    #[ORM\Column(name: 'req_level')]
    private int $level = 1;

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

    public function getObj(): ?Technology
    {
        return $this->obj;
    }

    public function setObj(?Technology $obj): static
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
