<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\ObjectWithImage;
use EtoA\Universe\Star\SolarTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SolarTypeRepository::class)]
#[ORM\Table(name: 'sol_types')]
class SolarType implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "sol_type_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "sol_type_name")]
    private string $name;

    #[ORM\Column(name: "sol_type_f_metal", type: "float")]
    private float $metal;

    #[ORM\Column(name: "sol_type_f_crystal", type: "float")]
    private float $crystal;

    #[ORM\Column(name: "sol_type_f_plastic", type: "float")]
    private float $plastic;

    #[ORM\Column(name: "sol_type_f_fuel", type: "float")]
    private float $fuel;

    #[ORM\Column(name: "sol_type_f_food", type: "float")]
    private float $food;

    #[ORM\Column(name: "sol_type_f_power", type: "float")]
    private float $power;

    #[ORM\Column(name: "sol_type_f_population", type: "float")]
    private float $people;

    #[ORM\Column(name: "sol_type_f_buildtime", type: "float")]
    private float $buildTime;

    #[ORM\Column(name: "sol_type_comment")]
    private string $comment;

    #[ORM\Column(name: "sol_type_f_researchtime", type: "float")]
    private float $researchTime;

    #[ORM\Column(name: "sol_type_consider", type: "boolean")]
    private bool $consider;

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/stars/star".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH."/stars/star".$this->id."_middle.png";
            default:
                return self::BASE_PATH."/stars/star".$this->id.".png";
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

    public function getBuildTime(): ?float
    {
        return $this->buildTime;
    }

    public function setBuildTime(float $buildTime): static
    {
        $this->buildTime = $buildTime;

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
}
