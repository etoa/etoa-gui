<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Race\RaceDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceDataRepository::class)]
#[ORM\Table(name: 'races')]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "race_id",type: "integer")]
    protected int $id;

    #[ORM\Column(name: "race_name",type: "string")]
    protected string $name;

    #[ORM\Column(name: "race_comment",type: "string")]
    protected string $comment;

    #[ORM\Column(name: "race_short_comment",type: "string")]
    protected string $shortComment;

    #[ORM\Column(name: "race_adj1",type: "string")]
    protected string $adj1;

    #[ORM\Column(name:  "race_adj2",type: "string")]
    protected string $adj2;

    #[ORM\Column(name: "race_adj3",type: "string")]
    protected string $adj3;

    #[ORM\Column(name: "race_leadertitle",type: "string")]
    protected string $leaderTitle;

    #[ORM\Column(name: "race_f_researchtime",type: "float")]
    protected float $researchTime;

    #[ORM\Column(name: "race_f_buildtime",type: "float")]
    protected float $buildTime;

    #[ORM\Column(name: "race_f_fleettime",type: "float")]
    protected float $fleetTime;

    #[ORM\Column(name: "race_f_metal",type: "float")]
    protected float $metal;

    #[ORM\Column(name: "race_f_crystal",type: "float")]
    protected float $crystal;

    #[ORM\Column(name: "race_f_plastic",type: "float")]
    protected float $plastic;

    #[ORM\Column(name: "race_f_fuel",type: "float")]
    protected float $fuel;

    #[ORM\Column(name: "race_f_food",type: "float")]
    protected float $food;

    #[ORM\Column(name: "race_f_power",type: "float")]
    protected float $power;

    #[ORM\Column(name: "race_f_population",type: "float")]
    protected float $population;

    #[ORM\Column(name: "race_active",type: "boolean")]
    protected bool $active;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getShortComment(): ?string
    {
        return $this->shortComment;
    }

    public function setShortComment(string $shortComment): static
    {
        $this->shortComment = $shortComment;

        return $this;
    }

    public function getAdj1(): ?string
    {
        return $this->adj1;
    }

    public function setAdj1(string $adj1): static
    {
        $this->adj1 = $adj1;

        return $this;
    }

    public function getAdj2(): ?string
    {
        return $this->adj2;
    }

    public function setAdj2(string $adj2): static
    {
        $this->adj2 = $adj2;

        return $this;
    }

    public function getAdj3(): ?string
    {
        return $this->adj3;
    }

    public function setAdj3(string $adj3): static
    {
        $this->adj3 = $adj3;

        return $this;
    }

    public function getLeaderTitle(): ?string
    {
        return $this->leaderTitle;
    }

    public function setLeaderTitle(string $leaderTitle): static
    {
        $this->leaderTitle = $leaderTitle;

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

    public function getBuildTime(): ?float
    {
        return $this->buildTime;
    }

    public function setBuildTime(float $buildTime): static
    {
        $this->buildTime = $buildTime;

        return $this;
    }

    public function getFleetTime(): ?float
    {
        return $this->fleetTime;
    }

    public function setFleetTime(float $fleetTime): static
    {
        $this->fleetTime = $fleetTime;

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

    public function getPopulation(): ?float
    {
        return $this->population;
    }

    public function setPopulation(float $population): static
    {
        $this->population = $population;

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

}
