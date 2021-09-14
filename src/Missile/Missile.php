<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Core\ObjectWithImage;

class Missile implements ObjectWithImage
{
    public int $id;
    public string $name;
    public string $shortDescription;
    public string $longDescription;
    public int $costsMetal;
    public int $costsCrystal;
    public int $costsPlastic;
    public int $costsFuel;
    public int $costsFood;
    public int $damage;
    public int $speed;
    public int $range;
    public int $deactivate;
    public int $def;
    public bool $launchable;
    public bool $show;

    public function __construct(array $data)
    {
        $this->id = (int) $data['missile_id'];
        $this->name = $data['missile_name'];
        $this->shortDescription = $data['missile_sdesc'];
        $this->longDescription = $data['missile_ldesc'];
        $this->costsMetal = (int) $data['missile_costs_metal'];
        $this->costsCrystal = (int) $data['missile_costs_crystal'];
        $this->costsPlastic = (int) $data['missile_costs_plastic'];
        $this->costsFuel = (int) $data['missile_costs_fuel'];
        $this->costsFood = (int) $data['missile_costs_food'];
        $this->damage = (int) $data['missile_damage'];
        $this->speed = (int) $data['missile_speed'];
        $this->range = (int) $data['missile_range'];
        $this->deactivate = (int) $data['missile_deactivate'];
        $this->def = (int) $data['missile_def'];
        $this->launchable = (bool) $data['missile_launchable'];
        $this->show = (bool) $data['missile_show'];
    }

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/missiles/missile".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH."/missiles/missile".$this->id."_middle.png";
            default:
                return self::BASE_PATH."/missiles/missile".$this->id.".png";
        }
    }
}
