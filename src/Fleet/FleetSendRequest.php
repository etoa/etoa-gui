<?php declare(strict_types=1);

namespace EtoA\Fleet;

class FleetSendRequest
{
    public int $launchTime;
    public int $landTime;
    public int $entityFrom;
    public int $count;
    public int $shipId;

    public static function new(): FleetSendRequest
    {
        $request = new FleetSendRequest();
        $request->launchTime = time();
        $request->landTime = time() + 3600;
        $request->entityFrom = 0;
        $request->count = 1;
        $request->shipId = 0;

        return $request;
    }
}
