<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Universe\Resources\BaseResources;

class Ship
{
    public int $id;
    public string $name;
    public int $typeId;
    public string $shortComment;
    public string $longComment;
    public int $costsMetal;
    public int $costsCrystal;
    public int $costsPlastic;
    public int $costsFuel;
    public int $costsFood;
    public int $costsPower;
    public int $powerUse;
    public int $fuelUse;
    public int $fuelUseLaunch;
    public int $fuelUseLanding;
    public int $powerProduction;
    public int $capacity;
    public int $peopleCapacity;
    public int $pilots;
    public int $speed;
    public int $timeToStart;
    public int $timeToLand;
    public bool $show;
    public bool $buildable;
    public int $order;
    public string $actions;
    public float $bountyBonus;
    public int $heal;
    public int $structure;
    public int $shield;
    public int $weapon;
    public int $raceId;
    public bool $launchable;
    public int $fieldsProvided;
    public int $catId;
    public bool $fakeable;
    public bool $special;
    public int $maxCount;
    public int $specialMaxLevel;
    public int $specialNeedExp;
    public float $specialExpFactor;
    public float $specialBonusWeapon;
    public float $specialBonusStructure;
    public float $specialBonusShield;
    public float $specialBonusHeal;
    public float $specialBonusCapacity;
    public float $specialBonusSpeed;
    public float $specialBonusPilots;
    public float $specialBonusTarn;
    public float $specialBonusAntrax;
    public float $specialBonusForsteal;
    public float $specialBonusBuildDestroy;
    public float $specialBonusAntraxFood;
    public float $specialBonusDeactivate;
    public float $specialBonusReadiness;
    public float $points;
    public int $allianceShipyardLevel;
    public int $allianceCosts;
    public bool $shipTradeable;

    public function __construct(array $data)
    {
        $this->id = (int) $data['ship_id'];
        $this->name = $data['ship_name'];
        $this->typeId = (int) $data['ship_type_id'];
        $this->shortComment = $data['ship_shortcomment'];
        $this->longComment = $data['ship_longcomment'];
        $this->costsMetal = (int) $data['ship_costs_metal'];
        $this->costsCrystal = (int) $data['ship_costs_crystal'];
        $this->costsPlastic = (int) $data['ship_costs_plastic'];
        $this->costsFuel = (int) $data['ship_costs_fuel'];
        $this->costsFood = (int) $data['ship_costs_food'];
        $this->costsPower = (int) $data['ship_costs_power'];
        $this->powerUse = (int) $data['ship_power_use'];
        $this->fuelUse = (int) $data['ship_fuel_use'];
        $this->fuelUseLaunch = (int) $data['ship_fuel_use_launch'];
        $this->fuelUseLanding = (int) $data['ship_fuel_use_landing'];
        $this->powerProduction = (int) $data['ship_prod_power'];
        $this->capacity = (int) $data['ship_capacity'];
        $this->peopleCapacity = (int) $data['ship_people_capacity'];
        $this->pilots = (int) $data['ship_pilots'];
        $this->speed = (int) $data['ship_speed'];
        $this->timeToStart = (int) $data['ship_time2start'];
        $this->timeToLand = (int) $data['ship_time2land'];
        $this->show = (bool) $data['ship_show'];
        $this->buildable = (bool) $data['ship_buildable'];
        $this->order = (int) $data['ship_order'];
        $this->actions = $data['ship_actions'];
        $this->bountyBonus = (int) $data['ship_bounty_bonus'];
        $this->heal = (int) $data['ship_heal'];
        $this->structure = (int) $data['ship_structure'];
        $this->shield = (int) $data['ship_shield'];
        $this->weapon = (int) $data['ship_weapon'];
        $this->raceId = (int) $data['ship_race_id'];
        $this->launchable = (bool) $data['ship_launchable'];
        $this->fieldsProvided = (int) $data['ship_fieldsprovide'];
        $this->catId = (int) $data['ship_cat_id'];
        $this->fakeable = (bool) $data['ship_fakeable'];
        $this->special = (bool) $data['special_ship'];
        $this->maxCount = (int) $data['ship_max_count'];
        $this->specialMaxLevel = (int) $data['special_ship_max_level'];
        $this->specialNeedExp = (int) $data['special_ship_need_exp'];
        $this->specialExpFactor = (float) $data['special_ship_exp_factor'];
        $this->specialBonusWeapon = (int) $data['special_ship_bonus_weapon'];
        $this->specialBonusStructure = (int) $data['special_ship_bonus_structure'];
        $this->specialBonusShield = (int) $data['special_ship_bonus_shield'];
        $this->specialBonusHeal = (int) $data['special_ship_bonus_heal'];
        $this->specialBonusCapacity = (int) $data['special_ship_bonus_capacity'];
        $this->specialBonusSpeed = (int) $data['special_ship_bonus_speed'];
        $this->specialBonusPilots = (int) $data['special_ship_bonus_pilots'];
        $this->specialBonusTarn = (int) $data['special_ship_bonus_tarn'];
        $this->specialBonusAntrax = (int) $data['special_ship_bonus_antrax'];
        $this->specialBonusForsteal = (int) $data['special_ship_bonus_forsteal'];
        $this->specialBonusBuildDestroy = (int) $data['special_ship_bonus_build_destroy'];
        $this->specialBonusAntraxFood = (int) $data['special_ship_bonus_antrax_food'];
        $this->specialBonusDeactivate = (int) $data['special_ship_bonus_deactivade'];
        $this->specialBonusReadiness = (int) $data['special_ship_bonus_readiness'];
        $this->points = (int) $data['ship_points'];
        $this->allianceShipyardLevel = (int) $data['ship_alliance_shipyard_level'];
        $this->allianceCosts = (int) $data['ship_alliance_costs'];
        $this->shipTradeable = (bool) $data['ship_tradable'];
    }

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id."_small.".IMAGE_EXT;
            case 'medium':
                return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id."_middle.".IMAGE_EXT;
            default:
                return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id.".".IMAGE_EXT;
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
}
