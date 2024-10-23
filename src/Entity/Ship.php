<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\ObjectWithImage;
use EtoA\Ship\ShipDataRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipDataRepository::class)]
#[ORM\Table(name: 'ships')]
class Ship implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "ship_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "ship_name")]
    private string $name;

    #[ORM\Column(name: "ship_type_id", type: "integer")]
    private int $typeId;

    #[ORM\Column(name: "ship_shortcomment")]
    private string $shortComment;

    #[ORM\Column(name: "ship_longcomment")]
    private string $longComment;

    #[ORM\Column(name: "ship_costs_metal", type: "integer")]
    private int $costsMetal;

    #[ORM\Column(name: "ship_costs_crystal", type: "integer")]
    private int $costsCrystal;

    #[ORM\Column(name: "ship_costs_plastic", type: "integer")]
    private int $costsPlastic;

    #[ORM\Column(name: "ship_costs_fuel", type: "integer")]
    private int $costsFuel;

    #[ORM\Column(name: "ship_costs_food", type: "integer")]
    private int $costsFood;

    #[ORM\Column(name: "ship_costs_power", type: "integer")]
    private int $costsPower;

    #[ORM\Column(name: "ship_power_base", type: "integer")]
    private int $powerUse;

    #[ORM\Column(name: "ship_fuel_use", type: "integer")]
    private int $fuelUse;

    #[ORM\Column(name: "ship_fuel_use_launch", type: "integer")]
    private int $fuelUseLaunch;

    #[ORM\Column(name: "ship_fuel_use_landing", type: "integer")]
    private int $fuelUseLanding;

    #[ORM\Column(name: "ship_power_production", type: "integer")]
    private int $powerProduction;

    #[ORM\Column(name: "ship_capacity", type: "integer")]
    private int $capacity;

    #[ORM\Column(name: "ship_people_capacity", type: "integer")]
    private int $peopleCapacity;

    #[ORM\Column(name: "ship_pilots", type: "integer")]
    private int $pilots;

    #[ORM\Column(name: "ship_speed", type: "integer")]
    private int $speed;

    #[ORM\Column(name: "ship_time_to_start", type: "integer")]
    private int $timeToStart;

    #[ORM\Column(name: "ship_time_to_land", type: "integer")]
    private int $timeToLand;

    #[ORM\Column(name: "ship_show", type: "boolean")]
    private bool $show;

    #[ORM\Column(name: "ship_buildable", type: "boolean")]
    private bool $buildable;

    #[ORM\Column(name: "ship_order", type: "integer")]
    private int $order;

    #[ORM\Column(name: "ship_actions")]
    private string $actions;

    #[ORM\Column(name: "ship_bounty_bonus", type: "float")]
    private float $bountyBonus;

    #[ORM\Column(name: "ship_heal", type: "integer")]
    private int $heal;

    #[ORM\Column(name: "ship_structure", type: "integer")]
    private int $structure;

    #[ORM\Column(name: "ship_shield", type: "integer")]
    private int $shield;

    #[ORM\Column(name: "ship_weapon", type: "integer")]
    private int $weapon;

    #[ORM\Column(name: "ship_race_id", type: "integer")]
    private int $raceId;

    #[ORM\Column(name: "ship_launchable", type: "boolean")]
    private bool $launchable;

    #[ORM\Column(name: "ship_fields_provided", type: "integer")]
    private int $fieldsProvided;

    #[ORM\Column(name: "ship_cat_id", type: "integer")]
    private int $catId;

    #[ORM\Column(name: "ship_fakeable", type: "boolean")]
    private bool $fakeable;

    #[ORM\Column(name: "ship_special", type: "boolean")]
    private bool $special;

    #[ORM\Column(name: "ship_max_count", type: "integer")]
    private int $maxCount;

    #[ORM\Column(name: "ship_special_max_level", type: "integer")]
    private int $specialMaxLevel;

    #[ORM\Column(name: "ship_special_need_exp", type: "integer")]
    private int $specialNeedExp;

    #[ORM\Column(name: "ship_special_exp_factor", type: "float")]
    private float $specialExpFactor;

    #[ORM\Column(name: "ship_special_bonus_weapon", type: "float")]
    private float $specialBonusWeapon;

    #[ORM\Column(name: "ship_special_bonus_strcuture", type: "float")]
    private float $specialBonusStructure;

    #[ORM\Column(name: "ship_special_bonus_shield", type: "float")]
    private float $specialBonusShield;

    #[ORM\Column(name: "ship_special_bonus_heal", type: "float")]
    private float $specialBonusHeal;

    #[ORM\Column(name: "ship_special_bonus_capacity", type: "float")]
    private float $specialBonusCapacity;

    #[ORM\Column(name: "ship_special_bonus_speed", type: "float")]
    private float $specialBonusSpeed;

    #[ORM\Column(name: "ship_special_bonus_pilots", type: "float")]
    private float $specialBonusPilots;

    #[ORM\Column(name: "ship_special_bonus_tarn", type: "float")]
    private float $specialBonusTarn;

    #[ORM\Column(name: "ship_special_bonus_antrax", type: "float")]
    private float $specialBonusAntrax;

    #[ORM\Column(name: "ship_special_bonus_forsteal", type: "float")]
    private float $specialBonusForsteal;

    #[ORM\Column(name: "ship_special_bonus_build_destory", type: "float")]
    private float $specialBonusBuildDestroy;

    #[ORM\Column(name: "ship_special_bonus_antrax_food", type: "float")]
    private float $specialBonusAntraxFood;

    #[ORM\Column(name: "ship_special_bonus_deactivate", type: "float")]
    private float $specialBonusDeactivate;

    #[ORM\Column(name: "ship_special_bonus_readiness", type: "float")]
    private float $specialBonusReadiness;

    #[ORM\Column(name: "ship_points", type: "float")]
    private float $points;

    #[ORM\Column(name: "ship_alliance_shipyard_level", type: "integer")]
    private int $allianceShipyardLevel;

    #[ORM\Column(name: "ship_alliance_costs", type: "integer")]
    private int $allianceCosts;

    #[ORM\Column(name: "ship_ship_tradeable", type: "boolean")]
    private bool $shipTradeable;

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/ships/ship".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH."/ships/ship".$this->id."_middle.png";
            default:
                return self::BASE_PATH."/ships/ship".$this->id.".png";
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

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function setTypeId(int $typeId): static
    {
        $this->typeId = $typeId;

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

    public function getFuelUseLaunch(): ?int
    {
        return $this->fuelUseLaunch;
    }

    public function setFuelUseLaunch(int $fuelUseLaunch): static
    {
        $this->fuelUseLaunch = $fuelUseLaunch;

        return $this;
    }

    public function getFuelUseLanding(): ?int
    {
        return $this->fuelUseLanding;
    }

    public function setFuelUseLanding(int $fuelUseLanding): static
    {
        $this->fuelUseLanding = $fuelUseLanding;

        return $this;
    }

    public function getPowerProduction(): ?int
    {
        return $this->powerProduction;
    }

    public function setPowerProduction(int $powerProduction): static
    {
        $this->powerProduction = $powerProduction;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getPeopleCapacity(): ?int
    {
        return $this->peopleCapacity;
    }

    public function setPeopleCapacity(int $peopleCapacity): static
    {
        $this->peopleCapacity = $peopleCapacity;

        return $this;
    }

    public function getPilots(): ?int
    {
        return $this->pilots;
    }

    public function setPilots(int $pilots): static
    {
        $this->pilots = $pilots;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getTimeToStart(): ?int
    {
        return $this->timeToStart;
    }

    public function setTimeToStart(int $timeToStart): static
    {
        $this->timeToStart = $timeToStart;

        return $this;
    }

    public function getTimeToLand(): ?int
    {
        return $this->timeToLand;
    }

    public function setTimeToLand(int $timeToLand): static
    {
        $this->timeToLand = $timeToLand;

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

    public function getActions(): ?string
    {
        return $this->actions;
    }

    public function setActions(string $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getBountyBonus(): ?float
    {
        return $this->bountyBonus;
    }

    public function setBountyBonus(float $bountyBonus): static
    {
        $this->bountyBonus = $bountyBonus;

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

    public function getRaceId(): ?int
    {
        return $this->raceId;
    }

    public function setRaceId(int $raceId): static
    {
        $this->raceId = $raceId;

        return $this;
    }

    public function isLaunchable(): ?bool
    {
        return $this->launchable;
    }

    public function setLaunchable(bool $launchable): static
    {
        $this->launchable = $launchable;

        return $this;
    }

    public function getFieldsProvided(): ?int
    {
        return $this->fieldsProvided;
    }

    public function setFieldsProvided(int $fieldsProvided): static
    {
        $this->fieldsProvided = $fieldsProvided;

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

    public function isFakeable(): ?bool
    {
        return $this->fakeable;
    }

    public function setFakeable(bool $fakeable): static
    {
        $this->fakeable = $fakeable;

        return $this;
    }

    public function isSpecial(): ?bool
    {
        return $this->special;
    }

    public function setSpecial(bool $special): static
    {
        $this->special = $special;

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

    public function getSpecialMaxLevel(): ?int
    {
        return $this->specialMaxLevel;
    }

    public function setSpecialMaxLevel(int $specialMaxLevel): static
    {
        $this->specialMaxLevel = $specialMaxLevel;

        return $this;
    }

    public function getSpecialNeedExp(): ?int
    {
        return $this->specialNeedExp;
    }

    public function setSpecialNeedExp(int $specialNeedExp): static
    {
        $this->specialNeedExp = $specialNeedExp;

        return $this;
    }

    public function getSpecialExpFactor(): ?float
    {
        return $this->specialExpFactor;
    }

    public function setSpecialExpFactor(float $specialExpFactor): static
    {
        $this->specialExpFactor = $specialExpFactor;

        return $this;
    }

    public function getSpecialBonusWeapon(): ?float
    {
        return $this->specialBonusWeapon;
    }

    public function setSpecialBonusWeapon(float $specialBonusWeapon): static
    {
        $this->specialBonusWeapon = $specialBonusWeapon;

        return $this;
    }

    public function getSpecialBonusStructure(): ?float
    {
        return $this->specialBonusStructure;
    }

    public function setSpecialBonusStructure(float $specialBonusStructure): static
    {
        $this->specialBonusStructure = $specialBonusStructure;

        return $this;
    }

    public function getSpecialBonusShield(): ?float
    {
        return $this->specialBonusShield;
    }

    public function setSpecialBonusShield(float $specialBonusShield): static
    {
        $this->specialBonusShield = $specialBonusShield;

        return $this;
    }

    public function getSpecialBonusHeal(): ?float
    {
        return $this->specialBonusHeal;
    }

    public function setSpecialBonusHeal(float $specialBonusHeal): static
    {
        $this->specialBonusHeal = $specialBonusHeal;

        return $this;
    }

    public function getSpecialBonusCapacity(): ?float
    {
        return $this->specialBonusCapacity;
    }

    public function setSpecialBonusCapacity(float $specialBonusCapacity): static
    {
        $this->specialBonusCapacity = $specialBonusCapacity;

        return $this;
    }

    public function getSpecialBonusSpeed(): ?float
    {
        return $this->specialBonusSpeed;
    }

    public function setSpecialBonusSpeed(float $specialBonusSpeed): static
    {
        $this->specialBonusSpeed = $specialBonusSpeed;

        return $this;
    }

    public function getSpecialBonusPilots(): ?float
    {
        return $this->specialBonusPilots;
    }

    public function setSpecialBonusPilots(float $specialBonusPilots): static
    {
        $this->specialBonusPilots = $specialBonusPilots;

        return $this;
    }

    public function getSpecialBonusTarn(): ?float
    {
        return $this->specialBonusTarn;
    }

    public function setSpecialBonusTarn(float $specialBonusTarn): static
    {
        $this->specialBonusTarn = $specialBonusTarn;

        return $this;
    }

    public function getSpecialBonusAntrax(): ?float
    {
        return $this->specialBonusAntrax;
    }

    public function setSpecialBonusAntrax(float $specialBonusAntrax): static
    {
        $this->specialBonusAntrax = $specialBonusAntrax;

        return $this;
    }

    public function getSpecialBonusForsteal(): ?float
    {
        return $this->specialBonusForsteal;
    }

    public function setSpecialBonusForsteal(float $specialBonusForsteal): static
    {
        $this->specialBonusForsteal = $specialBonusForsteal;

        return $this;
    }

    public function getSpecialBonusBuildDestroy(): ?float
    {
        return $this->specialBonusBuildDestroy;
    }

    public function setSpecialBonusBuildDestroy(float $specialBonusBuildDestroy): static
    {
        $this->specialBonusBuildDestroy = $specialBonusBuildDestroy;

        return $this;
    }

    public function getSpecialBonusAntraxFood(): ?float
    {
        return $this->specialBonusAntraxFood;
    }

    public function setSpecialBonusAntraxFood(float $specialBonusAntraxFood): static
    {
        $this->specialBonusAntraxFood = $specialBonusAntraxFood;

        return $this;
    }

    public function getSpecialBonusDeactivate(): ?float
    {
        return $this->specialBonusDeactivate;
    }

    public function setSpecialBonusDeactivate(float $specialBonusDeactivate): static
    {
        $this->specialBonusDeactivate = $specialBonusDeactivate;

        return $this;
    }

    public function getSpecialBonusReadiness(): ?float
    {
        return $this->specialBonusReadiness;
    }

    public function setSpecialBonusReadiness(float $specialBonusReadiness): static
    {
        $this->specialBonusReadiness = $specialBonusReadiness;

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

    public function getAllianceShipyardLevel(): ?int
    {
        return $this->allianceShipyardLevel;
    }

    public function setAllianceShipyardLevel(int $allianceShipyardLevel): static
    {
        $this->allianceShipyardLevel = $allianceShipyardLevel;

        return $this;
    }

    public function getAllianceCosts(): ?int
    {
        return $this->allianceCosts;
    }

    public function setAllianceCosts(int $allianceCosts): static
    {
        $this->allianceCosts = $allianceCosts;

        return $this;
    }

    public function isShipTradeable(): ?bool
    {
        return $this->shipTradeable;
    }

    public function setShipTradeable(bool $shipTradeable): static
    {
        $this->shipTradeable = $shipTradeable;

        return $this;
    }
}
