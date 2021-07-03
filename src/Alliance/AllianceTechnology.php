<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceTechnology
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
        $this->id = (int) $data['alliance_tech_id'];
        $this->name = $data['alliance_tech_name'];
        $this->shortComment = $data['alliance_tech_shortcomment'];
        $this->longComment = $data['alliance_tech_longcomment'];
        $this->costsMetal = (int) $data['alliance_tech_costs_metal'];
        $this->costsCrystal = (int) $data['alliance_tech_costs_crystal'];
        $this->costsPlastic = (int) $data['alliance_tech_costs_plastic'];
        $this->costsFuel = (int) $data['alliance_tech_costs_fuel'];
        $this->costsFood = (int) $data['alliance_tech_costs_food'];
        $this->buildTime = (int) $data['alliance_tech_build_time'];
        $this->buildFactor = (float) $data['alliance_tech_costs_factor'];
        $this->lastLevel = (int) $data['alliance_tech_last_level'];
        $this->show = (bool) $data['alliance_tech_show'];
        $this->neededId = (int) $data['alliance_tech_needed_id'];
        $this->neededLevel = (int) $data['alliance_tech_needed_level'];
    }
}
