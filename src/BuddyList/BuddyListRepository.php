<?php declare(strict_types=1);

namespace EtoA\BuddyList;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Buddy;
use EtoA\Entity\User;
use EtoA\Entity\UserSession;

class BuddyListRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Buddy::class);
    }


    /**
     * Confirmed buddies of the user that currently have a session.
     */
    public function countFriendsOnline(int $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            // a user can have several sessions, so count each buddy once
            ->select('COUNT(DISTINCT q.id)')
            ->innerJoin(UserSession::class, 's', Join::WITH, 's.user = q.buddy')
            ->where('q.user = :userId')
            ->andWhere('q.allowed = :allowed')
            ->setParameters([
                'userId' => $userId,
                'allowed' => true,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * True while someone added the user as a buddy and the user has not confirmed yet.
     */
    public function hasPendingFriendRequest(int $userId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->where('q.buddy = :userId')
            ->andWhere('q.allowed = :allowed')
            ->setParameters([
                'userId' => $userId,
                'allowed' => false,
            ])
            ->getQuery()
            ->getSingleScalarResult();
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

    public function addBuddyRequest(User $user, User $buddy): void
    {

        $request = new Buddy();
        $request->setAllowed(false);
        $request->setBuddy($buddy);
        $request->setUser($user);

        $this->persist($request);
        $this->save();
    }

    public function acceptBuddyRequest(Buddy $buddy): void
    {
        $buddy->setAllowed(true);

        $otherBuddy = new Buddy();
        $otherBuddy->setAllowed(true);
        $otherBuddy->setBuddy($buddy->getUser());
        $otherBuddy->setUser($buddy->getBuddy());
        $this->persist($otherBuddy);
        $this->save();
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

    public function buddyListEntryExist(User $user, User $buddy): bool
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

    public function removeBuddy(Buddy $buddy): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user AND q.buddy = :buddy')
            ->orWhere('q.user = :buddy AND q.buddy = :user')
            ->setParameters([
                'user' => $buddy->getUser(),
                'buddy' => $buddy->getBuddy(),
            ])
            ->getQuery()
            ->execute();
    }

    public function removeForUser(User $user): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->orWhere('q.buddy= :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
