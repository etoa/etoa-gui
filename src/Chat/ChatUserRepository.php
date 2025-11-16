<?php declare(strict_types=1);

namespace EtoA\Chat;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\ChatUser;
use EtoA\Entity\User;

class ChatUserRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatUser::class);
    }

    /**
     * @return ChatUser[]
     */
    public function getChatUsers(): array
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.user', 'u')
            ->addSelect('u')
            ->orderBy('u.nick', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ChatUser[]
     */
    public function getTimedOutChatUsers(int $timeout): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.timestamp < :timeout')
            ->setParameter('timeout', time() - $timeout)
            ->getQuery()
            ->execute();
    }

    public function getChatUser(int $userId): ?ChatUser
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('chat_users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
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

    public function kickUser(User $user, string $kickMessage): void
    {
        $chatUser = $this->find($user);

        if($chatUser)
            $chatUser->setKick($kickMessage);

        $this->save();
    }

    public function deleteUser(User $user): void
    {
        $chatUser = $this->find($user);

        if($chatUser) {
            $this->remove($chatUser);
            $this->save();
        }
    }
}
