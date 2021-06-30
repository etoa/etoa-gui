<?php

declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\AbstractRepository;

class MessageIgnoreRepository extends AbstractRepository
{
    public function isRecipientIgnoringSender(int $senderId, int $recipientId): bool
    {
        $data = (int) $this->createQueryBuilder()
            ->select('COUNT(ignore_id)')
            ->from('message_ignore')
            ->where('ignore_owner_id = :recipient')
            ->andWhere('ignore_target_id = :sender')
            ->setParameters([
                'sender' => $senderId,
                'recipient' => $recipientId,
            ])
            ->execute()
            ->fetchOne();

        return $data > 0;
    }

    /**
     * @return array<int>
     */
    public function findForOwner(int $ownerId): array
    {
        $data = $this->createQueryBuilder()
            ->select('ignore_target_id')
            ->from('message_ignore')
            ->where('ignore_owner_id = :ownerId')
            ->setParameters([
                'ownerId' => $ownerId,
            ])
            ->execute()
            ->fetchFirstColumn();

        return array_map(fn ($id) => (int) $id, $data);
    }

    /**
     * @return array<int>
     */
    public function findForTarget(int $targetId): array
    {
        $data = $this->createQueryBuilder()
            ->select('ignore_owner_id')
            ->from('message_ignore')
            ->where('ignore_target_id = :targetId')
            ->setParameters([
                'targetId' => $targetId,
            ])
            ->execute()
            ->fetchFirstColumn();

        return array_map(fn ($id) => (int) $id, $data);
    }

    public function add(int $ownerId, int $targetId): void
    {
        $this->createQueryBuilder()
            ->insert('message_ignore')
            ->values([
                'ignore_owner_id' => ':ownerId',
                'ignore_target_id' => ':targetId',
            ])
            ->setParameters([
                'ownerId' => $ownerId,
                'targetId' => $targetId,
            ])
            ->execute();
    }

    public function remove(int $ownerId, int $targetId): void
    {
        $this->createQueryBuilder()
            ->delete('message_ignore')
            ->where('ignore_owner_id = :ownerId')
            ->andWhere('ignore_target_id = :targetId')
            ->setParameters([
                'ownerId' => $ownerId,
                'targetId' => $targetId,
            ])
            ->execute();
    }
}
