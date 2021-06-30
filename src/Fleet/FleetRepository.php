<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Core\AbstractRepository;

class FleetRepository extends AbstractRepository
{
    public function hasAnyFleetsWithAction(int $userId, string $action): bool
    {
        $data = $this->createQueryBuilder()
            ->select('id')
            ->from('fleet')
            ->where('user_id = :userId')
            ->andWhere('action = :action')
            ->setParameters([
                'userId' => $userId,
                'action' => $action,
            ])
            ->execute()
            ->fetchOne();

        return $data !== false;
    }
}
