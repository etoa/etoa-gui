<?php declare(strict_types=1);

namespace EtoA\Ship\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ShipUpgrade extends Event
{
    const UPGRADE_SUCCESS = 'ship.upgrade.success';
}
