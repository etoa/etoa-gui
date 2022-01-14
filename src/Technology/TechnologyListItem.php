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

    public static function createFromData(array $data): TechnologyListItem
    {
        $item = new TechnologyListItem();
        $item->id = (int) $data['techlist_id'];
        $item->userId = (int) $data['techlist_user_id'];
        $item->technologyId = (int) $data['techlist_tech_id'];
        $item->entityId = (int) $data['techlist_entity_id'];
        $item->currentLevel = (int) $data['techlist_current_level'];
        $item->buildType = (int) $data['techlist_build_type'];
        $item->startTime = (int) $data['techlist_build_start_time'];
        $item->endTime = (int) $data['techlist_build_end_time'];
        $item->prodPercent = (int) $data['techlist_prod_percent'];

        return $item;
    }

    public static function empty(): TechnologyListItem
    {
        $item = new TechnologyListItem();
        $item->id = 0;
        $item->userId = 0;
        $item->technologyId = 0;
        $item->entityId = 0;
        $item->currentLevel = 0;
        $item->buildType = 0;
        $item->startTime = 0;
        $item->endTime = 0;
        $item->prodPercent = 0;

        return $item;
    }
}
