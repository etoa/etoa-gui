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
}
