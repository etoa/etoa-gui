<?php

declare(strict_types=1);

namespace EtoA\Message;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\MessageCategory;
use EtoA\Entity\User;

class MessageCategoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageCategory::class);
    }

    /**
     * @return array<int,string>
     */
    public function getNames(): array
    {
        return $this->createQueryBuilder('q')
            ->select('cat_id', 'cat_name')
            ->from('message_cat')
            ->orderBy('cat_order')
            ->fetchAllKeyValue();
    }

    /**
     * @return array<MessageCategory>
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('cat_id', 'cat_name', 'cat_desc', 'cat_sender')
            ->from('message_cat')
            ->orderBy('cat_order')
            ->fetchAllAssociative();

        return array_map(fn ($row) => MessageCategory::createFromArray($row), $data);
    }

    public function getName(int $catId): ?string
    {
        $data = $this->createQueryBuilder('q')
            ->select('cat_name')
            ->from('message_cat')
            ->where('cat_id = :cat_id')
            ->setParameter('cat_id', $catId)
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function getSender(int $catId): ?string
    {
        $data = $this->createQueryBuilder('q')
            ->select('cat_sender')
            ->from('message_cat')
            ->where('cat_id = :cat_id')
            ->setParameter('cat_id', $catId)
            ->executeQuery()
            ->fetchOne();

        return $data !== false ? $data : null;
    }
}
