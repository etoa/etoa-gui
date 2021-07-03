<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Core\AbstractRepository;

class FleetRepository extends AbstractRepository
{
    public function hasFleetsRelatedToEntity(int $entityId): bool
    {
        $count = (int) $this->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('fleet')
            ->where('entity_to = :entityId')
            ->orWhere('entity_from  = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute()
            ->fetchOne();

        return $count > 0;
    }
}
