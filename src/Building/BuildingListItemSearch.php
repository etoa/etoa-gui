<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Database\AbstractSearch;

class BuildingListItemSearch extends AbstractSearch
{
    public static function create(): BuildingListItemSearch
    {
        return new BuildingListItemSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'buildlist_user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function entityId(int $entityId): self
    {
        $this->parts[] = 'buildlist_entity_id = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function buildingId(int $buildingId): self
    {
        $this->parts[] = 'buildlist_building_id = :buildingId';
        $this->parameters['buildingId'] = $buildingId;

        return $this;
    }

    public function buildType(int $buildType): self
    {
        $this->parts[] = 'buildlist_build_type = :buildType';
        $this->parameters['buildType'] = $buildType;

        return $this;
    }
}
