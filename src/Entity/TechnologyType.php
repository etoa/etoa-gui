<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Technology\TechnologyTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnologyTypeRepository::class)]
#[ORM\Table(name: 'tech_types')]
class TechnologyType
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "type_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "type_name")]
    private string $name;

    #[ORM\Column(name: "type_order", type: "integer")]
    private int $order;

    #[ORM\Column(name: "type_color", type: "string")]
    private string $color;

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

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
