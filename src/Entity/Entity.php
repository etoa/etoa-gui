<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;

#[ORM\Entity(repositoryClass: EntityRepository::class)]
#[ORM\Table(name: 'entities')]
class Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $cellId;

    #[ORM\ManyToOne(targetEntity: Cell::class)]
    private Cell $cell;

    #[ORM\Column(type: "string")]
    private string $code;

    #[ORM\Column(type: "integer")]
    private int $pos;

    #[ORM\Column(type: "integer")]
    private int $lastvisited;


    public function toString(): string
    {
        return $this->codeString() . ' ' . $this->coordinatesString();
    }

    public function getCoordinates(): EntityCoordinates
    {
        return new EntityCoordinates($this->cell->getSx(), $this->cell->getSy(), $this->cell->getCx(), $this->cell->getCy(), $this->pos);
    }

    public function coordinatesString(): string
    {
        return $this->cell->getSx() . "/" . $this->cell->getSy() . " : " . $this->cell->getCx() . "/" . $this->cell->getCy() . " : " . $this->pos;
    }

    public function codeString(): string
    {
        $types = EntityType::all();

        return $types[$this->code] ?? 'Unbekannter Raum';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCellId(): ?int
    {
        return $this->cellId;
    }

    public function setCellId(int $cellId): static
    {
        $this->cellId = $cellId;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getPos(): ?int
    {
        return $this->pos;
    }

    public function setPos(int $pos): static
    {
        $this->pos = $pos;

        return $this;
    }

    public function getLastvisited(): ?int
    {
        return $this->lastvisited;
    }

    public function setLastvisited(int $lastvisited): static
    {
        $this->lastvisited = $lastvisited;

        return $this;
    }

    public function getCell(): ?Cell
    {
        return $this->cell;
    }

    public function setCell(?Cell $cell): static
    {
        $this->cell = $cell;

        return $this;
    }
}
