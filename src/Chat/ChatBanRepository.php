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
        $data = $this->createQueryBuilder('q')
            ->select('b.*', 'u.user_nick')
            ->from('chat_banns', 'b')
            ->innerJoin('b', 'users', 'u', 'u.user_id=b.user_id')
            ->where('b.user_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchAssociative();

        return $data !== false ? new ChatBan($data) : null;
    }

    /**
     * @return ChatBan[]
     */
    public function getBans(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('b.*', 'u.user_nick')
            ->from('chat_banns', 'b')
            ->innerJoin('b', 'users', 'u', 'u.user_id=b.user_id')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new ChatBan($row), $data);
    }

    public function banUser(User $user, string $reason, bool $forceReason = false): void
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

    public function deleteBan(User $user): void
    {
        $ban = $this->find($user);

        $this->remove($ban);
        $this->save();
    }
}
