<?php

declare(strict_types=1);

namespace EtoA\Defense;

class DefenseListItem
{
    public int $id;
    public int $userId;
    public int $defenseId;
    public int $entityId;
    public int $count;

    public static function createFromData(array $data): DefenseListItem
    {
        $item = new DefenseListItem();
        $item->id = (int) $data['deflist_id'];
        $item->userId = (int) $data['deflist_user_id'];
        $item->defenseId = (int) $data['deflist_def_id'];
        $item->entityId = (int) $data['deflist_entity_id'];
        $item->count = (int) $data['deflist_count'];

        return $item;
    }

    public static function empty(): DefenseListItem
    {
        $item = new DefenseListItem();
        $item->id = 0;
        $item->userId = 0;
        $item->entityId = 0;
        $item->defenseId = 0;
        $item->count = 0;

        return $item;
    }
}
