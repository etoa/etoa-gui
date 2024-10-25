<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Star\StarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StarRepository::class)]
#[ORM\Table(name: 'stars')]
class Star extends AbstractEntity implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\OneToOne(mappedBy: "star", targetEntity: Entity::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private Entity $entity;

    #[ORM\Column]
    private ?string $name;

    #[ORM\Column]
    private int $typeId;

    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'sol_type_id')]
    #[ORM\ManyToOne(targetEntity: SolarType::class)]
    private SolarType $solarType;

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/stars/star".$this->typeId."_small.png";
            case 'medium':
                return self::BASE_PATH."/stars/star".$this->typeId."_middle.png";
            default:
                return self::BASE_PATH."/stars/star".$this->typeId.".png";
        }
    }

    public function getEntityCodeString(): string
    {
        return "Stern";
    }

    public function getAllowedFleetActions(): array
    {
        return [FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function setTypeId(int $typeId): static
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function getSolarType(): ?SolarType
    {
        return $this->solarType;
    }

    public function setSolarType(?SolarType $solarType): static
    {
        $this->solarType = $solarType;

        return $this;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): static
    {
        $this->entity = $entity;

        return $this;
    }
}
