<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;

class TechnologyRepository extends AbstractRepository
{
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
}
