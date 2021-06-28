<?php

declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\AbstractRepository;

class MessageCategoryRepository extends AbstractRepository
{
    /**
     * @return array<int,string>
     */
    public function getNames(): array
    {
        return $this->createQueryBuilder()
            ->select('cat_id', 'cat_name')
            ->from('message_cat')
            ->orderBy('cat_order')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return array<MessageCategory>
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select('cat_id', 'cat_name', 'cat_desc', 'cat_sender')
            ->from('message_cat')
            ->orderBy('cat_order')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => MessageCategory::createFromArray($row), $data);
    }

    public function getName(int $catId): ?string
    {
        $data = $this->createQueryBuilder()
            ->select('cat_name')
            ->from('message_cat')
            ->where('cat_id = :cat_id')
            ->setParameter('cat_id', $catId)
            ->execute()
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function getSender(int $catId): ?string
    {
        $data = $this->createQueryBuilder()
            ->select('cat_sender')
            ->from('message_cat')
            ->where('cat_id = :cat_id')
            ->setParameter('cat_id', $catId)
            ->execute()
            ->fetchOne();

        return $data !== false ? $data : null;
    }
}
