<?php declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class PlanetRepository extends AbstractRepository
{
    /**
     * @return Planet[]
     */
    public function getUserPlanets(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('planets')
            ->where('planet_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Planet($row), $data);
    }

    public function getPlanetUserId(int $planetId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('planet_user_id')
            ->from('planets')
            ->where('id = :planetId')
            ->setParameter('planetId', $planetId)
            ->execute()
            ->fetchOne();
    }

    public function getUserMainId(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('p.id')
            ->from('planets', 'p')
            ->where('p.planet_user_main = 1')
            ->andWhere('p.planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->execute()->fetchOne();
    }

    public function getPlanetCount(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from('planets', 'p')
            ->where('p.planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->execute()->fetchOne();
    }
}
