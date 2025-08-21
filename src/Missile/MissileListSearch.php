<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Missile;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class MissileListSearch extends AbstractSearch
{
    public static function create(): MissileListSearch
    {
        return new MissileListSearch();
    }

    public function id(int $id): MissileListSearch
    {
        $this->parts[] = "q.id = :id";
        $this->parameters['id'] = $id;

        return $this;
    }

    public function missileId(Missile|int $missileId): MissileListSearch
    {
        $this->parts[] = "q.missile = :missileId";
        $this->parameters['missileId'] = $missileId;

        return $this;
    }

    public function userId(User|int $userId): MissileListSearch
    {
        $this->parts[] = "q.user = :userId";
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function entityId(Planet|int $entityId): MissileListSearch
    {
        $this->parts[] = "q.entity = :entityId";
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function hasMissiles(): MissileListSearch
    {
        $this->parts[] = "q.count > 0";

        return $this;
    }
}
