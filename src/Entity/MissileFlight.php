<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Missile\MissileFlightRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissileFlightRepository::class)]
#[ORM\Table(name: 'missile_flights')]
class MissileFlight
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "flight_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "flight_entity_from", type: "integer")]
    private int $entityFromId;

    #[ORM\Column(name: "flight_entity_to", type: "integer")]
    private int $targetPlanetId;

    #[ORM\JoinColumn(name: 'flight_entity_to', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private ?Planet $target;

    #[ORM\Column(name: "flight_landtime", type: "integer")]
    private int $landTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityFromId(): ?int
    {
        return $this->entityFromId;
    }

    public function setEntityFromId(int $entityFromId): static
    {
        $this->entityFromId = $entityFromId;

        return $this;
    }

    public function getTargetPlanetId(): ?int
    {
        return $this->targetPlanetId;
    }

    public function setTargetPlanetId(int $targetPlanetId): static
    {
        $this->targetPlanetId = $targetPlanetId;

        return $this;
    }

    public function getLandTime(): ?int
    {
        return $this->landTime;
    }

    public function setLandTime(int $landTime): static
    {
        $this->landTime = $landTime;

        return $this;
    }

    public function getTarget(): ?Planet
    {
        return $this->target;
    }

    public function setTarget(?Planet $target): static
    {
        $this->target = $target;

        return $this;
    }
}
