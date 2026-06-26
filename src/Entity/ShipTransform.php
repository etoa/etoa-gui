<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Ship\ShipTransformRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipTransformRepository::class)]
#[ORM\Table(name: 'obj_transforms')]
class ShipTransform
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'ship_id', referencedColumnName: 'ship_id')]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    private Ship $ship;

    #[ORM\JoinColumn(name: 'def_id', referencedColumnName: 'def_id')]
    #[ORM\ManyToOne(targetEntity: Defense::class)]
    private Defense $defense;

    #[ORM\Column(name:"num_def", type: "integer")]
    private int $numberOfDefense;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberOfDefense(): ?int
    {
        return $this->numberOfDefense;
    }

    public function setNumberOfDefense(int $numberOfDefense): static
    {
        $this->numberOfDefense = $numberOfDefense;

        return $this;
    }

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): static
    {
        $this->ship = $ship;

        return $this;
    }

    public function getDefense(): ?Defense
    {
        return $this->defense;
    }

    public function setDefense(?Defense $defense): static
    {
        $this->defense = $defense;

        return $this;
    }
}
