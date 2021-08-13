<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\Database\AbstractSearch;

class ShipQueueSearch extends AbstractSearch
{
    public static function create(): ShipQueueSearch
    {
        return new ShipQueueSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'queue_user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function likeUserNick(string $userNick): self
    {
        $this->parts[] = 'users.user_nick LIKE :likeUserNick';
        $this->parameters['likeUserNick'] = '%' . $userNick . '%';

        return $this;
    }

    public function entityId(int $entityId): self
    {
        $this->parts[] = 'queue_entity_id = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function likePlanetName(string $planetName): self
    {
        $this->parts[] = 'planets.planet_name = :likePlanetName';
        $this->parameters['likePlanetName'] = '%' . $planetName . '%';

        return $this;
    }

    public function shipId(int $shipId): self
    {
        $this->parts[] = 'queue_ship_id = :shipId';
        $this->parameters['shipId'] = $shipId;

        return $this;
    }

    public function startEqualAfter(int $time): self
    {
        $this->parts[] = 'queue_endtime >= :startEqualAfter';
        $this->parameters['startEqualAfter'] = $time;

        return $this;
    }

    public function endAfter(int $time): self
    {
        $this->parts[] = 'queue_endtime > :endAfter';
        $this->parameters['endAfter'] = $time;

        return $this;
    }
}
