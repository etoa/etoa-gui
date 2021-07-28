<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Core\AbstractRepository;

class FleetBookmarkRepository extends AbstractRepository
{
    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('fleet_bookmarks')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}
