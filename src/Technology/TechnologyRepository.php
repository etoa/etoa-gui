<?php

declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;

class TechnologyRepository extends AbstractRepository
{
    /**
     * @return TechnologyListItem[]
     */
    public function findForUser(int $userId, int $endTimeAfter = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('techlist')
            ->where('techlist_user_id = :userId')
            ->setParameter('userId', $userId);

        if ($endTimeAfter !== null) {
            $qb
                ->andWhere('techlist_build_end_time > :time')
                ->setParameter('time', $endTimeAfter);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn ($row) => TechnologyListItem::createFromData($row), $data);
    }

    public function getEntry(int $id): ?TechnologyListItem
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('techlist')
            ->where('techlist_id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? TechnologyListItem::createFromData($data) : null;
    }

    public function searchEntry(TechnologyListItemSearch $search): ?TechnologyListItem
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, 1)
            ->select('*')
            ->from('techlist')
            ->fetchAssociative();

        return $data !== false ? TechnologyListItem::createFromData($data) : null;
    }

    public function save(TechnologyListItem $item): void
    {
        $this->createQueryBuilder()
            ->update('techlist')
            ->set('techlist_user_id', ':userId')
            ->set('techlist_tech_id', ':technologyId')
            ->set('techlist_entity_id', ':entityId')
            ->set('techlist_current_level', ':currentLevel')
            ->set('techlist_build_type', ':buildType')
            ->set('techlist_build_start_time', ':startTime')
            ->set('techlist_build_end_time', ':endTime')
            ->set('techlist_prod_percent', ':prodPercent')
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
            ->executeQuery();
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
            ])
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
            ])
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
        ])->rowCount();
    }

    public function countResearchInProgress(int $userId, int $entityId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('COUNT(techlist_id)')
            ->from('techlist')
            ->where('techlist_user_id = :userId')
            ->where('techlist_entity_id = :entityId')
            ->andWhere('techlist_build_type > 2')
            ->andWhere('techlist_tech_id <> :techId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'techId' => TechnologyId::GEN,
            ])
            ->fetchOne();
    }

    public function isTechInProgress(int $userId, int $technologyId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('techlist')
            ->where('techlist_user_id = :userId')
            ->andWhere('techlist_build_type > 2')
            ->andWhere('techlist_tech_id = :techId')
            ->setParameters([
                'userId' => $userId,
                'techId' => $technologyId,
            ])
            ->fetchOne();
    }

    public function count(TechnologyListItemSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(techlist_id)')
            ->from('techlist')
            ->fetchOne();
    }

    public function countEmpty(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(techlist_id)')
            ->from('techlist')
            ->where('techlist_current_level=0')
            ->andWhere('techlist_build_start_time=0')
            ->andWhere('techlist_build_end_time=0')
            ->fetchOne();
    }

    public function deleteEmpty(): int
    {
        return $this->createQueryBuilder()
            ->delete('techlist')
            ->where('techlist_current_level=0')
            ->andWhere('techlist_build_start_time=0')
            ->andWhere('techlist_build_end_time=0')
            ->executeQuery()
            ->rowCount();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('techlist')
            ->where('techlist_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    /**
     * @return array<int, array{name: string, max: int}>
     */
    public function getBestLevels(): array
    {
        $data = $this->getConnection()
            ->fetchAllAssociative(
                "SELECT
                    technologies.tech_name as name,
                    MAX(techlist.techlist_current_level) as max
                FROM
                    technologies
                INNER JOIN
                    (
                        techlist
                    INNER JOIN
                        users
                    ON
                        techlist_user_id = user_id
                        AND user_ghost = 0
                        AND user_hmode_from = 0
                        AND user_hmode_to = 0
                    )
                ON
                    tech_id = techlist_tech_id
                GROUP BY
                    technologies.tech_id
                ORDER BY
                    max DESC;"
            );

        return array_map(fn ($arr) => [
            'name' => $arr['name'],
            'max' => (int) $arr['max'],
        ], $data);
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('techlist')
            ->where('techlist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }

    public function freezeConstruction(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('techlist')
            ->set('techlist_build_type', 'techlist_build_type - 2')
            ->where('techlist_user_id = :userId')
            ->andWhere('techlist_build_start_time > 0')
            ->setParameters([
                'userId' => $userId,
                'type' => 1,
            ])
            ->executeQuery();
    }

    public function unfreezeConstruction(int $userId, int $duration): void
    {
        $this->createQueryBuilder()
            ->update('techlist')
            ->set('techlist_build_type', 'techlist_build_type + 2')
            ->set('techlist_build_start_time', 'techlist_build_start_time + :duration')
            ->set('techlist_build_end_time', 'techlist_build_end_time + :duration')
            ->where('techlist_user_id = :userId')
            ->andWhere('techlist_build_start_time > 0')
            ->setParameters([
                'userId' => $userId,
                'duration' => $duration,
            ])
            ->executeQuery();
    }

    /**
     * @return TechnologyListItem[]
     */
    public function search(TechnologyListItemSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit, $offset)
            ->select('techlist.*')
            ->from('techlist')
            ->fetchAllAssociative();

        return array_map(fn ($row) => TechnologyListItem::createFromData($row), $data);
    }
}
