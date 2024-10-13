<?php

declare(strict_types=1);

namespace EtoA\Universe\Wormhole;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;

class Wormhole extends AbstractEntity implements ObjectWithImage
{
    public int $id;
    public int $targetId;
    public int $changed;
    public bool $persistent;

    public function __construct(array $arr)
    {
        $this->id = (int) $arr['id'];
        $this->targetId = (int) $arr['target_id'];
        $this->changed = (int) $arr['changed'];
        $this->persistent = (bool) $arr['persistent'];
    }

    public function getEntityCodeString(): string
    {
        return "Wurmloch";
    }

    public function getAllowedFleetActions(): array
    {
        return [FleetAction::FLIGHT, FleetAction::EXPLORE];
    }

    public function getImagePath(): string
    {
        $prefix = $this->persistent ? 'wormhole_persistent' : 'wormhole';
        return ObjectWithImage::BASE_PATH . "/wormholes/" . $prefix . "1_small.png";
    }
}
