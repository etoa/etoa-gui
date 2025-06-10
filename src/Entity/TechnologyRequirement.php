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
    #[ORM\ManyToOne(targetEntity: Technology::class)]
    private Technology $object;

    #[ORM\JoinColumn(name: 'req_building_id', referencedColumnName: 'building_id')]
    #[ORM\ManyToOne(targetEntity: Building::class)]
    private ?Building $requiredBuilding = null;

    #[ORM\JoinColumn(name: 'req_tech_id', referencedColumnName: 'tech_id')]
    #[ORM\ManyToOne(targetEntity: Technology::class)]
    private ?Technology $requiredTechnology = null;

    #[ORM\Column(name: 'req_level')]
    private int $requiredLevel = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequiredLevel(): ?int
    {
        return $this->requiredLevel;
    }

    public function setRequiredLevel(int $requiredLevel): static
    {
        $this->requiredLevel = $requiredLevel;

        return $this;
    }

    public function getObject(): ?Technology
    {
        return $this->object;
    }

    public function setObject(?Technology $object): static
    {
        $this->object = $object;

        return $this;
    }

    public function getRequiredBuilding(): ?Building
    {
        return $this->requiredBuilding;
    }

    public function setRequiredBuilding(?Building $requiredBuilding): static
    {
        $this->requiredBuilding = $requiredBuilding;

        return $this;
    }

    public function getRequiredTechnology(): ?Technology
    {
        return $this->requiredTechnology;
    }

    public function setRequiredTechnology(?Technology $requiredTechnology): static
    {
        $this->requiredTechnology = $requiredTechnology;

        return $this;
    }

}
