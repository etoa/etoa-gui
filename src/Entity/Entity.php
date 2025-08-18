<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Others\AllianceMarket;
use EtoA\Universe\Others\Market;
use EtoA\Universe\Others\Unexplored;
use EtoA\Universe\Others\UnknownEntity;

#[ORM\Entity(repositoryClass: EntityRepository::class)]
#[ORM\Table(name: 'entities')]
class Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\OneToOne(inversedBy: 'entity', targetEntity: Star::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private Star $star;

    #[ORM\OneToOne(inversedBy: 'entity', targetEntity: Planet::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private Planet $planet;

    #[ORM\OneToOne(inversedBy: 'entity', targetEntity: Asteroid::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private Asteroid $asteroid;

    #[ORM\OneToOne(inversedBy: 'entity', targetEntity: Nebula::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private Nebula $nebula;

    #[ORM\OneToOne(inversedBy: 'entity', targetEntity: Wormhole::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private Wormhole $wormhole;

    #[ORM\OneToOne(inversedBy: 'entity', targetEntity: EmptySpace::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private EmptySpace $emptySpace;

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

    public function getStar(): ?Star
    {
        return $this->star;
    }

    public function setStar(?Star $star): static
    {
        $this->star = $star;

        return $this;
    }

    public function getPlanet(): ?Planet
    {
        return $this->planet;
    }

    public function setPlanet(?Planet $planet): static
    {
        $this->planet = $planet;

        return $this;
    }

    public function getAsteroid(): ?Asteroid
    {
        return $this->asteroid;
    }

    public function setAsteroid(?Asteroid $asteroid): static
    {
        $this->asteroid = $asteroid;

        return $this;
    }

    public function getNebula(): ?Nebula
    {
        return $this->nebula;
    }

    public function setNebula(?Nebula $nebula): static
    {
        $this->nebula = $nebula;

        return $this;
    }

    public function getWormhole(): ?Wormhole
    {
        return $this->wormhole;
    }

    public function setWormhole(?Wormhole $wormhole): static
    {
        $this->wormhole = $wormhole;

        return $this;
    }

    public function getEmptySpace(): ?EmptySpace
    {
        return $this->emptySpace;
    }

    public function setEmptySpace(?EmptySpace $emptySpace): static
    {
        $this->emptySpace = $emptySpace;

        return $this;
    }

    public function getType():EmptySpace|Asteroid|Star|Wormhole|Planet|Nebula|UnknownEntity|AllianceMarket|Market|Unexplored
    {
        return match ($this->code) {
            EntityType::STAR => $this->star,
            EntityType::PLANET => $this->planet,
            EntityType::ASTEROID => $this->asteroid,
            EntityType::NEBULA => $this->nebula,
            EntityType::WORMHOLE => $this->wormhole,
            EntityType::EMPTY_SPACE => $this->emptySpace,
            EntityType::MARKET => new Market(),
            EntityType::UNEXPLORED => new Unexplored(),
            EntityType::ALLIANCE_MARKET => new AllianceMarket(),
            default => new UnknownEntity(),
        };
    }

    public function displayName(): ?string
    {
        return match ($this->getCode()) {
            EntityType::PLANET => $this->getPlanet()->getName()??'Unbenannt',
            EntityType::STAR => $this->getStar()->getName()??'Unbenannt',
            default => null,
        };
    }

    public function displayOwner(): ?string
    {
        return match ($this->getCode()) {
            EntityType::PLANET => $this->getPlanet()->getUser()?->getNick()??'Niemand',
            default => 'Niemand',
        };
    }

    public function getOwner(): ?User
    {
        if($this->getCode() === EntityType::PLANET && $this->planet->getUser()) {
            return $this->planet->getUser();
        }

        return null;
    }
}
