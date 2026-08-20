<?php declare(strict_types=1);

namespace EtoA\Chat;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\ChatBan;
use EtoA\Entity\User;

class ChatBanRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatBan::class);
    }

    public function getUserBan(int $userId): ?ChatBan
    {
        return $this->find($userId);
    }

    /**
     * @return ChatBan[]
     */
    public function getBans(): array
    {
        // the join keeps the previous behaviour of skipping bans whose user is gone
        return $this->createQueryBuilder('q')
            ->innerJoin('q.user', 'u')
            ->getQuery()
            ->getResult();
    }

    public function banUser(User|int $user, string $reason, bool $forceReason = false): void
    {
        $ban = $this->find($user);

        if(!$ban) {
            $ban = new ChatBan();
            $ban->setUser($user);
            $ban->setReason($reason);
        } else {
            if($forceReason)
                $ban->setReason($reason);
        }

        $ban->setTimestamp(time());

        $this->persist($ban);
        $this->save();
    }

    public function deleteBan(User|int $user): bool
    {
        $ban = $this->find($user);

        if($ban) {
            $this->remove($ban);
            $this->save();
            return true;
        }

        return false;
    }
}
