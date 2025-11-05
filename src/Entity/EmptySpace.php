<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmptySpaceRepository::class)]
#[ORM\Table(name: 'space')]
class EmptySpace extends AbstractEntity implements ObjectWithImage
{

    #[ORM\Id]
    #[ORM\OneToOne(mappedBy: "emptySpace", targetEntity: Entity::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private Entity $entity;

    #[ORM\Column(name: 'lastvisited', type: "integer")]
    private int $lastVisited = 0;

    public function getEntityCodeString(): string
    {
        return "Leerer Raum";
    }

    public function getAllowedFleetActions(): array
    {
        return [FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getImagePath(): string
    {
        $numImages = 10;
        $r = ($this->id % $numImages) + 1;
        return ObjectWithImage::BASE_PATH . "/space/space" . $r . "_small.png";
    }

    public function getLastVisited(): ?int
    {
        return $this->lastVisited;
    }

    public function setLastVisited(int $lastVisited): static
    {
        $this->lastVisited = $lastVisited;

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
