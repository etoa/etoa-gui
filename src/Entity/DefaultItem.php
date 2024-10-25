<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\DefaultItem\DefaultItemRepository;

#[ORM\Entity(repositoryClass: DefaultItemRepository::class)]
#[ORM\Table(name: 'default_items')]
class DefaultItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "item_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "item_set_id", type: "integer")]
    private int $setId;

    #[ORM\ManyToOne(targetEntity: DefaultItemSet::class, inversedBy: 'defaultItems')]
    #[ORM\JoinColumn(name: 'item_set_id', referencedColumnName: 'set_id')]
    private DefaultItemSet|null $defaultItemSet = null;

    #[ORM\Column(name: "item_object_id", type: "integer")]
    private int $objectId;

    #[ORM\Column(name: "item_count", type: "integer")]
    private int $count;

    #[ORM\Column(name: "item_cat", type: "string")]
    private string $cat;



    public static function createFromData(array $data): DefaultItem
    {
        $item = new DefaultItem();
        $item->id = (int) $data['item_id'];
        $item->objectId = (int) $data['item_object_id'];
        $item->count = (int) $data['item_count'];
        $item->cat = $data['item_cat'];

        return $item;
    }

    public static function empty(): DefaultItem
    {
        $item = new DefaultItem();
        $item->id = 0;
        $item->objectId = 0;
        $item->count = 0;
        $item->cat = '';

        return $item;
    }

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSetId(): ?int
    {
        return $this->setId;
    }

    public function setSetId(int $setId): static
    {
        $this->setId = $setId;

        return $this;
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
}
