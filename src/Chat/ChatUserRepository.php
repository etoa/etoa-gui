<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\AbstractRepository;

class ChatUserRepository extends AbstractRepository
{
    /**
     * @return ChatUser[]
     */
    public function getChatUsers(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('chat_users')
            ->orderBy('nick')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new ChatUser($row), $data);
    }

    /**
     * @return ChatUser[]
     */
    public function getTimedOutChatUsers(int $timeout): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('chat_users')
            ->where('timestamp < UNIX_TIMESTAMP() - :timeout')
            ->setParameter('timeout', $timeout)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new ChatUser($row), $data);
    }

    public function getChatUser(int $userId): ?ChatUser
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('chat_users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new ChatUser($data) : null;
    }

    public function updateChatUser(int $userId, string $nick): void
    {
        $this->getConnection()->executeQuery('
            INSERT INTO
				chat_users
			(user_id, nick, timestamp)
			VALUES (:userId, :nick, :time)
			ON DUPLICATE KEY UPDATE timestamp = :time, nick = :nick
        ', [
                'userId' => $userId,
                'nick' => $nick,
                'time' => time(),
            ]);
    }

    public function kickUser(int $userId, string $kickMessage): int
    {
        return $this->getConnection()->executeQuery('
            UPDATE
				chat_users
			SET
				kick=:kick
			WHERE
				user_id=:userId
        ', [
            'kick' => $kickMessage,
            'userId' => $userId,
        ])->rowCount();
    }

    public function deleteUser(int $userId): int
    {
        return $this->getConnection()->executeQuery('
            DELETE FROM
				chat_users
			WHERE
				user_id=:userId
        ', [
            'userId' => $userId,
        ])->rowCount();
    }
}
