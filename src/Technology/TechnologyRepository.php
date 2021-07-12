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
}
