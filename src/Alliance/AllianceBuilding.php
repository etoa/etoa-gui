<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceBuilding
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
    public int $buildTime;
    public float $buildFactor;
    public int $lastLevel;
    public bool $show;
    public int $neededId;
    public int $neededLevel;

    public function __construct(array $data)
    {
        $this->id = (int) $data['alliance_building_id'];
        $this->name = $data['alliance_building_name'];
        $this->shortComment = $data['alliance_building_shortcomment'];
        $this->longComment = $data['alliance_building_longcomment'];
        $this->costsMetal = (int) $data['alliance_building_costs_metal'];
        $this->costsCrystal = (int) $data['alliance_building_costs_crystal'];
        $this->costsPlastic = (int) $data['alliance_building_costs_plastic'];
        $this->costsFuel = (int) $data['alliance_building_costs_fuel'];
        $this->costsFood = (int) $data['alliance_building_costs_food'];
        $this->buildTime = (int) $data['alliance_building_build_time'];
        $this->buildFactor = (float) $data['alliance_building_costs_factor'];
        $this->lastLevel = (int) $data['alliance_building_last_level'];
        $this->show = (bool) $data['alliance_building_show'];
        $this->neededId = (int) $data['alliance_building_needed_id'];
        $this->neededLevel = (int) $data['alliance_building_needed_level'];
    }
}
