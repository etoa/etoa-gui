<?php

declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\AbstractRepository;

class MessageRepository extends AbstractRepository
{
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
                ->execute();

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
                ->execute();
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
    ) {
        try {
            if ($catId == 0) {
                $cat = USER_MSG_CAT_ID;
            }
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
                    'catId' => $catId,
                ])
                ->execute();

            $id = (int) $this->getConnection()->lastInsertId();

            $this->createQueryBuilder()
                ->insert('message_data')
                ->values([
                    'id' => $id,
                    'subject' => ':subject',
                    'text' => ':text',
                    'fleet_id' => ':fleet_id'
                ])
                ->setParameters([
                    'subject' => $subject,
                    'text' => $text,
                    'fleet_id' => $fleetId,
                ])
                ->execute();

            $this->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->getConnection()->rollBack();

            throw $ex;
        }
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
            ->execute()
            ->fetchAllAssociative();

        return collect($data)
            ->map(fn ($arr) => Message::createFromArray($arr))
            ->toArray();
    }
}
