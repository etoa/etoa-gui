<?php

declare(strict_types=1);

namespace EtoA\Technology;

class TechnologyListItem
{
    public int $id;
    public int $userId;
    public int $technologyId;
    public int $entityId;
    public int $currentLevel;
    public int $buildType;
    public int $startTime;
    public int $endTime;
    public int $prodPercent;

    public function __construct(array $data)
    {
        $this->id = (int) $data['techlist_id'];
        $this->userId = (int) $data['techlist_user_id'];
        $this->technologyId = (int) $data['techlist_tech_id'];
        $this->entityId = (int) $data['techlist_entity_id'];
        $this->currentLevel = (int) $data['techlist_current_level'];
        $this->buildType = (int) $data['techlist_build_type'];
        $this->startTime = (int) $data['techlist_build_start_time'];
        $this->endTime = (int) $data['techlist_build_end_time'];
        $this->prodPercent = (int) $data['techlist_prod_percent'];
    }
}
