<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\DefaultItem\DefaultItemType;

#[ORM\Entity(repositoryClass: DefaultItemRepository::class)]
#[ORM\Table(name: 'default_items')]
class DefaultItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "item_id", type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: DefaultItemSet::class, inversedBy: 'defaultItems')]
    #[ORM\JoinColumn(name: 'item_set_id', referencedColumnName: 'set_id')]
    private ?DefaultItemSet $defaultItemSet = null;

    #[ORM\Column(name: "item_object_id", type: "integer")]
    private int $objectId;

    #[ORM\Column(name: "item_count", type: "integer")]
    private int $count = 0;

    #[ORM\Column(name: "item_cat", type: "string")]
    private string $cat = '';

    #[ORM\ManyToOne(targetEntity: Building::class)]
    #[ORM\JoinColumn(name: 'item_object_id', referencedColumnName: 'building_id')]
    private ?Building $building = null;

    #[ORM\ManyToOne(targetEntity: Technology::class)]
    #[ORM\JoinColumn(name: 'item_object_id', referencedColumnName: 'tech_id')]
    private ?Technology $technology = null;

    #[ORM\ManyToOne(targetEntity: Ship::class)]
    #[ORM\JoinColumn(name: 'item_object_id', referencedColumnName: 'ship_id')]
    private ?Ship $ship = null;

    #[ORM\ManyToOne(targetEntity: Defense::class)]
    #[ORM\JoinColumn(name: 'item_object_id', referencedColumnName: 'def_id')]
    private ?Defense $defense = null;

    #[ORM\ManyToOne(targetEntity: Missile::class)]
    #[ORM\JoinColumn(name: 'item_object_id', referencedColumnName: 'missile_id')]
    private ?Missile $missile = null;

    public function setObject(?string $object): void
    {
        if ($object !== null) {
            $parts = explode(':', $object);
            $this->cat = $parts[0];
            $this->objectId = (int) $parts[1];
        }
    }

    public function getObject(): string
    {
        return $this->cat . ':' . $this->objectId;
    }

    public function getObjectEntity():Missile|Building|Defense|Technology|Ship
    {
        return match ($this->cat) {
            DefaultItemType::BUILDING->value => $this->building,
            DefaultItemType::TECHNOLOGY->value => $this->technology,
            DefaultItemType::MISSILE->value => $this->missile,
            DefaultItemType::DEFENSE->value => $this->defense,
            DefaultItemType::SHIP->value => $this->ship,
        };
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    public function setObjectId(int $objectId): static
    {
        $this->objectId = $objectId;

        return $this;
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

    public function getCat(): ?string
    {
        return $this->cat;
    }

    public function setCat(string $cat): static
    {
        $this->cat = $cat;

        return $this;
    }

    public function getDefaultItemSet(): ?DefaultItemSet
    {
        return $this->defaultItemSet;
    }

    public function setDefaultItemSet(?DefaultItemSet $defaultItemSet): static
    {
        $this->defaultItemSet = $defaultItemSet;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getTechnology(): ?Technology
    {
        return $this->technology;
    }

    public function setTechnology(?Technology $technology): static
    {
        $this->technology = $technology;

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
