<?php

namespace EtoA\Planet;

use EtoA\Core\AbstractRepository;

class PlanetRepository extends AbstractRepository
{
    public function getUserMainId($userId)
    {
        return $this->createQueryBuilder()
            ->select('p.id')
            ->from('planets', 'p')
            ->where('p.planet_user_main = 1')
            ->andWhere('p.planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->execute()->fetchColumn();
    }
}
