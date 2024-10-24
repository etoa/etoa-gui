<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Core\ObjectWithImage;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceBuildingRepository::class)]
#[ORM\Table(name: 'alliance_buildings')]
class AllianceBuilding implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "alliance_building_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "alliance_building_name")]
    private string $name;

    #[ORM\Column(name: "alliance_building_shortcomment")]
    private string $shortComment;

    #[ORM\Column(name: "alliance_building_longcomment")]
    private string $longComment;

    #[ORM\Column(name: "alliance_building_costs_metal")]
    private int $costsMetal;

    #[ORM\Column(name: "alliance_building_costs_crystal")]
    private int $costsCrystal;

    #[ORM\Column(name: "alliance_building_costs_plastic")]
    private int $costsPlastic;

    #[ORM\Column(name: "alliance_building_costs_fuel")]
    private int $costsFuel;

    #[ORM\Column(name: "alliance_building_costs_food")]
    private int $costsFood;

    #[ORM\Column(name: "alliance_building_build_time")]
    private int $buildTime;

    #[ORM\Column(name: "alliance_building_build_factor")]
    private float $buildFactor;

    #[ORM\Column(name: "alliance_building_last_level")]
    private int $lastLevel;

    #[ORM\Column(name: "alliance_building_show")]
    private bool $show;

    #[ORM\Column(name: "alliance_building_needed_id")]
    private int $neededId;

    #[ORM\Column(name: "alliance_building_needed_level")]
    private int $neededLevel;

    public function getImagePath(): string
    {
        return self::BASE_PATH . "/abuildings/building" . $this->id . "_middle.png";
    }

    public function getCosts(): BaseResources
    {
        $costs = new BaseResources();
        $costs->metal = $this->costsMetal;
        $costs->crystal = $this->costsCrystal;
        $costs->plastic = $this->costsPlastic;
        $costs->fuel = $this->costsFuel;
        $costs->food = $this->costsFood;

        return $costs;
    }

    public function calculateCosts(int $level, int $members, float $memberCostsFactor): BaseResources
    {
        $level = max(1, $level);
        $members = max(1, $members);

        $factor = $this->buildFactor ** ($level - 1);
        $memberLevelFactor = $factor * (1 + ($members - 1) * $memberCostsFactor);

        $costs = new BaseResources();
        $costs->metal = (int) ceil($this->costsMetal * $memberLevelFactor);
        $costs->crystal = (int) ceil($this->costsCrystal * $memberLevelFactor);
        $costs->plastic = (int) ceil($this->costsPlastic * $memberLevelFactor);
        $costs->fuel = (int) ceil($this->costsFuel * $memberLevelFactor);
        $costs->food = (int) ceil($this->costsFood * $memberLevelFactor);

        return $costs;
    }

    public function calculateBuildTime(int $level): int
    {
        return $this->buildTime * $level;
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

    public function getShortComment(): ?string
    {
        return $this->shortComment;
    }

    public function setShortComment(string $shortComment): static
    {
        $this->shortComment = $shortComment;

        return $this;
    }

    public function getLongComment(): ?string
    {
        return $this->longComment;
    }

    public function setLongComment(string $longComment): static
    {
        $this->longComment = $longComment;

        return $this;
    }

    public function getCostsMetal(): ?int
    {
        return $this->costsMetal;
    }

    public function setCostsMetal(int $costsMetal): static
    {
        $this->costsMetal = $costsMetal;

        return $this;
    }

    public function getCostsCrystal(): ?int
    {
        return $this->costsCrystal;
    }

    public function setCostsCrystal(int $costsCrystal): static
    {
        $this->costsCrystal = $costsCrystal;

        return $this;
    }

    public function getCostsPlastic(): ?int
    {
        return $this->costsPlastic;
    }

    public function setCostsPlastic(int $costsPlastic): static
    {
        $this->costsPlastic = $costsPlastic;

        return $this;
    }

    public function getCostsFuel(): ?int
    {
        return $this->costsFuel;
    }

    public function setCostsFuel(int $costsFuel): static
    {
        $this->costsFuel = $costsFuel;

        return $this;
    }

    public function getCostsFood(): ?int
    {
        return $this->costsFood;
    }

    public function setCostsFood(int $costsFood): static
    {
        $this->costsFood = $costsFood;

        return $this;
    }

    public function getBuildTime(): ?int
    {
        return $this->buildTime;
    }

    public function setBuildTime(int $buildTime): static
    {
        $this->buildTime = $buildTime;

        return $this;
    }

    public function getBuildFactor(): ?float
    {
        return $this->buildFactor;
    }

    public function setBuildFactor(float $buildFactor): static
    {
        $this->buildFactor = $buildFactor;

        return $this;
    }

    public function getLastLevel(): ?int
    {
        return $this->lastLevel;
    }

    public function setLastLevel(int $lastLevel): static
    {
        $this->lastLevel = $lastLevel;

        return $this;
    }

    public function isShow(): ?bool
    {
        return $this->show;
    }

    public function setShow(bool $show): static
    {
        $this->show = $show;

        return $this;
    }

    public function getNeededId(): ?int
    {
        return $this->neededId;
    }

    public function setNeededId(int $neededId): static
    {
        $this->neededId = $neededId;

        return $this;
    }

    public function getNeededLevel(): ?int
    {
        return $this->neededLevel;
    }

    public function setNeededLevel(int $neededLevel): static
    {
        $this->neededLevel = $neededLevel;

        return $this;
    }
}
