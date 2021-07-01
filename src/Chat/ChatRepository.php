<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\AbstractRepository;

class ChatRepository extends AbstractRepository
{
    /**
     * @return ChatMessage[]
     */
    public function getMessagesAfter(int $minId, int $channelId = 0): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('chat')
            ->where('id > :minId')
            ->andWhere('channel_id = :channelId')
            ->setParameters([
                'minId' => $minId,
                'channelId' => $channelId,
            ])
            ->orderBy('timestamp', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new ChatMessage($row), $data);
    }

    public function addSystemMessage(string $message): void
    {
        $this->createQueryBuilder()
            ->insert('chat')
            ->values([
                'timestamp' => ':time',
                'text' => ':text',
            ])
            ->setParameters([
                'time' => time(),
                'text' => $message,
            ])->execute();
    }

    public function addMessage(int $userId, string $nick, string $message, string $color, int $admin): void
    {
        $this->createQueryBuilder()
            ->insert('chat')
            ->values([
                'timestamp' => ':time',
                'text' => ':text',
                'user_id' => ':userId',
                'nick' => ':nick',
                'color' => ':color',
                'admin' => ':admin',
            ])
            ->setParameters([
                'time' => time(),
                'text' => $message,
                'userId' => $userId,
                'nick' => $nick,
                'color' => $color,
                'admin' => $admin,
            ])->execute();
    }

    public function cleanupMessage(int $keep): int
    {
        $keepId = (int) $this->createQueryBuilder()
            ->select('id')
            ->from('chat')
            ->orderBy('id', 'DESC')
            ->setMaxResults(1)
            ->setFirstResult($keep)
            ->execute()
            ->fetchOne();

        return $this->getConnection()->executeQuery('
            DELETE FROM chat
            WHERE id <= :keepId
        ', [
            'keepId' => $keepId,
        ])->rowCount();
    }
}
