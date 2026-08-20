<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;
use EtoA\Entity\UserMulti;

class UserMultiRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry
    )
    {
        parent::__construct($registry, UserMulti::class);
    }

    public function addOrUpdateEntry(UserMulti $userMulti): void
    {

        $entry = $this->findOneBy(['user'=>$userMulti->getUser(),'multiUser'=>$userMulti->getMultiUser()]);

        if ($entry) {
            $entry->setActive(true);
            $entry->setTimestamp(time());
        } else {
            $userMulti->setActive(true);
            $userMulti->setTimestamp(time());

            $this->persist($userMulti);
        }

        $this->save();
    }

    public function addEmptyEntry(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->insert('user_multi')
            ->values([
                'user_id' => ':userId',
                'timestamp' => ':now',
            ])
            ->setParameters([
                'userId' => $userId,
                'now' => time(),
            ])
            ->executeQuery();
    }

    public function updateEntry(int $id, int $userId, int $multiId, string $reason): void
    {
        $this->createQueryBuilder('q')
            ->update('user_multi')
            ->set('multi_id', ':multiId')
            ->set('connection', ':reason')
            ->set('timestamp', ':now')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'multiId' => $multiId,
                'reason' => $reason,
                'now' => time(),
            ])
            ->executeQuery();
    }

    public function deactivateEntry(int $id): void
    {
        $this->createQueryBuilder('q')
            ->update('user_multi')
            ->set('active', ':active')
            ->set('timestamp', ':now')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'active' => 0,
                'now' => time(),
            ])
            ->executeQuery();
    }

    public function deactivate(int $userId, int $multiId): void
    {
        $entry = $this->findOneBy(['user' => $userId, 'multiUser' => $multiId]);
        if ($entry === null) {
            return;
        }

        $entry->setActive(false);
        $entry->setTimestamp(time());

        $this->save();
    }

    /**
     * @return UserMulti[]
     */
    public function getUserEntries(User $user, bool $active = null): array
    {
        $constraints = ['user'=>$user];

        if ($active)
            $constraints['active'] = true;

        return $this->findBy($constraints,['id'=>'DESC']);

    }

    /**
     * @param int[] $userIds
     * @return array<int, UserMulti[]>
     */
    public function getUsersEntries(array $userIds): array
    {
        if (count($userIds) === 0) {
            return [];
        }

        return $this->createQueryBuilder('q')
            ->leftJoin('App:User', 'u', 'WITH', 'u.id = q.multiUser')
            ->where('q.user IN (:userIds)')
            ->setParameter('userIds', $userIds, ArrayParameterType::INTEGER)
            ->orderBy('q.id', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function getUserEntry(int $userId, int $id): ?UserMulti
    {
        $data = $this->createQueryBuilder('q')
            ->select('m.*, u.user_nick as multi_nick')
            ->from('user_multi', 'm')
            ->leftJoin('m', 'users', 'u', 'u.user_id = m.multi_id')
            ->where('m.user_id = :userId')
            ->andWhere('m.multi_id = :id')
            ->setParameter('userId', $userId, )
            ->setParameter('id', $id, )
            ->fetchAssociative();

        return $data !== false ? new UserMulti($data) : null;
    }

    public function existsEntryWith(User $userId, User $otherUserId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->where('q.user = :userId AND q.multiUser = :otherUserId')
            ->orWhere('q.user = :otherUserId AND q.multiUser = :userId')
            ->setParameters([
                'userId' => $userId,
                'otherUserId' => $otherUserId,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteUserEntries(User $user): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function deleteEntry(int $id): int
    {
        $qb = $this->createQueryBuilder('q');

        return $qb
            ->delete('user_multi')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->rowCount();
    }
}
