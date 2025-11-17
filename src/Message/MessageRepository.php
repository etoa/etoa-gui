<?php

declare(strict_types=1);

namespace EtoA\Message;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Fleet;
use EtoA\Entity\Message;
use EtoA\Entity\MessageCategory;
use EtoA\Entity\MessageData;
use EtoA\Entity\User;

class MessageRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly MessageDataRepository $messageDataRepository,
        private readonly MessageCategoryRepository $messageCategoryRepository
    )
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[]
     */
    public function search(MessageSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->innerJoin('App:MessageData', 'd', 'WITH', 'd.message = q.id')
            ->orderBy('q.read', 'ASC')
            ->addOrderBy('q.timestamp', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function countNotArchived(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(*)')
            ->from('messages')
            ->where('message_archived = 0')
            ->fetchOne();
    }

    public function countDeleted(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(*)')
            ->from('messages')
            ->where('message_deleted = 1')
            ->fetchOne();
    }

    public function countBySearch(MessageSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->innerJoin('App:MessageData', 'd', 'WITH', 'd.message = q.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countNewForUser(int $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(message_id)')
            ->from('messages')
            ->where('message_deleted = 0')
            ->andWhere('message_user_to = :userId')
            ->andWhere('message_read = 0')
            ->setParameters([
                'userId' => $userId,
            ])
            ->fetchOne();
    }

    public function countReadForUser(int $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(message_id)')
            ->from('messages')
            ->where('message_read = 1')
            ->andWhere('message_deleted = 0')
            ->andWhere('message_archived = 0')
            ->andWhere('message_user_to = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->fetchOne();
    }

    public function countArchivedForUser(int $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(message_id)')
            ->from('messages')
            ->where('message_archived = 1')
            ->andWhere('message_deleted = 0')
            ->andWhere('message_user_to = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->fetchOne();
    }

    /**
     * @return array<int>
     */
    public function findIdsOfDeletedOlderThan(int $timestamp): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('message_id')
            ->from('messages')
            ->where('message_deleted = 1')
            ->andWhere('message_timestamp < :timestamp')
            ->setParameters([
                'timestamp' => $timestamp,
            ])
            ->fetchFirstColumn();

        return array_map(fn ($id) => (int) $id, $data);
    }

    /**
     * @return array<int>
     */
    public function findIdsOfReadNotArchivedOlderThan(int $timestamp): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('message_id')
            ->from('messages')
            ->where('message_archived = 0')
            ->andWhere('message_read = 1')
            ->andWhere('message_timestamp < :timestamp')
            ->setParameters([
                'timestamp' => $timestamp,
            ])
            ->fetchFirstColumn();

        return array_map(fn ($id) => (int) $id, $data);
    }

    /**
     * Sends a message from the system to ghe given user
     *
     * @param User $user the recipient user ID
     * @param MessageCategory $cat the message category ID
     * @param string $subject the subject
     * @param string $text the text
     * @return Message the newly created message
     */
    public function createSystemMessage(User $user, MessageCategory $cat, string $subject, string $text): Message
    {
        $msg = new Message();
        $msg->setUserFrom(null);
        $msg->setUserTo($user);
        $msg->setCat($cat);
        $msg->setTimestamp(time());
        $this->persist($msg);
        $this->save();

        $msgData = new MessageData();
        $msgData->setMessage($msg);
        $msgData->setSubject($subject);
        $msgData->setText($text);
        $this->messageDataRepository->persist($msgData);

        $this->messageDataRepository->save();

        return $msg;
    }

    public function sendFromUserToUser(
        User $sender,
        User $receiver,
        MessageData $messageData,
        MessageCategory $cat = null,
        Fleet $fleet = null
    ): void {
        $message = new Message();
        $message->setUserFrom($sender);
        $message->setUserTo($receiver);
        $message->setCat($cat??$this->messageCategoryRepository->find(MessageCategoryId::USER));
        $message->setTimestamp(time());

        $messageData->setFleet($fleet);
        $message->setMessageData($messageData);

        $this->persist($message);
        $this->save();
    }

    /**
     * @return array<Message>
     */
    public function findByRecipient(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('messages', 'm')
            ->innerJoin('m', 'message_data', 'd', 'd.id = m.message_id')
            ->where('message_user_to = :userId')
            ->orderBy('message_timestamp', 'ASC')
            ->setParameter('userId', $userId)
            ->fetchAllAssociative();

        return array_map(fn ($arr) => Message::createFromArray($arr), $data);
    }

    public function setArchived(int $id, bool $archived = true, ?int $userToId = null): bool
    {
        $qry = $this->createQueryBuilder('q')
            ->update('messages')
            ->set('message_archived', ':archived')
            ->where('message_id = :id')
            ->setParameters([
                'id' => $id,
                'archived' => $archived,
            ]);

        if ($userToId !== null) {
            $qry->andWhere('message_user_to = :userToId')
                ->setParameter('userToId', $userToId);
        }

        $affected = $qry->executeQuery()->rowCount();

        return $affected > 0;
    }

    public function setDeleted(int $id, bool $deleted = true, ?int $userToId = null, ?bool $isArchived = null): bool
    {
        $qry = $this->createQueryBuilder('q')
            ->update('messages')
            ->set('message_deleted', ':deleted')
            ->where('message_id = :id')
            ->setParameters([
                'id' => $id,
                'deleted' => (int) $deleted,
            ]);

        if ($userToId !== null) {
            $qry->andWhere('message_user_to = :userToId')
                ->setParameter('userToId', $userToId);
        }

        if ($isArchived !== null) {
            $qry->andWhere('message_archived = :isArchived')
                ->setParameter('isArchived', $isArchived);
        }

        $affected = $qry->executeQuery()->rowCount();

        return $affected > 0;
    }

    public function setDeletedForUser(int $userId, bool $deleted = true, int $userFromId = null, ?bool $isArchived = null): bool
    {
        $qry = $this->createQueryBuilder('q')
            ->update('messages')
            ->set('message_deleted', ':deleted')
            ->where('message_user_to = :userId')
            ->setParameters([
                'userId' => $userId,
                'deleted' => $deleted,
            ]);

        if ($userFromId !== null) {
            $qry->andWhere('message_user_from = :userFromId')
                ->setParameter('userFromId', $userFromId);
        }

        if ($isArchived !== null) {
            $qry->andWhere('message_archived = :isArchived')
                ->setParameter('isArchived', $isArchived);
        }

        $affected = $qry->executeQuery()->rowCount();

        return $affected > 0;
    }

    public function setRead(int $id, bool $read = true): bool
    {
        $affected = $this->createQueryBuilder('q')
            ->update('messages')
            ->set('message_read', ':read')
            ->where('message_id = :id')
            ->setParameters([
                'id' => $id,
                'read' => $read,
            ])
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function setMailed(int|User $userTo, bool $mailed = true): void
    {
        $affected = $this->createQueryBuilder('q')
            ->update()
            ->set('q.mailed', ':mailed')
            ->where('q.userTo = :userTo')
            ->setParameters([
                'userTo' => $userTo,
                'mailed' => $mailed,
            ])
            ->getQuery()
            ->execute();
    }

    /**
     * @param array<int> $ids
     */
    public function removeBulk(array $ids): int
    {
        if (count($ids) == 0) {
            return 0;
        }

        $affected = $this->createQueryBuilder('q')
            ->delete('messages')
            ->where('message_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')')
            ->setParameters($ids)
            ->executeQuery()
            ->rowCount();

        $this->createQueryBuilder('q')
            ->delete('message_data')
            ->where('id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')')
            ->setParameters($ids)
            ->executeQuery();

        return $affected;
    }
}
