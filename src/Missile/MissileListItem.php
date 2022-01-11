<?php declare(strict_types=1);

namespace EtoA\Missile;

class MissileListItem
{
    public int $id;
    public int $userId;
    public int $entityId;
    public int $missileId;
    public int $count;

    public static function createFromArray(array $data): MissileListItem
    {
        $item = new MissileListItem();
        $item->id = (int) $data['missilelist_id'];
        $item->userId = (int) $data['missilelist_user_id'];
        $item->entityId = (int) $data['missilelist_entity_id'];
        $item->missileId = (int) $data['missilelist_missile_id'];
        $item->count = (int) $data['missilelist_count'];

        return $item;
    }

    public static function empty(): MissileListItem
    {
        $item = new MissileListItem();
        $item->id = 0;
        $item->userId = 0;
        $item->entityId = 0;
        $item->missileId = 0;
        $item->count = 0;

        return $item;
    }
}
