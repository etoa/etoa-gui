<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\AbstractRepository;

class BuildingRepository extends AbstractRepository
{
    public function getBuildingLevel(int $userId, int $buildingId): int
    {
        return (int)$this->createQueryBuilder()
            ->select('MAX(buildlist_current_level)')
            ->from('buildlist')
            ->where('buildlist_building_id = :buildingId')
            ->andWhere('buildlist_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'buildingId' => $buildingId,
            ])
            ->execute()
            ->fetchColumn();
    }
}
