<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\Database\AbstractSearch;

class DefenseListSearch extends AbstractSearch
{
    public static function create(): DefenseListSearch
    {
        return new DefenseListSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'deflist_user_id = :userId';
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
        $this->parts[] = 'deflist_entity_id = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function likePlanetName(string $planetName): self
    {
        $this->parts[] = 'planets.planet_name LIKE :likePlanetName';
        $this->parameters['likePlanetName'] = '%' . $planetName . '%';

        return $this;
    }

    public function defenseId(int $defenseId): self
    {
        $this->parts[] = 'deflist_def_id = :defenseId';
        $this->parameters['defenseId'] = $defenseId;

        return $this;
    }
}
