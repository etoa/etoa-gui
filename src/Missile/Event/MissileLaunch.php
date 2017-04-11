<?php

namespace EtoA\Missile\Event;

use Symfony\Component\EventDispatcher\Event;

class MissileLaunch extends Event
{
    const LAUNCH_SUCCESS = 'missile.launch.success';

    /** @var int[] */
    private $missiles;

    public function __construct(array $missiles)
    {
        $this->missiles = $missiles;
    }

    public function getMissileCount()
    {
        return array_reduce($this->missiles, function ($total, $count) {
            return $total + $count;
        }, 0);
    }
}
