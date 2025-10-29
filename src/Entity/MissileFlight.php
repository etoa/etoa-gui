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

    #[ORM\JoinColumn(name: 'flight_entity_from', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private ?Planet $entityFrom;

    #[ORM\JoinColumn(name: 'flight_entity_to', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private ?Planet $target;

    #[ORM\Column(name: "flight_landtime", type: "integer")]
    private int $landTime;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEntityFrom(): ?Planet
    {
        return $this->entityFrom;
    }

    public function setEntityFrom(?Planet $entityFrom): static
    {
        $this->entityFrom = $entityFrom;

        return $this;
    }
}
