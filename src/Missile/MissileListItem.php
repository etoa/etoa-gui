<?php declare(strict_types=1);

namespace EtoA\Missile;

class MissileListItem
{
    public int $id;
    public int $userId;
    public int $entityId;
    public int $missileId;
    public int $count;

    public function __construct(array $data)
    {
        $this->id = (int) $data['missilelist_id'];
        $this->userId = (int) $data['missilelist_user_id'];
        $this->entityId = (int) $data['missilelist_entity_id'];
        $this->missileId = (int) $data['missilelist_missile_id'];
        $this->count = (int) $data['missilelist_count'];
    }
}
