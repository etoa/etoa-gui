<?php declare(strict_types=1);

namespace EtoA\Fleet\Event;

use Symfony\Component\EventDispatcher\Event;

class FleetLaunch extends Event
{
    const LAUNCH_SUCCESS = 'fleet.launch.success';
}
