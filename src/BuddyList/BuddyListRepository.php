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
     * Buddy entries of the user. The inner join skips entries whose buddy account was deleted.
     *
     * @return Buddy[]
     */
    public function getBuddies(User $user): array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('q.buddy', 'b')
            ->addSelect('b')
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Unconfirmed requests sent to the user. The inner join skips requests from deleted accounts.
     *
     * @return Buddy[]
     */
    public function getPendingRequests(User $user): array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('q.user', 'u')
            ->addSelect('u')
            ->where('q.buddy = :user')
            ->andWhere('q.allowed = :allowed')
            ->setParameters([
                'user' => $user,
                'allowed' => false,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * True while someone added the user as a buddy and the user has not confirmed yet.
     */
    public function hasPendingFriendRequest(int $userId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->innerJoin('q.user', 'u')
            ->where('q.buddy = :userId')
            ->andWhere('q.allowed = :allowed')
            ->setParameters([
                'userId' => $userId,
                'allowed' => false,
            ])
            ->getQuery()
            ->getSingleScalarResult();
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
