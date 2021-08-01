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

    public function __construct(array $data)
    {
        $this->id = (int) $data['deflist_id'];
        $this->userId = (int) $data['deflist_user_id'];
        $this->defenseId = (int) $data['deflist_def_id'];
        $this->entityId = (int) $data['deflist_entity_id'];
        $this->count = (int) $data['deflist_count'];
    }
}
