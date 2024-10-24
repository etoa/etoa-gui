<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Wormhole\WormholeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WormholeRepository::class)]
#[ORM\Table(name: 'wormholes')]
class Wormhole extends AbstractEntity implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $targetId;

    #[ORM\Column]
    private int $changed;

    #[ORM\Column]
    private bool $persistent;

    public function getEntityCodeString(): string
    {
        return "Wurmloch";
    }

    public function getAllowedFleetActions(): array
    {
        return [FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getImagePath(): string
    {
        $prefix = $this->persistent ? 'wormhole_persistent' : 'wormhole';
        return ObjectWithImage::BASE_PATH . "/wormholes/" . $prefix . "1_small.png";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTargetId(): ?int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): static
    {
        $this->targetId = $targetId;

        return $this;
    }

    public function getChanged(): ?int
    {
        return $this->changed;
    }

    public function setChanged(int $changed): static
    {
        $this->changed = $changed;

        return $this;
    }

    public function isPersistent(): ?bool
    {
        return $this->persistent;
    }

    public function setPersistent(bool $persistent): static
    {
        $this->persistent = $persistent;

        return $this;
    }
}
