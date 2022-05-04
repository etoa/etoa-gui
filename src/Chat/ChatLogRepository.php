<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\AbstractRepository;

class ChatLogRepository extends AbstractRepository
{
    /**
     * @return ChatLog[]
     */
    public function getLogs(string $order = 'timestamp', string $sort = 'ASC'): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('chat_log')
            ->orderBy($order, $sort)
            ->setMaxResults(10000)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new ChatLog($row), $data);
    }

    public function count(ChatLogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(*)')
            ->from('chat_log')
            ->fetchOne();
    }

    /**
     * @return ChatLog[]
     */
    public function search(ChatLogSearch $search = null, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit, $offset)
            ->select('*')
            ->from('chat_log')
            ->orderBy('id', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new ChatLog($row), $data);
    }

    public function addLog(int $userId, string $nick, string $text, string $color, int $admin, string $channel = ''): void
    {
        $this->createQueryBuilder()
            ->insert('chat_log')
            ->values([
                'timestamp' => ':time',
                'user_id' => ':userId',
                'nick' => ':nick',
                'text' => ':text',
                'color' => ':color',
                'admin' => ':admin',
                'channel' => ':channel',
            ])
            ->setParameters([
                'time' => time(),
                'userId' => $userId,
                'nick' => $nick,
                'text' => $text,
                'color' => $color,
                'admin' => $admin,
                'channel' => $channel,
            ])
            ->executeQuery();
    }
}
