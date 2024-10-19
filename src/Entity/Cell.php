<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Cell::class)]
#[ORM\Table(name: 'cells')]
class Cell
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $sx;

    #[ORM\Column(type: "integer")]
    private int $cx;

    #[ORM\Column(type: "integer")]
    private int $sy;

    #[ORM\Column(type: "integer")]
    private int $cy;

    public function toString(): string
    {
        return $this->sx . "/" . $this->sy . " : " . $this->cx . "/" . $this->cy;
    }

    /**
     * @return array<int>
     */
    public function getAbsoluteCoordinates(int $numberOfCellsX, int $numberOfCellsY): array
    {
        $x = (($this->sx - 1) * $numberOfCellsX) + $this->cx;
        $y = (($this->sy - 1) * $numberOfCellsY) + $this->cy;

        return [$x, $y];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSx(): ?int
    {
        return $this->sx;
    }

    public function setSx(int $sx): static
    {
        $this->sx = $sx;

        return $this;
    }

    public function getCx(): ?int
    {
        return $this->cx;
    }

    public function setCx(int $cx): static
    {
        $this->cx = $cx;

        return $this;
    }

    public function getSy(): ?int
    {
        return $this->sy;
    }

    public function setSy(int $sy): static
    {
        $this->sy = $sy;

        return $this;
    }

    public function getCy(): ?int
    {
        return $this->cy;
    }

    public function setCy(int $cy): static
    {
        $this->cy = $cy;

        return $this;
    }
}
