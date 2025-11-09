<?php declare(strict_types=1);

namespace EtoA\Chat;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Chat;

class ChatRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    /**
     * @return ChatMessage[]
     */
    public function getMessagesAfter(int $minId, int $channelId = 0): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.id > :minId')
            ->andWhere('q.channelId = :channelId')
            ->setParameters([
                'minId' => $minId,
                'channelId' => $channelId,
            ])
            ->orderBy('q.timestamp', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function addSystemMessage(string $message): void
    {
        $chat = new Chat();
        $chat->setTimestamp(time());
        $chat->setText($message);

        $this->persist($chat);
        $this->save();
    }

    public function addMessage(int $userId, string $nick, string $message, string $color, int $admin): void
    {
        $this->createQueryBuilder('q')
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
            ])->executeQuery();
    }

    public function cleanupMessage(int $keep): int
    {
        $keepId = (int) $this->createQueryBuilder('q')
            ->select('id')
            ->from('chat')
            ->orderBy('id', 'DESC')
            ->setMaxResults(1)
            ->setFirstResult($keep)
            ->fetchOne();

        return $this->getConnection()->executeQuery('
            DELETE FROM chat
            WHERE id <= :keepId
        ', [
            'keepId' => $keepId,
        ])->rowCount();
    }
}
