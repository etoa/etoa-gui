<?php declare(strict_types=1);

namespace EtoA\Chat;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\ChatLog;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\ChatBan;
use EtoA\Entity\User;

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
    public function search(?ChatLogSearch $search = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.id', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function addLog(User $user, string $text, string $color, int $admin, string $channel = ''): void
    {
        $log = new ChatLog();
        $log->setTimestamp(time());
        $log->setUser($user);
        $log->setNick($user->getNick());
        $log->setText($text);
        $log->setColor($color);
        $log->setChannel($channel);
        $log->setAdmin($admin);

        $this->persist($log);
        $this->save();
    }

    public function countBySearch(?ChatLogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
