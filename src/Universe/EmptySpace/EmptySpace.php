<?php

declare(strict_types=1);

namespace EtoA\Universe\EmptySpace;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;

class EmptySpace extends AbstractEntity implements ObjectWithImage
{
    public int $id;
    public int $lastVisited;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->lastVisited = (int) $data['lastvisited'];
    }

    public function getEntityCodeString(): string
    {
        return "Leerer Raum";
    }

    public function getAllowedFleetActions(): array
    {
        return [FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getImagePath(): string
    {
        $numImages = 10;
        $r = ($this->id % $numImages) + 1;
        return ObjectWithImage::BASE_PATH . "/space/space" . $r . "_small.png";
    }
}
