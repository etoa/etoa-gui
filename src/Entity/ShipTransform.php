<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Ship\ShipTransformRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipTransformRepository::class)]
#[ORM\Table(name: 'obj_transform')]
class ShipTransform
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    protected int $id;

    #[ORM\Column(type: "integer")]
    private int $shipId;

    #[ORM\Column(name:"def_id", type: "integer")]
    private int $defenseId;

    #[ORM\Column(name:"num_def", type: "integer")]
    private int $numberOfDefense;

    public static function createFromShip(array $data): ShipTransform
    {
        $transform = new ShipTransform();
        $transform->shipId = (int) $data['ship_id'];
        $transform->defenseId = (int) $data['def_id'];
        $transform->availableShips = (int) $data['count'];
        $transform->numberOfDefense = (int) $data['num_def'];

        return $transform;
    }

    public static function createFromDefense(array $data): ShipTransform
    {
        $transform = new ShipTransform();
        $transform->shipId = (int) $data['ship_id'];
        $transform->defenseId = (int) $data['def_id'];
        $transform->availableDefense = (int) $data['count'];
        $transform->numberOfDefense = (int) $data['num_def'];

        return $transform;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShipId(): ?int
    {
        return $this->shipId;
    }

    public function setShipId(int $shipId): static
    {
        $this->shipId = $shipId;

        return $this;
    }

    public function getDefenseId(): ?int
    {
        return $this->defenseId;
    }

    public function setDefenseId(int $defenseId): static
    {
        $this->defenseId = $defenseId;

        return $this;
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
}
