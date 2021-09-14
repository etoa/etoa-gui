<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Universe\Resources\BaseResources;

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

    public function getImagePath(): string
    {
        return IMAGE_PATH . "/abuildings/building" . $this->id . "_middle.png";
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
}
