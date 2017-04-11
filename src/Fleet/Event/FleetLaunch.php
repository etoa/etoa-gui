<?php

namespace EtoA\Fleet\Event;

use Symfony\Component\EventDispatcher\Event;

class FleetLaunch extends Event
{
    const LAUNCH_SUCCESS = 'fleet.launch.success';
}
