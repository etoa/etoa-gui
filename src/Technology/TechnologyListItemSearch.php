<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\Database\AbstractSearch;

class TechnologyListItemSearch extends AbstractSearch
{
    public static function create(): TechnologyListItemSearch
    {
        return new TechnologyListItemSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'q.userId = :userId';
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
        $this->parts[] = 'techlist_entity_id = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function likePlanetName(string $planetName): self
    {
        $this->parts[] = 'planets.planet_name LIKE :likePlanetName';
        $this->parameters['likePlanetName'] = '%' . $planetName . '%';

        return $this;
    }

    public function technologyId(int $technologyId): self
    {
        $this->parts[] = 'q.technologyId = :technologyId';
        $this->parameters['technologyId'] = $technologyId;

        return $this;
    }

    public function notTechnologyId(int $technologyId): self
    {
        $this->parts[] = 'q.technologyId <> :notTechnologyId';
        $this->parameters['notTechnologyId'] = $technologyId;

        return $this;
    }

    public function buildType(int $buildType): self
    {
        $this->parts[] = 'q.buildType = :buildType';
        $this->parameters['buildType'] = $buildType;

        return $this;
    }

    public function underConstruction(): self
    {
        $this->parts[] = 'q.buildType > 0';

        return $this;
    }
}
