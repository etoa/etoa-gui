<?php declare(strict_types=1);

namespace EtoA\BuddyList;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;

class BuddyListRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Buddy::class);
    }


    public function countFriendsOnline(int $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(user_id)')
            ->from('buddylist', 'b')
            ->innerJoin('b', 'user_sessions', 's', 'b.bl_buddy_id = s.user_id')
            ->where('b.bl_allow = 1')
            ->andWhere('bl_user_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchOne();
    }

    public function hasPendingFriendRequest(int $userId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('COUNT(bl_id)')
            ->from('buddylist')
            ->where('bl_allow = 0')
            ->andWhere('bl_buddy_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchOne();
    }

    /**
     * @return Buddy[]
     */
    public function getBuddies(int $userId): array
    {
        $data = $this->getConnection()->fetchAllAssociative('
            SELECT b.*, u.user_id, u.user_nick, u.user_points, p.id AS planetId, s.time_action as isOnline, x.time_action AS last_action
            FROM buddylist b
            INNER JOIN users u ON b.bl_buddy_id = u.user_id
            INNER JOIN planets p ON u.user_id = p.planet_user_id AND p.planet_user_main = 1
            LEFT JOIN user_sessions s ON u.user_id = s.user_id
            LEFT JOIN (
                SELECT user_id, MAX(time_action) as time_action FROM user_sessionlog GROUP BY user_id
            ) x ON x.user_id = u.user_id
            WHERE b.bl_user_id = :userId
            ORDER BY u.user_nick ASC
        ', [
            'userId' => $userId,
        ]);

        return array_map(fn (array $row) => new Buddy($row), $data);
    }

    public function getBuddy(int $userId, int $buddyId): ?Buddy
    {
        $data = $this->createQueryBuilder('q')
            ->select('b.*')
            ->addSelect('u.user_id, u.user_nick, u.user_points')
            ->addSelect('p.id as planetId')
            ->addSelect('s.time_action')
            ->from('buddylist', 'b')
            ->innerJoin('b', 'users', 'u', 'b.bl_buddy_id = u.user_id')
            ->innerJoin('u', 'planets', 'p', 'u.user_id = p.planet_user_id AND p.planet_user_main = 1')
            ->leftJoin('u', 'user_sessions', 's', ' u.user_id = s.user_id')
            ->where('b.bl_user_id = :userId')
            ->andWhere('b.bl_buddy_id = :buddyId')
            ->setParameters([
                'userId' => $userId,
                'buddyId' => $buddyId,
            ])
            ->fetchAssociative();

        return $data !== false ? new Buddy($data) : null;
    }

    /**
     * @return PendingBuddyRequest[]
     */
    public function getPendingBuddyRequests(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('b.*')
            ->addSelect('u.user_id, u.user_nick, u.user_points')
            ->from('buddylist', 'b')
            ->innerJoin('b', 'users', 'u', 'b.bl_user_id = u.user_id')
            ->where('b.bl_allow = 0')
            ->andWhere('b.bl_buddy_id = :userId')
            ->orderBy('u.user_nick', 'ASC')
            ->setParameter('userId', $userId)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new PendingBuddyRequest($row), $data);
    }

    public function addBuddyRequest(int $userId, int $buddyId): void
    {
        $this->createQueryBuilder('q')
            ->insert('buddylist')
            ->values([
                'bl_user_id' => ':userId',
                'bl_buddy_id' => ':buddyId',
                'bl_allow' => ':allow',
            ])
            ->setParameters([
                'userId' => $userId,
                'buddyId' => $buddyId,
                'allow' => 0,
            ])
            ->executeQuery();
    }

    public function acceptBuddyRequest(int $userId, int $buddyId): bool
    {
        $existed = (bool) $this->createQueryBuilder('q')
            ->update('buddylist')
            ->set('bl_allow', ':allow')
            ->where('bl_user_id = :buddyId')
            ->andWhere('bl_buddy_id = :userId')
            ->andWhere('bl_allow = 0')
            ->setParameters([
                'allow' => 1,
                'buddyId' => $buddyId,
                'userId' => $userId,
            ])
            ->executeQuery()
            ->rowCount();

        if (!$existed) {
            return false;
        }

        if ($this->buddyListEntryExist($userId, $buddyId)) {
            $this->createQueryBuilder('q')
                ->update('buddylist')
                ->set('bl_allow', ':allow')
                ->where('bl_user_id = :userId')
                ->andWhere('bl_buddy_id = :buddyId')
                ->setParameters([
                    'allow' => 1,
                    'userId' => $userId,
                    'buddyId' => $buddyId,
                ])
                ->executeQuery();

            return true;
        }

        $this->createQueryBuilder('q')
            ->insert('buddylist')
            ->values([
                'bl_allow' => ':allow',
                'bl_user_id' => ':userId',
                'bl_buddy_id' => ':buddyId',
            ])
            ->setParameters([
                'allow' => 1,
                'userId' => $userId,
                'buddyId' => $buddyId,
            ])
            ->executeQuery();

        return true;
    }

    public function rejectBuddyRequest(int $userId, int $buddyId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->delete('buddylist')
            ->where('bl_user_id = :buddyId')
            ->andWhere('bl_buddy_id = :userId')
            ->andWhere('bl_allow = 0')
            ->setParameters([
                'buddyId' => $buddyId,
                'userId' => $userId,
            ])
            ->executeQuery()
            ->rowCount();
    }

    public function updateComment(int $userId, int $buddyId, string $comment): void
    {
        $this->createQueryBuilder('q')
            ->update('buddylist')
            ->set('bl_comment', ':comment')
            ->where('bl_user_id = :userId')
            ->andWhere('bl_buddy_id = :buddyId')
            ->setParameters([
                'userId' => $userId,
                'buddyId' => $buddyId,
                'comment' => $comment,
            ])
            ->executeQuery();
    }

    public function buddyListEntryExist(int $userId, int $buddyId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('1')
            ->from('buddylist')
            ->where('bl_user_id = :userId')
            ->andWhere('bl_buddy_id = :buddyId')
            ->setParameters([
                'userId' => $userId,
                'buddyId' => $buddyId,
            ])
            ->fetchOne();
    }

    public function removeBuddy(int $userId, int $buddyId): bool
    {
        $counts = $this->createQueryBuilder('q')
            ->delete('buddylist')
            ->where('bl_user_id = :userId AND bl_buddy_id = :buddyId')
            ->orWhere('bl_user_id = :buddyId AND bl_buddy_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'buddyId' => $buddyId,
            ])
            ->executeQuery()
            ->rowCount();

        return (bool) $counts;
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->delete('buddylist')
            ->where('bl_user_id = :userId')
            ->orWhere('bl_buddy_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }
}
