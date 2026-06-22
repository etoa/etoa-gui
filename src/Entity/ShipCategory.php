<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Ship\ShipCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipCategoryRepository::class)]
#[ORM\Table(name: 'ship_cat')]
class ShipCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "cat_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "cat_name", type: "string")]
    private string $name;

    #[ORM\Column(name: "cat_order", type: "integer")]
    private int $order;

    #[ORM\Column(name: "cat_color", type: "string")]
    private string $color;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
