<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\ObjectWithImage;

class Technology implements ObjectWithImage
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
    public float $buildCostsFactor;
    public int $lastLevel;
    public bool $show;
    public int $order;
    public bool $stealable;

    public function __construct(array $data)
    {
        $this->id = (int) $data['tech_id'];
        $this->name = $data['tech_name'];
        $this->typeId = (int) $data['tech_type_id'];
        $this->shortComment = $data['tech_shortcomment'];
        $this->longComment = $data['tech_longcomment'];
        $this->costsMetal = (int) $data['tech_costs_metal'];
        $this->costsCrystal = (int) $data['tech_costs_crystal'];
        $this->costsPlastic = (int) $data['tech_costs_plastic'];
        $this->costsFuel = (int) $data['tech_costs_fuel'];
        $this->costsFood = (int) $data['tech_costs_food'];
        $this->costsPower = (int) $data['tech_costs_power'];
        $this->buildCostsFactor = (float) $data['tech_build_costs_factor'];
        $this->lastLevel = (int) $data['tech_last_level'];
        $this->show = (bool) $data['tech_show'];
        $this->order = (int) $data['tech_order'];
        $this->stealable = (bool) $data['tech_stealable'];
    }

    public function getImagePath(string $type = 'small'): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH . "/technologies/technology".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH . "/technologies/technology".$this->id."_middle.png";
            default:
                return self::BASE_PATH . "/technologies/technology".$this->id.".png";
        }
    }
}
