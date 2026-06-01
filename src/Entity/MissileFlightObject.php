<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Missile\MissileFlightObjectRepository;
#[ORM\Table(name: 'missile_flights_obj')]
#[ORM\Entity(repositoryClass: MissileFlightObjectRepository::class)]
class MissileFlightObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'obj_id')]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'obj_flight_id', referencedColumnName: 'flight_id')]
    #[ORM\ManyToOne(targetEntity: MissileFlight::class,inversedBy: 'flightObjects')]
    private ?MissileFlight $flight = null;

    #[ORM\JoinColumn(name: 'obj_missile_id', referencedColumnName: 'missile_id')]
    #[ORM\ManyToOne(targetEntity: Missile::class)]
    private ?Missile $missile = null;

    private ?int $count = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getFlight(): ?MissileFlight
    {
        return $this->flight;
    }

    public function setFlight(?MissileFlight $flight): static
    {
        $this->flight = $flight;

        return $this;
    }

    public function getMissile(): ?Missile
    {
        return $this->missile;
    }

    public function setMissile(?Missile $missile): static
    {
        $this->missile = $missile;

        return $this;
    }
}
