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

    #[ORM\Column(name: "cat_name", type: "integer")]
    private string $name;

    #[ORM\Column(name: "cat_border", type: "integer")]
    private int $order;

    #[ORM\Column(name: "cat_color", type: "integer")]
    private string $color;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?int
    {
        return $this->name;
    }

    public function setName(int $name): static
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

    public function getColor(): ?int
    {
        return $this->color;
    }

    public function setColor(int $color): static
    {
        $this->color = $color;

        return $this;
    }
}
