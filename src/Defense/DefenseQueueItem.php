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

    public function __construct(array $data)
    {
        $this->id = (int) $data['queue_id'];
        $this->userId = (int) $data['queue_user_id'];
        $this->defenseId = (int) $data['queue_def_id'];
        $this->entityId = (int) $data['queue_entity_id'];
        $this->count = (int) $data['queue_cnt'];
        $this->startTime = (int) $data['queue_starttime'];
        $this->endTime = (int) $data['queue_endtime'];
        $this->objectTime = (int) $data['queue_objtime'];
        $this->buildType = (int) $data['queue_build_type'];
        $this->userClickTime = (int) $data['queue_user_click_time'];
    }
}
