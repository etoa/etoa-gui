<?php

namespace EtoA\Ship\Event;

use Symfony\Component\EventDispatcher\Event;

class ShipUpgrade extends Event
{
    const UPGRADE_SUCCESS = 'ship.upgrade.success';
}
