<?php

declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;

class TechnologyRepository extends AbstractRepository
{
    /**
     * @return TechnologyListItem[]
     */
    public function findForUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('techlist')
            ->where('techlist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new TechnologyListItem($row), $data);
    }

    public function save(TechnologyListItem $item): void
    {
        $this->createQueryBuilder()
            ->update('techlist')
            ->set('techlist_user_id', 'userId')
            ->set('techlist_tech_id', 'technologyId')
            ->set('techlist_entity_id', 'entityId')
            ->set('techlist_current_level', 'currentLevel')
            ->set('techlist_build_type', 'buildType')
            ->set('techlist_build_start_time', 'startTime')
            ->set('techlist_build_end_time', 'endTime')
            ->set('techlist_prod_percent', 'prodPercent')
            ->where('techlist_id = :id')
            ->setParameters([
                'id' => $item->id,
                'userId' => $item->userId,
                'technologyId' => $item->technologyId,
                'entityId' => $item->entityId,
                'currentLevel' => $item->currentLevel,
                'buildType' => $item->buildType,
                'startTime' => $item->startTime,
                'endTime' => $item->endTime,
                'prodPercent' => $item->prodPercent,
            ])
            ->execute();
    }

    /**
     * @return array<int, int>
     */
    public function getTechnologyLevels(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('techlist_tech_id, techlist_current_level')
            ->from('techlist')
            ->where('techlist_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function getTechnologyLevel(int $userId, int $technologyId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('techlist_current_level')
            ->from('techlist')
            ->where('techlist_tech_id = :technologyId')
            ->andWhere('techlist_user_id = :userId')
            ->setParameters([
                'technologyId' => $technologyId,
                'userId' => $userId,
            ])->execute()
            ->fetchOne();
    }

    public function addTechnology(int $technologyId, int $level, int $userId, int $entityId): void
    {
        $this->getConnection()->executeQuery('INSERT INTO techlist (
                techlist_user_id,
                techlist_entity_id,
                techlist_tech_id,
                techlist_current_level
            ) VALUES (
                :userId,
                :entityId,
                :technologyId,
                :level
            ) ON DUPLICATE KEY
            UPDATE techlist_current_level = :level;
        ', [
            'userId' => $userId,
            'level' => max(0, $level),
            'entityId' => $entityId,
            'technologyId' => $technologyId,
        ]);
    }

    public function updateBuildStatus(int $userId, int $entityId, int $technologyId, int $status, int $startTime, int $endTime): bool
    {
        return (bool) $this->getConnection()->executeQuery('INSERT INTO techlist (
                techlist_user_id,
                techlist_entity_id,
                techlist_tech_id,
                techlist_build_type,
                techlist_build_start_time,
                techlist_build_end_time
            ) VALUES (
                :userId,
                :entityId,
                :technologyId,
                :status,
                :startTime,
                :endTime
            ) ON DUPLICATE KEY
            UPDATE techlist_entity_id = :entityId, techlist_build_type = :status, techlist_build_start_time = :startTime, techlist_build_end_time = :endTime;
        ', [
            'userId' => $userId,
            'entityId' => $entityId,
            'technologyId' => $technologyId,
            'status' => $status,
            'startTime' => $startTime,
            'endTime' => $endTime,
        ]);
    }
}
