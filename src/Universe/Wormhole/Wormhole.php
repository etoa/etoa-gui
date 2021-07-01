<?php

declare(strict_types=1);

namespace EtoA\Universe\Wormhole;

class Wormhole
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
}
