<?php declare(strict_types=1);

namespace EtoA\Ship\Event;

use Symfony\Component\EventDispatcher\Event;

class ShipRecycle extends Event
{
    public const RECYCLE_SUCCESS = 'ship.recycle.success';

    /** @var int */
    private $shipId;
    /** @var int */
    private $count;

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
