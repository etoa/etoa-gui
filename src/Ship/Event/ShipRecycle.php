<?php declare(strict_types=1);

namespace EtoA\Ship\Event;

use Symfony\Component\EventDispatcher\Event;

class ShipRecycle extends Event
{
    const RECYCLE_SUCCESS = 'ship.recycle.success';

    /** @var int */
    private $shipId;
    /** @var int */
    private $count;

    public function __construct($shipId, $count)
    {
        $this->shipId = $shipId;
        $this->count = $count;
    }

    public function getCount()
    {
        return $this->count;
    }
}
