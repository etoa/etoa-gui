<?php

namespace EtoA\Defense\Event;

use Symfony\Component\EventDispatcher\Event;

class DefenseRecycle extends Event
{
    const RECYCLE_SUCCESS = 'defense.recycle.success';

    /** @var int */
    private $defenseId;
    /** @var int */
    private $count;

    public function __construct($defenseId, $count)
    {
        $this->defenseId = $defenseId;
        $this->count = $count;
    }

    public function getCount()
    {
        return $this->count;
    }
}
