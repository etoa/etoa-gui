<?php

declare(strict_types=1);

namespace EtoA\Universe\Nebula;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;

class Nebula extends AbstractEntity implements ObjectWithImage
{
    public int $id;
    public int $resMetal;
    public int $resCrystal;
    public int $resPlastic;
    public int $resFuel;
    public int $resFood;
    public int $resPower;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->resMetal = (int) $data['res_metal'];
        $this->resCrystal = (int) $data['res_crystal'];
        $this->resPlastic = (int) $data['res_plastic'];
        $this->resFuel = (int) $data['res_fuel'];
        $this->resFood = (int) $data['res_food'];
        $this->resPower = (int) $data['res_power'];
    }

    public function getImagePath(string $type = ""):string
    {
        $numImages = 9;
        $r = ($this->id % $numImages) + 1;
        return ObjectWithImage::BASE_PATH . "/nebulas/nebula" . $r . "_small.png";
    }

    public function getAllowedFleetActions():array
    {
        return [FleetAction::COLLECT_CRYSTAL, FleetAction::ANALYZE, FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getEntityCodeString(): string
    {
        return "Interstellarer Gasnebel";
    }
}
