<?php declare(strict_types=1);

namespace EtoA\Missile\Event;

use Symfony\Component\EventDispatcher\Event;

class MissileLaunch extends Event
{
    public const LAUNCH_SUCCESS = 'missile.launch.success';

    /** @var int[] */
    private $missiles;

    public function __construct(array $missiles)
    {
        $this->missiles = $missiles;
    }

    public function getMissileCount(): int
    {
        return array_reduce($this->missiles, function ($total, $count) {
            return $total + $count;
        }, 0);
    }
}
