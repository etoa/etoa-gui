<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Universe\Resources\BaseResources;

class Defense
{
    public int $id;
    public string $name;
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
    public int $prodPower;
    public int $fields;
    public bool $show;
    public bool $buildable;
    public int $order;
    public int $structure;
    public int $shield;
    public int $weapon;
    public int $heal;
    public int $jam;
    public int $raceId;
    public int $catId;
    public int $maxCount;
    public float $points;

    public function __construct(array $data)
    {
        $this->id = (int) $data['def_id'];
        $this->name = $data['def_name'];
        $this->shortComment = $data['def_shortcomment'];
        $this->longComment = $data['def_longcomment'];
        $this->costsMetal = (int) $data['def_costs_metal'];
        $this->costsCrystal = (int) $data['def_costs_crystal'];
        $this->costsPlastic = (int) $data['def_costs_plastic'];
        $this->costsFuel = (int) $data['def_costs_fuel'];
        $this->costsFood = (int) $data['def_costs_food'];
        $this->costsPower = (int) $data['def_costs_power'];
        $this->powerUse = (int) $data['def_power_use'];
        $this->fuelUse = (int) $data['def_fuel_use'];
        $this->prodPower = (int) $data['def_prod_power'];
        $this->fields = (int) $data['def_fields'];
        $this->show = (bool) $data['def_show'];
        $this->buildable = (bool) $data['def_buildable'];
        $this->order = (int) $data['def_order'];
        $this->structure = (int) $data['def_structure'];
        $this->shield = (int) $data['def_shield'];
        $this->weapon = (int) $data['def_weapon'];
        $this->heal = (int) $data['def_heal'];
        $this->jam = (int) $data['def_jam'];
        $this->raceId = (int) $data['def_race_id'];
        $this->catId = (int) $data['def_cat_id'];
        $this->maxCount = (int) $data['def_max_count'];
        $this->points = (float) $data['def_points'];
    }

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return IMAGE_PATH."/defense/def".$this->id."_small.png";
            case 'medium':
                return IMAGE_PATH."/defense/def".$this->id."_middle.png";
            default:
                return IMAGE_PATH."/defense/def".$this->id.".png";
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
