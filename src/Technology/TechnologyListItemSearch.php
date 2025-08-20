<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Entity;
use EtoA\Entity\Technology;
use EtoA\Entity\User;

class TechnologyListItemSearch extends AbstractSearch
{
    public static function create(): TechnologyListItemSearch
    {
        return new TechnologyListItemSearch();
    }

    public function userId(User|int $userId): self
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

    public function entityId(Entity|int $entityId): self
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

    public function technologyId(int|Technology $technologyId): self
    {
        $this->parts[] = 'q.technology = :technologyId';
        $this->parameters['technologyId'] = $technologyId;

        return $this;
    }

    public function notTechnologyId(int $technologyId): self
    {
        $this->parts[] = 'q.technology <> :notTechnologyId';
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
