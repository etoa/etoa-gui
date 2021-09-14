<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

class PlanetType
{
    public int $id;
    public string $name;
    public bool $habitable;
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
    public bool $collectGas;

    public function __construct(array $data)
    {
        $this->id = (int) $data['type_id'];
        $this->name = $data['type_name'];
        $this->habitable = (bool) $data['type_habitable'];
        $this->metal = (float) $data['type_f_metal'];
        $this->crystal = (float) $data['type_f_crystal'];
        $this->plastic = (float) $data['type_f_plastic'];
        $this->fuel = (float) $data['type_f_fuel'];
        $this->food = (float) $data['type_f_food'];
        $this->power = (float) $data['type_f_power'];
        $this->people = (float) $data['type_f_population'];
        $this->buildTime = (float) $data['type_f_buildtime'];
        $this->comment = $data['type_comment'];
        $this->researchTime = (float) $data['type_f_researchtime'];
        $this->consider = (bool) $data['type_consider'];
        $this->collectGas = (bool) $data['type_consider'];
    }

    public function getImagePath(string $type = "small", int $imageNumber = 1): string
    {
        switch ($type) {
            case 'small':
                return IMAGE_PATH . "/planets/planet" . $this->id . '_' . $imageNumber . "_small.png";
            case 'medium':
                return IMAGE_PATH . "/planets/planet" . $this->id . '_' . $imageNumber . "_middle.png";
            default:
                return IMAGE_PATH . "/planets/planet" . $this->id . '_' . $imageNumber . ".png";
        }
    }
}
