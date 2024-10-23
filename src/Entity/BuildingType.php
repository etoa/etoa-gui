<?php

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EtoA\Building\BuildingTypeDataRepository;

#[ORM\Entity(repositoryClass: BuildingTypeDataRepository::class)]
class BuildingType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"type_id")]
    private ?int $id = null;

    #[ORM\Column(name:"type_name", length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $typeOrder = null;

    #[ORM\Column(name:"type_color", length: 10)]
    private ?string $color = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTypeOrder(): ?int
    {
        return $this->typeOrder;
    }

    public function setTypeOrder(int $typeOrder): static
    {
        $this->typeOrder = $typeOrder;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
