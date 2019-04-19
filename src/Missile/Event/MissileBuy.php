<?php declare(strict_types=1);

namespace EtoA\Missile\Event;

use Symfony\Component\EventDispatcher\Event;

class MissileBuy extends Event
{
    const BUY_SUCCESS = 'missile.buy.success';

    /** @var int */
    private $missileId;
    /** @var int */
    private $count;

    public function __construct($missileId, $count)
    {
        $this->missileId = $missileId;
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
