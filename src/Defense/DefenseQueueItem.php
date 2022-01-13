<?php

declare(strict_types=1);

namespace EtoA\Defense;

class DefenseQueueItem
{
    public int $id;
    public int $userId;
    public int $defenseId;
    public int $entityId;
    public int $count;
    public int $startTime;
    public int $endTime;
    public int $objectTime;
    public int $buildType;
    public int $userClickTime;

    public static function createFromData(array $data): DefenseQueueItem
    {
        $item = new DefenseQueueItem();
        $item->id = (int) $data['queue_id'];
        $item->userId = (int) $data['queue_user_id'];
        $item->defenseId = (int) $data['queue_def_id'];
        $item->entityId = (int) $data['queue_entity_id'];
        $item->count = (int) $data['queue_cnt'];
        $item->startTime = (int) $data['queue_starttime'];
        $item->endTime = (int) $data['queue_endtime'];
        $item->objectTime = (int) $data['queue_objtime'];
        $item->buildType = (int) $data['queue_build_type'];
        $item->userClickTime = (int) $data['queue_user_click_time'];

        return $item;
    }
}
