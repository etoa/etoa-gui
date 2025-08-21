<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Defense;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class DefenseListSearch extends AbstractSearch
{
    public static function create(): DefenseListSearch
    {
        return new DefenseListSearch();
    }

    public function userId(int|User $userId): self
    {
        $this->parts[] = 'q.user = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function likeUserNick(string $userNick): self
    {
        $this->parts[] = 'users.nick LIKE :likeUserNick';
        $this->parameters['likeUserNick'] = '%' . $userNick . '%';

        return $this;
    }

    public function entityId(int|Planet $entityId): self
    {
        $this->parts[] = 'q.entity = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function likePlanetName(string $planetName): self
    {
        $this->parts[] = 'planets.name LIKE :likePlanetName';
        $this->parameters['likePlanetName'] = '%' . $planetName . '%';

        return $this;
    }

    public function defenseId(int|Defense $defenseId): self
    {
        $this->parts[] = 'q.defense = :defenseId';
        $this->parameters['defenseId'] = $defenseId;

        return $this;
    }
}
