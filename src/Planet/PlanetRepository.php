<?php declare(strict_types=1);

namespace EtoA\Planet;

use EtoA\Core\AbstractRepository;

class PlanetRepository extends AbstractRepository
{
    public function getUserMainId(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('p.id')
            ->from('planets', 'p')
            ->where('p.planet_user_main = 1')
            ->andWhere('p.planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->execute()->fetchColumn();
    }

    public function getPlanetCount(int $userId): int
    {
        return (int)$this->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from('planets', 'p')
            ->where('p.planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->execute()->fetchColumn();
    }
}
