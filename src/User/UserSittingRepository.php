<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;
use EtoA\Entity\UserSitting;
use function Symfony\Component\Clock\now;

class UserSittingRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSitting::class);
    }

    public function addEntry(UserSitting $userSitting): void
    {
        $this->getEntityManager()->persist($userSitting);
        $this->getEntityManager()->flush();
    }

    /**
     * @return UserSitting[]
     */
    public function getActiveSittingEntries(): array
    {
        $data = $this->createSitterQueryBuilder()
            ->where('s.date_from < :time')
            ->andWhere('s.date_to > :time')
            ->setParameter('time', time())
            ->fetchAllAssociative();

        return array_map(fn(array $row) => new UserSitting($row), $data);
    }

    public function getActiveUserEntry(User $user): ?UserSitting
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.dateFrom < :time')
            ->andWhere('q.dateTo > :time')
            ->setParameter('time', time())
            ->setParameter('user', $user)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * @param int[] $userIds
     * @return array<int, UserSitting>
     */
    public function getActiveUsersEntry(array $userIds): array
    {
        $data = $this->createSitterQueryBuilder()
            ->where('s.user_id IN (:userIds)')
            ->andWhere('s.date_from < :time')
            ->andWhere('s.date_to > :time')
            ->setParameter('time', time())
            ->setParameter('userIds', $userIds, ArrayParameterType::INTEGER)
            ->fetchAllAssociative();

        $entries = [];
        foreach ($data as $row) {
            $entries[(int)$row['user_id']] = new UserSitting($data);
        }

        return $entries;
    }

    /**
     * @return UserSitting[]
     */
    public function getWhereUser(int $userId): array
    {
        return $this->findBy(['userId'=>$userId]);
    }

    /**
     * @return UserSitting[]
     */
    public function getWhereSitter(int $userId): array
    {
        $data = $this->createSitterQueryBuilder()
            ->where('s.sitter_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchAllAssociative();

        return array_map(fn(array $row) => new UserSitting($row), $data);
    }

    public function existsEntry(int $userId, string $password): bool
    {
        return (bool)$this->createQueryBuilder('q')
            ->select('1')
            ->from('user_sitting')
            ->where('user_id = :userId')
            ->andWhere('password = :password')
            ->setParameters([
                'userId' => $userId,
                'password' => $password,
            ])
            ->fetchOne();
    }

    public function hasSittingEntryForTimeSpan(int $userId, int $from, int $to): bool
    {
        return (bool)$this->createQueryBuilder('q')
            ->select('1')
            ->where('q.userId = :userId')
            ->andWhere('(q.dateFrom < :from AND :from < q.dateTo) OR (q.dateFrom < :to AND :to < q.dateTo)')
            ->setParameters([
                'userId' => $userId,
                'from' => $from,
                'to' => $to,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getUsedSittingTime(int $userId): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select('SUM(CEIL((q.dateTo - q.dateFrom) / 86400))')
            ->where('q.userId = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function createSitterQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->select('s.*', 'u.user_nick as user_nick', 'us.user_nick as sitter_nick')
            ->from('user_sitting', 's')
            ->leftJoin('s', 'users', 'u', 'u.user_id = s.user_id')
            ->leftJoin('s', 'users', 'us', 'us.user_id = s.sitter_id')
            ->orderBy('s.date_from', 'DESC');
    }

    public function cancelEntry(int $id): void
    {
        $this->createQueryBuilder('q')
            ->update('user_sitting')
            ->set('date_to', 'UNIX_TIMESTAMP()')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    public function cancelUserEntry(int $id, int $userId): bool
    {

        $sitting = $this->createQueryBuilder('q')
            ->set('date_to', 'UNIX_TIMESTAMP()')
            ->where('q.id = :id')
            ->andWhere('q.userId = :userId')
            ->andWhere('q.dateFrom < :time')
            ->andWhere('q.dateTo > :time')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'time' => time(),
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if($sitting) {
            $sitting->setDateTo(now()->getTimestamp());
            $this->save();
            return true;
        }
        return false;
    }

    public function deleteFutureUserEntry(int $id, int $userId): bool
    {
        $sitting = $this->createQueryBuilder('q')
        ->where('q.id = :id')
        ->andWhere('q.userId = :userId')
        ->andWhere('q.dateFrom > :time')
        ->setParameters([
            'id' => $id,
            'userId' => $userId,
            'time' => time(),
        ])
        ->getQuery()
        ->getOneOrNullResult();

        if($sitting) {
            $this->getEntityManager()->remove($sitting);
            $this->save();
            return true;
        }

        return false;
    }

    public function deleteAllUserEntries(User $user): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
