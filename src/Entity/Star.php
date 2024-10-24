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

    #[ORM\Column]
    private ?string $name;

    #[ORM\Column]
    private int $typeId;

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
}
