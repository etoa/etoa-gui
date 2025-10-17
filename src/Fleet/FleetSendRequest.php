<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Entity\Entity;
use EtoA\Entity\Ship;

class FleetSendRequest
{
    public int $launchTime;
    public int $landTime;
    public ?Entity $entityFrom;
    public int $count;
    public ?Ship $ship;

    public static function new(): FleetSendRequest
    {
        $request = new FleetSendRequest();
        $request->launchTime = time();
        $request->landTime = time() + 3600;
        $request->entityFrom = null;
        $request->count = 1;
        $request->ship = null;

        return $request;
    }
}
