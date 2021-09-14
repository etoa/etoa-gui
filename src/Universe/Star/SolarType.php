<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use EtoA\Core\ObjectWithImage;

class SolarType implements ObjectWithImage
{
    public int $id;
    public string $name;
    public float $metal;
    public float $crystal;
    public float $plastic;
    public float $fuel;
    public float $food;
    public float $power;
    public float $people;
    public float $buildTime;
    public string $comment;
    public float $researchTime;
    public bool $consider;

    public function __construct(array $data)
    {
        $this->id = (int) $data['sol_type_id'];
        $this->name = $data['sol_type_name'];
        $this->metal = (float) $data['sol_type_f_metal'];
        $this->crystal = (float) $data['sol_type_f_crystal'];
        $this->plastic = (float) $data['sol_type_f_plastic'];
        $this->fuel = (float) $data['sol_type_f_fuel'];
        $this->food = (float) $data['sol_type_f_food'];
        $this->power = (float) $data['sol_type_f_power'];
        $this->people = (float) $data['sol_type_f_population'];
        $this->buildTime = (float) $data['sol_type_f_buildtime'];
        $this->comment = $data['sol_type_comment'];
        $this->researchTime = (float) $data['sol_type_f_researchtime'];
        $this->consider = (bool) $data['sol_type_consider'];
    }

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/stars/star".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH."/stars/star".$this->id."_middle.png";
            default:
                return self::BASE_PATH."/stars/star".$this->id.".png";
        }
    }
}
