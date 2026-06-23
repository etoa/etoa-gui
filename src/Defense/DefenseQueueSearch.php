<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Defense;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class DefenseQueueSearch extends AbstractSearch
{
    public static function create(): DefenseQueueSearch
    {
        return new DefenseQueueSearch();
    }

    public function user(User $user): self
    {
        $this->parts[] = 'q.user = :user';
        $this->parameters['user'] = $user;

        return $this;
    }

    public function likeUserNick(string $userNick): self
    {
        $this->parts[] = 'users.nick LIKE :likeUserNick';
        $this->parameters['likeUserNick'] = '%' . $userNick . '%';

        return $this;
    }

    public function entity(Planet|int $entity): self
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

    public function defenseId(Defense|int $defense): self
    {
        $this->parts[] = 'q.def = :defense';
        $this->parameters['defense'] = $defense;

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
