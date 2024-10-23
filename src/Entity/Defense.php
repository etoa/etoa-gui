<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Core\ObjectWithImage;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Universe\Resources\BaseResources;

#[ORM\Entity(repositoryClass: DefenseDataRepository::class)]
class Defense implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "def_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "def_name")]
    private string $name;

    #[ORM\Column(name: "def_short_comment")]
    private string $shortComment;

    #[ORM\Column(name: "def_long_comment")]
    private string $longComment;

    #[ORM\Column(name: "def_costs_metal", type: "integer")]
    private int $costsMetal;

    #[ORM\Column(name: "def_costs_crystal", type: "integer")]
    private int $costsCrystal;

    #[ORM\Column(name: "def_costs_plastic", type: "integer")]
    private int $costsPlastic;

    #[ORM\Column(name: "def_costs_fuel", type: "integer")]
    private int $costsFuel;

    #[ORM\Column(name: "def_costs_food", type: "integer")]
    private int $costsFood;

    #[ORM\Column(name: "def_costs_power", type: "integer")]
    private int $costsPower;

    #[ORM\Column(name: "def_power_use", type: "integer")]
    private int $powerUse;

    #[ORM\Column(name: "def_fuel_use", type: "integer")]
    private int $fuelUse;

    #[ORM\Column(name: "def_prod_power", type: "integer")]
    private int $prodPower;

    #[ORM\Column(name: "def_fields", type: "integer")]
    private int $fields;

    #[ORM\Column(name: "def_show", type: "boolean")]
    private bool $show;

    #[ORM\Column(name: "def_buildable", type: "boolean")]
    private bool $buildable;

    #[ORM\Column(name: "def_order", type: "integer")]
    private int $order;

    #[ORM\Column(name: "def_structure", type: "integer")]
    private int $structure;

    #[ORM\Column(name: "def_shield", type: "integer")]
    private int $shield;

    #[ORM\Column(name: "def_weapon", type: "integer")]
    private int $weapon;

    #[ORM\Column(name: "def_heal", type: "integer")]
    private int $heal;

    #[ORM\Column(name: "def_jam", type: "integer")]
    private int $jam;

    #[ORM\Column(name: "def_race_id", type: "integer")]
    private int $raceId;

    #[ORM\Column(name: "def_cat_id", type: "integer")]
    private int $catId;

    #[ORM\Column(name: "def_max_count", type: "integer")]
    private int $maxCount;

    #[ORM\Column(name: "def_points", type: "float")]
    private float $points;

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/defense/def".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH."/defense/def".$this->id."_middle.png";
            default:
                return self::BASE_PATH."/defense/def".$this->id.".png";
        }
    }

    public function getCosts(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->costsMetal;
        $resources->crystal = $this->costsCrystal;
        $resources->plastic = $this->costsPlastic;
        $resources->fuel = $this->costsFuel;
        $resources->food = $this->costsFood;

        return $resources;
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

    public function getCostsPower(): ?int
    {
        return $this->costsPower;
    }

    public function setCostsPower(int $costsPower): static
    {
        $this->costsPower = $costsPower;

        return $this;
    }

    public function getPowerUse(): ?int
    {
        return $this->powerUse;
    }

    public function setPowerUse(int $powerUse): static
    {
        $this->powerUse = $powerUse;

        return $this;
    }

    public function getFuelUse(): ?int
    {
        return $this->fuelUse;
    }

    public function setFuelUse(int $fuelUse): static
    {
        $this->fuelUse = $fuelUse;

        return $this;
    }

    public function getProdPower(): ?int
    {
        return $this->prodPower;
    }

    public function setProdPower(int $prodPower): static
    {
        $this->prodPower = $prodPower;

        return $this;
    }

    public function getFields(): ?int
    {
        return $this->fields;
    }

    public function setFields(int $fields): static
    {
        $this->fields = $fields;

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

    public function isBuildable(): ?bool
    {
        return $this->buildable;
    }

    public function setBuildable(bool $buildable): static
    {
        $this->buildable = $buildable;

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

    public function getStructure(): ?int
    {
        return $this->structure;
    }

    public function setStructure(int $structure): static
    {
        $this->structure = $structure;

        return $this;
    }

    public function getShield(): ?int
    {
        return $this->shield;
    }

    public function setShield(int $shield): static
    {
        $this->shield = $shield;

        return $this;
    }

    public function getWeapon(): ?int
    {
        return $this->weapon;
    }

    public function setWeapon(int $weapon): static
    {
        $this->weapon = $weapon;

        return $this;
    }

    public function getHeal(): ?int
    {
        return $this->heal;
    }

    public function setHeal(int $heal): static
    {
        $this->heal = $heal;

        return $this;
    }

    public function getJam(): ?int
    {
        return $this->jam;
    }

    public function setJam(int $jam): static
    {
        $this->jam = $jam;

        return $this;
    }

    public function getRaceId(): ?int
    {
        return $this->raceId;
    }

    public function setRaceId(int $raceId): static
    {
        $this->raceId = $raceId;

        return $this;
    }

    public function getCatId(): ?int
    {
        return $this->catId;
    }

    public function setCatId(int $catId): static
    {
        $this->catId = $catId;

        return $this;
    }

    public function getMaxCount(): ?int
    {
        return $this->maxCount;
    }

    public function setMaxCount(int $maxCount): static
    {
        $this->maxCount = $maxCount;

        return $this;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(float $points): static
    {
        $this->points = $points;

        return $this;
    }
}
