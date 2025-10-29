<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Planet;
use EtoA\Entity\Ship;
use EtoA\Entity\User;

class ShipQueueSearch extends AbstractSearch
{
    public static function create(): ShipQueueSearch
    {
        return new ShipQueueSearch();
    }

    public function userId(int|User $user): self
    {
        $this->parts[] = 'q.user = :userId';
        $this->parameters['userId'] = $user;

        return $this;
    }

    public function likeUserNick(string $userNick): self
    {
        $this->parts[] = 'users.nick LIKE :likeUserNick';
        $this->parameters['likeUserNick'] = '%' . $userNick . '%';

        return $this;
    }

    public function entityId(Planet $entity): self
    {
        $this->parts[] = 'q.entity = :entity';
        $this->parameters['entity'] = $entity;

        return $this;
    }

    public function likePlanetName(string $planetName): self
    {
        $this->parts[] = 'planets.name = :likePlanetName';
        $this->parameters['likePlanetName'] = '%' . $planetName . '%';

        return $this;
    }

    public function shipId(int|Ship $ship): self
    {
        $this->parts[] = 'q.ship = :shipId';
        $this->parameters['shipId'] = $ship;

        return $this;
    }

    public function startEqualAfter(int $time): self
    {
        $this->parts[] = 'q.startTime >= :startEqualAfter';
        $this->parameters['startEqualAfter'] = $time;

        return $this;
    }

    public function endAfter(int $time): self
    {
        $this->parts[] = 'q.endTime > :endAfter';
        $this->parameters['endAfter'] = $time;

        return $this;
    }
}
