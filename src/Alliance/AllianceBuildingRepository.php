<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceBuilding;
use EtoA\Entity\AllianceBuildListItem;

class AllianceBuildingRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceBuilding::class);
    }

    /**
     * @return array<int, string>
     */
    public function getNames(bool $orderById = false): array
    {
        return $this->fetchIdsWithNames('alliance_buildings', 'alliance_building_id', 'alliance_building_name', $orderById);
    }

    /**
     * @return AllianceBuilding[]
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('alliance_buildings')
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $building = new AllianceBuilding($row);
            $result[$building->id] = $building;
        }

        return $result;
    }

    public function getUserCooldown(int $userId, int $buildingId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('cooldown_end')
            ->from('alliance_building_cooldown')
            ->where('cooldown_user_id = :userId')
            ->andWhere('cooldown_alliance_building_id = :buildingId')
            ->setParameters([
                'userId' => $userId,
                'buildingId' => $buildingId,
            ])
            ->fetchOne();
    }

    public function setUserCooldown(int $userId, int $buildingId, int $cooldownEnd): void
    {
        $this->getConnection()->executeStatement(
            "REPLACE INTO
                alliance_building_cooldown
            (
                cooldown_user_id,
                cooldown_alliance_building_id,
                cooldown_end
            ) VALUES (
                :userId,
                :buildingId,
                :cooldownEnd
            );",
            [
                'userId' => $userId,
                'buildingId' => $buildingId,
                'cooldownEnd' => $cooldownEnd,
            ]
        );
    }


}
