<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\ObjectWithImage;
use EtoA\Universe\Planet\PlanetTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanetTypeRepository::class)]
#[ORM\Table(name: 'planet_types')]
class PlanetType implements ObjectWithImage
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name:"type_id", type: "integer")]
    private int $id;

    #[ORM\Column(name:"type_name", type: "string")]
    private string $name;

    #[ORM\Column(name:"type_habitable", type: "boolean")]
    private bool $habitable;

    #[ORM\Column(name:"type_f_metal", type: "float")]
    private float $metal;

    #[ORM\Column(name:"type_f_crystal", type: "float")]
    private float $crystal;

    #[ORM\Column(name:"type_f_plastic", type: "float")]
    private float $plastic;

    #[ORM\Column(name:"type_f_fuel", type: "float")]
    private float $fuel;

    #[ORM\Column(name:"type_f_food", type: "float")]
    private float $food;

    #[ORM\Column(name:"type_f_power", type: "float")]
    private float $power;

    #[ORM\Column(name:"type_f_people", type: "float")]
    private float $people;

    #[ORM\Column(name:"type_build_time", type: "float")]
    private float $buildTime;

    #[ORM\Column(name:"type_comment", type: "string")]
    private string $comment;

    #[ORM\Column(name:"type_f_researchtime", type: "float")]
    private float $researchTime;

    #[ORM\Column(name:"type_consider", type: "boolean")]
    private bool $consider;

    #[ORM\Column(name:"type_collect_gas", type: "boolean")]
    private bool $collectGas;

    public function getImagePath(string $type = "small", int $imageNumber = 1): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH . "/planets/planet" . $this->id . '_' . $imageNumber . "_small.png";
            case 'medium':
                return self::BASE_PATH . "/planets/planet" . $this->id . '_' . $imageNumber . "_middle.png";
            default:
                return self::BASE_PATH . "/planets/planet" . $this->id . '_' . $imageNumber . ".png";
        }
    }

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

    public function isHabitable(): ?bool
    {
        return $this->habitable;
    }

    public function setHabitable(bool $habitable): static
    {
        $this->habitable = $habitable;

        return $this;
    }

    public function getMetal(): ?float
    {
        return $this->metal;
    }

    public function setMetal(float $metal): static
    {
        $this->metal = $metal;

        return $this;
    }

    public function getCrystal(): ?float
    {
        return $this->crystal;
    }

    public function setCrystal(float $crystal): static
    {
        $this->crystal = $crystal;

        return $this;
    }

    public function getBuildTime(): float
    {
        return $this->buildTime;
    }

    public function setBuildTime(float $buildTime): static
    {
        $this->buildTime = $buildTime;

        return $this;
    }

    public function getPlastic(): ?float
    {
        return $this->plastic;
    }

    public function setPlastic(float $plastic): static
    {
        $this->plastic = $plastic;

        return $this;
    }

    public function getFuel(): ?float
    {
        return $this->fuel;
    }

    public function setFuel(float $fuel): static
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getFood(): ?float
    {
        return $this->food;
    }

    public function setFood(float $food): static
    {
        $this->food = $food;

        return $this;
    }

    public function getPower(): ?float
    {
        return $this->power;
    }

    public function setPower(float $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getPeople(): ?float
    {
        return $this->people;
    }

    public function setPeople(float $people): static
    {
        $this->people = $people;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getResearchTime(): ?float
    {
        return $this->researchTime;
    }

    public function setResearchTime(float $researchTime): static
    {
        $this->researchTime = $researchTime;

        return $this;
    }

    public function isConsider(): ?bool
    {
        return $this->consider;
    }

    public function setConsider(bool $consider): static
    {
        $this->consider = $consider;

        return $this;
    }

    public function isCollectGas(): ?bool
    {
        return $this->collectGas;
    }

    public function setCollectGas(bool $collectGas): static
    {
        $this->collectGas = $collectGas;

        return $this;
    }
}
