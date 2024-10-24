<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Nebula\NebulaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NebulaRepository::class)]
#[ORM\Table(name: 'nebulas')]
class Nebula extends AbstractEntity implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private int $resMetal;

    #[ORM\Column]
    private int $resCrystal;

    #[ORM\Column]
    private int $resPlastic;

    #[ORM\Column]
    private int $resFuel;

    #[ORM\Column]
    private int $resFood;

    #[ORM\Column]
    private int $resPower;

    public function getImagePath(string $type = ""):string
    {
        $numImages = 9;
        $r = ($this->id % $numImages) + 1;
        return ObjectWithImage::BASE_PATH . "/nebulas/nebula" . $r . "_small.png";
    }

    public function getAllowedFleetActions():array
    {
        return [FleetAction::COLLECT_CRYSTAL, FleetAction::ANALYZE, FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getEntityCodeString(): string
    {
        return "Interstellarer Gasnebel";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResMetal(): ?int
    {
        return $this->resMetal;
    }

    public function setResMetal(int $resMetal): static
    {
        $this->resMetal = $resMetal;

        return $this;
    }

    public function getResCrystal(): ?int
    {
        return $this->resCrystal;
    }

    public function setResCrystal(int $resCrystal): static
    {
        $this->resCrystal = $resCrystal;

        return $this;
    }

    public function getResPlastic(): ?int
    {
        return $this->resPlastic;
    }

    public function setResPlastic(int $resPlastic): static
    {
        $this->resPlastic = $resPlastic;

        return $this;
    }

    public function getResFuel(): ?int
    {
        return $this->resFuel;
    }

    public function setResFuel(int $resFuel): static
    {
        $this->resFuel = $resFuel;

        return $this;
    }

    public function getResFood(): ?int
    {
        return $this->resFood;
    }

    public function setResFood(int $resFood): static
    {
        $this->resFood = $resFood;

        return $this;
    }

    public function getResPower(): ?int
    {
        return $this->resPower;
    }

    public function setResPower(int $resPower): static
    {
        $this->resPower = $resPower;

        return $this;
    }
}
