<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EtoA\DefaultItem\DefaultItemSetRepository;

#[ORM\Entity(repositoryClass: DefaultItemSetRepository::class)]
#[ORM\Table(name: 'default_item_sets')]
class DefaultItemSet
{

    public function __construct() {
        $this->defaultItems = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "set_id", type: "integer")]
    private int $id;

    #[ORM\OneToMany(mappedBy: 'defaultItemSet', targetEntity: DefaultItem::class)]
    #[ORM\JoinColumn(name: 'set_id', referencedColumnName: 'item_set_id')]
    private Collection $defaultItems;

    #[ORM\Column(name: "set_name")]
    private string $name;

    #[ORM\Column(name: "set_active")]
    private bool $active;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, DefaultItem>
     */
    public function getDefaultItems(): Collection
    {
        return $this->defaultItems;
    }

    public function addDefaultItem(DefaultItem $defaultItem): static
    {
        if (!$this->defaultItems->contains($defaultItem)) {
            $this->defaultItems->add($defaultItem);
            $defaultItem->setDefaultItemSet($this);
        }

        return $this;
    }

    public function removeDefaultItem(DefaultItem $defaultItem): static
    {
        if ($this->defaultItems->removeElement($defaultItem)) {
            // set the owning side to null (unless already changed)
            if ($defaultItem->getDefaultItemSet() === $this) {
                $defaultItem->setDefaultItemSet(null);
            }
        }

        return $this;
    }
}
