<?php

namespace EtoA\Universe\Others;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;

class UnknownEntity extends AbstractEntity implements ObjectWithImage
{

    public function getEntityCodeString(): string
    {
        return "Unbekannter Raum";
    }

    public function getAllowedFleetActions(): array
    {
        return [FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getImagePath(): string
    {
        $r = mt_rand(1, 10);
        return ObjectWithImage::BASE_PATH . "/space/space" . $r . "_small.png";
    }
}