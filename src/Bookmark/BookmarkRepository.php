<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Core\AbstractRepository;

class BookmarkRepository extends AbstractRepository
{
    /**
     * @return array<Bookmark>
     */
    public function findForUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('bookmarks')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($arr) => new Bookmark($arr), $data);
    }
}
