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

    public function getChatUser(int|User $userId): ?ChatUser
    {
        return $this->findOneBy(['user'=>$userId]);
    }

    public function updateChatUser(User $user): void
    {
        $chatUser = $this->findOneBy(['user' => $user]);

        if (!$chatUser) {
            $chatUser = new ChatUser();
            $chatUser->setUser($user);
            $chatUser->setNick($user->getNick());

            $this->persist($chatUser);
        }

        $chatUser->setTimestamp(time());
        $this->save();
    }

    public function kickUser(User|int $user, string $kickMessage): bool
    {
        $chatUser = $this->find($user);

        if($chatUser) {
            $chatUser->setKick($kickMessage);
            $this->save();

            return true;
        }

        return false;
    }

    public function deleteUser(User|int $user): void
    {
        $chatUser = $this->find($user);

        if($chatUser) {
            $this->remove($chatUser);
            $this->save();
        }
    }
}
