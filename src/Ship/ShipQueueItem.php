<?php

declare(strict_types=1);

namespace EtoA\Ship;

class ShipQueueItem
{
    public int $id;
    public int $userId;
    public int $shipId;
    public int $entityId;
    public int $count;
    public int $startTime;
    public int $endTime;
    public int $objectTime;
    public int $buildType;
    public int $userClickTime;

    public static function createFromData(array $data): ShipQueueItem
    {
        $item = new ShipQueueItem();
        $item->id = (int) $data['queue_id'];
        $item->userId = (int) $data['queue_user_id'];
        $item->shipId = (int) $data['queue_ship_id'];
        $item->entityId = (int) $data['queue_entity_id'];
        $item->count = (int) $data['queue_cnt'];
        $item->startTime = (int) $data['queue_starttime'];
        $item->endTime = (int) $data['queue_endtime'];
        $item->objectTime = (int) $data['queue_objtime'];
        $item->buildType = (int) $data['queue_build_type'];

        return $item;
    }
}
