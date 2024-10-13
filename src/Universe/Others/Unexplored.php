<?php

namespace EtoA\Universe\Others;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;

class Unexplored extends AbstractEntity implements ObjectWithImage
{

    public function getEntityCodeString(): string
    {
        return "Unerforschte Raumzelle";
    }

    public function getAllowedFleetActions(): array
    {
        return [FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getImagePath(): string
    {
        return ObjectWithImage::BASE_PATH . "/unexplored/ue1.png";
    }
}