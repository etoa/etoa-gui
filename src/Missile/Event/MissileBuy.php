<?php declare(strict_types=1);

namespace EtoA\Missile\Event;

use Symfony\Contracts\EventDispatcher\Event;

class MissileBuy extends Event
{
    public const BUY_SUCCESS = 'missile.buy.success';

    private int $missileId;
    private int $count;

    public function __construct(int $missileId, int $count)
    {
        $this->missileId = $missileId;
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getMissileId(): int
    {
        return $this->missileId;
    }
}
