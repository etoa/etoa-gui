<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\AbstractRepository;

class ChatBanRepository extends AbstractRepository
{
    public function getUserBan(int $userId): ?ChatBan
    {
        $data = $this->createQueryBuilder()
            ->select('b.*', 'u.user_nick')
            ->from('chat_banns', 'b')
            ->innerJoin('b', 'users', 'u', 'u.user_id=b.user_id')
            ->where('b.user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new ChatBan($data) : null;
    }

    /**
     * @return ChatBan[]
     */
    public function getBans(): array
    {
        $data = $this->createQueryBuilder()
            ->select('b.*', 'u.user_nick')
            ->from('chat_banns', 'b')
            ->innerJoin('b', 'users', 'u', 'u.user_id=b.user_id')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new ChatBan($row), $data);
    }

    public function banUser(int $userId, string $reason, bool $forceReason = false): void
    {
        $this->getConnection()->executeQuery('
            INSERT INTO
				chat_banns
			(user_id, reason, timestamp)
			VALUES (:userId, :reason, :time)
			ON DUPLICATE KEY UPDATE timestamp = :time' . ($forceReason ? ',reason=:reason' : ''), [
            'userId' => $userId,
            'reason' => $reason,
            'time' => time(),
        ]);
    }

    public function deleteBan(int $userId): int
    {
        return $this->getConnection()->executeQuery('
            DELETE FROM chat_banns WHERE user_id = :userId
        ', [
            'userId' => $userId,
        ])->rowCount();
    }
}
