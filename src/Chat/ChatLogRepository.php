<?php declare(strict_types=1);

namespace EtoA\Chat;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\ChatLog;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\ChatBan;

class ChatLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatLog::class);
    }

    /**
     * @return ChatLog[]
     */
    public function getLogs(string $order = 'timestamp', string $sort = 'ASC'): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('chat_log')
            ->orderBy($order, $sort)
            ->setMaxResults(10000)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new ChatLog($row), $data);
    }

    /**
     * @return ChatLog[]
     */
    public function search(ChatLogSearch $search = null, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.id', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function addLog(int $userId, string $nick, string $text, string $color, int $admin, string $channel = ''): void
    {
        $this->createQueryBuilder('q')
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

    public function countBySearch(ChatLogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
