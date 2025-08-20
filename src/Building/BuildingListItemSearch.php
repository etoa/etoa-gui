<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Building;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class BuildingListItemSearch extends AbstractSearch
{
    public static function create(): BuildingListItemSearch
    {
        return new BuildingListItemSearch();
    }

    public function user(User|int $user): self
    {
        $this->parts[] = 'q.user = :user';
        $this->parameters['user'] = $user;

        return $this;
    }

    public function entity(Planet|int $entity): self
    {
        $this->parts[] = 'q.entity = :entity';
        $this->parameters['entity'] = $entity;

        return $this;
    }

    public function building(Building|int $building): self
    {
        $this->parts[] = 'q.building = :building';
        $this->parameters['building'] = $building;

        return $this;
    }

    public function buildType(int $buildType): self
    {
        $this->parts[] = 'q.buildType = :buildType';
        $this->parameters['buildType'] = $buildType;

        return $this;
    }
}
