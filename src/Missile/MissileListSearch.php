<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Core\Database\AbstractSearch;

class MissileListSearch extends AbstractSearch
{
    public static function create(): MissileListSearch
    {
        return new MissileListSearch();
    }

    public function id(int $id): MissileListSearch
    {
        $this->parts[] = "missilelist_id = :id";
        $this->parameters['id'] = $id;

        return $this;
    }

    public function missileId(int $missileId): MissileListSearch
    {
        $this->parts[] = "missilelist_missile_id = :missileId";
        $this->parameters['missileId'] = $missileId;

        return $this;
    }

    public function userId(int $userId): MissileListSearch
    {
        $this->parts[] = "missilelist_user_id = :userId";
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function entityId(int $entityId): MissileListSearch
    {
        $this->parts[] = "missilelist_entity_id = :entityId";
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function hasMissiles(): MissileListSearch
    {
        $this->parts[] = "missilelist_count > 0";

        return $this;
    }
}
