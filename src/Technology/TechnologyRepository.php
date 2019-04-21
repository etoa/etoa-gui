<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;

class TechnologyRepository extends AbstractRepository
{
    public function getTechnologyLevel(int $userId, int $technologyId): int
    {
        return (int)$this->createQueryBuilder()
            ->select('techlist_current_level')
            ->from('techlist')
            ->where('techlist_tech_id = :technologyId')
            ->andWhere('techlist_user_id = :userId')
            ->setParameters([
                'technologyId' => $technologyId,
                'userId' => $userId,
            ])->execute()
            ->fetchColumn();
    }
}
