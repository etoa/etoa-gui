<?php declare(strict_types=1);

namespace EtoA\Ship\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ShipRecycle extends Event
{
    public const RECYCLE_SUCCESS = 'ship.recycle.success';

    private int $shipId;
    private int $count;

    public function __construct(int $shipId, int $count)
    {
        $this->shipId = $shipId;
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
