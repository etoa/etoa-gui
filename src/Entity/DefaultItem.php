<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\DefaultItem\DefaultItemObjectResolver;
use EtoA\DefaultItem\DefaultItemRepository;

/**
 * item_object_id points at one of five tables, selected by item_cat. That is not a
 * Doctrine association — use {@see DefaultItemObjectResolver} to get the actual object.
 */
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
    private int $objectId = 0;

    #[ORM\Column(name: "item_count", type: "integer")]
    private int $count = 0;

    #[ORM\Column(name: "item_cat", type: "string")]
    private string $cat = '';

    /**
     * @param string|null $object category and object id, separated by a colon (e.g. "b:12")
     */
    public function setObject(?string $object): void
    {
        if ($object === null || !str_contains($object, ':')) {
            return;
        }

        [$cat, $objectId] = explode(':', $object, 2);
        $this->cat = $cat;
        $this->objectId = (int) $objectId;
    }

    public function getObject(): ?string
    {
        if ($this->cat === '') {
            return null;
        }

        return $this->cat . ':' . $this->objectId;
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
}
