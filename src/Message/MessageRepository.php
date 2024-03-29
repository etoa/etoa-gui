<?php

declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\AbstractRepository;

class MessageRepository extends AbstractRepository
{
    /**
     * @return Message[]
     */
    public function search(MessageSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit, $offset)
            ->select('m.*', 'd.*')
            ->from('messages', 'm')
            ->innerJoin('m', 'message_data', 'd', 'd.id = m.message_id')
            ->orderBy('m.message_read', 'ASC')
            ->addOrderBy('m.message_timestamp', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => Message::createFromArray($row), $data);
    }

    public function count(MessageSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(*)')
            ->from('messages', 'm')
            ->innerJoin('m', 'message_data', 'd', 'd.id = m.message_id')
            ->fetchOne();
    }

    public function countNotArchived(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('messages')
            ->where('message_archived = 0')
            ->fetchOne();
    }

    public function countDeleted(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('messages')
            ->where('message_deleted = 1')
            ->fetchOne();
    }

    public function countNewForUser(int $userId): int
    {
        return (int) $this->createQueryBuilder()
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
        return (int) $this->createQueryBuilder()
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
        return (int) $this->createQueryBuilder()
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
        $data = $this->createQueryBuilder()
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
        $data = $this->createQueryBuilder()
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
     * @todo This should eventually replace send_msg() defined in functions.inc.php
     * @todo In the future, return a Message object (to be defined) instead of the message ID
     *
     * @param integer $userId the recipient user ID
     * @param integer $catId the message category ID
     * @param string $subject the subject
     * @param string $text the text
     * @return integer the ID of the newly created message
     */
    public function createSystemMessage(int $userId, int $catId, string $subject, string $text): int
    {
        try {
            $this->getConnection()->beginTransaction();
            $this->createQueryBuilder()
                ->insert('messages')
                ->values([
                    'message_user_from' => 0,
                    'message_user_to' => ':userId',
                    'message_cat_id' => ':catId',
                    'message_timestamp' => time(),
                ])
                ->setParameters([
                    'userId' => $userId,
                    'catId' => $catId,
                ])
                ->executeQuery();

            $id = (int) $this->getConnection()->lastInsertId();

            $this->createQueryBuilder()
                ->insert('message_data')
                ->values([
                    'id' => $id,
                    'subject' => ':subject',
                    'text' => ':text',
                ])
                ->setParameters([
                    'subject' => $subject,
                    'text' => $text,
                ])
                ->executeQuery();
            $this->getConnection()->commit();

            return $id;
        } catch (\Exception $ex) {
            $this->getConnection()->rollBack();

            throw $ex;
        }
    }

    public function sendFromUserToUser(
        int $senderId,
        int $receiverId,
        string $subject,
        string $text,
        int $catId = 0,
        int $fleetId = 0
    ): void {
        try {
            $this->getConnection()->beginTransaction();

            $this->createQueryBuilder()
                ->insert('messages')
                ->values([
                    'message_user_from' => ':senderId',
                    'message_user_to' => ':receiverId',
                    'message_cat_id' => ':catId',
                    'message_timestamp' => time(),
                ])
                ->setParameters([
                    'senderId' => $senderId,
                    'receiverId' => $receiverId,
                    'catId' => $catId != 0 ? $catId : MessageCategoryId::USER,
                ])
                ->executeQuery();

            $id = (int) $this->getConnection()->lastInsertId();

            $this->createQueryBuilder()
                ->insert('message_data')
                ->values([
                    'id' => $id,
                    'subject' => ':subject',
                    'text' => ':text',
                    'fleet_id' => ':fleet_id',
                ])
                ->setParameters([
                    'subject' => $subject,
                    'text' => $text,
                    'fleet_id' => $fleetId,
                ])
                ->executeQuery();

            $this->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->getConnection()->rollBack();

            throw $ex;
        }
    }

    public function find(int $id): ?Message
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('messages', 'm')
            ->innerJoin('m', 'message_data', 'd', 'd.id = m.message_id')
            ->where('message_id = :message_id')
            ->setParameter('message_id', $id)
            ->executeQuery()
            ->fetchAssociative();

        return $data !== false ? Message::createFromArray($data) : null;
    }

    /**
     * @param array<string, int|string|bool> $params
     * @return array<Message>
     */
    public function findBy(array $params = [], ?int $limit = null): array
    {
        $qry = $this->createQueryBuilder()
            ->select('m.*', 'd.*')
            ->from('messages', 'm')
            ->innerJoin('m', 'message_data', 'd', 'd.id = m.message_id')
            ->orderBy('message_read', 'ASC')
            ->addOrderBy('message_timestamp', 'DESC')
            ->setMaxResults($limit);

        if (isset($params['id'])) {
            $qry->andWhere('message_id = :id')
                ->setParameter('id', $params['id']);
        }

        if (isset($params['user_from_id'])) {
            $qry->andWhere('message_user_from = :user_from_id')
                ->setParameter('user_from_id', $params['user_from_id']);
        }

        if (isset($params['user_from_nick'])) {
            $qry->andWhere('s.user_nick = :user_from_nick')
                ->setParameter('user_from_nick', $params['user_from_nick'])
                ->innerJoin('m', 'users', 's', 's.user_id = m.message_user_from');
        }

        if (isset($params['user_to_id'])) {
            $qry->andWhere('message_user_to = :user_to_id')
                ->setParameter('user_to_id', $params['user_to_id']);
        }

        if (isset($params['user_to_nick'])) {
            $qry->andWhere('r.user_nick = :user_to_nick')
                ->setParameter('user_to_nick', $params['user_to_nick'])
                ->innerJoin('m', 'users', 'r', 'r.user_id = m.message_user_to');
        }

        if (isset($params['subject'])) {
            $qry->andWhere('d.subject LIKE :subject')
                ->setParameter('subject', '%' . $params['subject'] . '%');
        }

        if (isset($params['text'])) {
            $qry->andWhere('d.text LIKE :text')
                ->setParameter('text', '%' . $params['text'] . '%');
        }

        if (isset($params['fleet_id'])) {
            $qry->andWhere('fleet_id = :fleet_id')
                ->setParameter('fleet_id', $params['fleet_id']);
        }

        if (isset($params['entity_id'])) {
            $qry->andWhere('entity_id = :entity_id')
                ->setParameter('entity_id', $params['entity_id']);
        }

        if (isset($params['cat_id'])) {
            $qry->andWhere('message_cat_id = :cat_id')
                ->setParameter('cat_id', $params['cat_id']);
        }

        if (isset($params['read'])) {
            $qry->andWhere('message_read = :read')
                ->setParameter('read', $params['read']);
        }

        if (isset($params['massmail'])) {
            $qry->andWhere('message_massmail = :massmail')
                ->setParameter('massmail', $params['massmail']);
        }

        if (isset($params['deleted'])) {
            $qry->andWhere('message_deleted = :deleted')
                ->setParameter('deleted', $params['deleted']);
        }

        if (isset($params['archived'])) {
            $qry->andWhere('message_archived = :archived')
                ->setParameter('archived', $params['archived']);
        }

        if (isset($params['mailed'])) {
            $qry->andWhere('message_mailed = :mailed')
                ->setParameter('mailed', $params['mailed']);
        }

        $data = $qry
            ->fetchAllAssociative();

        return array_map(fn ($arr) => Message::createFromArray($arr), $data);
    }

    /**
     * @return array<Message>
     */
    public function findByRecipient(int $userId): array
    {
        $data = $this->createQueryBuilder()
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
        $qry = $this->createQueryBuilder()
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
        $qry = $this->createQueryBuilder()
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
        $qry = $this->createQueryBuilder()
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
        $affected = $this->createQueryBuilder()
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

    public function setMailed(int $userTo, bool $mailed = true): bool
    {
        $affected = $this->createQueryBuilder()
            ->update('messages')
            ->set('message_mailed', ':mailed')
            ->where('message_user_to = :userTo')
            ->setParameters([
                'userTo' => $userTo,
                'mailed' => $mailed,
            ])
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('messages')
            ->where('message_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $this->createQueryBuilder()
            ->delete('message_data')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    /**
     * @param array<int> $ids
     */
    public function removeBulk(array $ids): int
    {
        if (count($ids) == 0) {
            return 0;
        }

        $affected = $this->createQueryBuilder()
            ->delete('messages')
            ->where('message_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')')
            ->setParameters($ids)
            ->executeQuery()
            ->rowCount();

        $this->createQueryBuilder()
            ->delete('message_data')
            ->where('id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')')
            ->setParameters($ids)
            ->executeQuery();

        return $affected;
    }
}
