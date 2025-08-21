<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\QueryBuilder;
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
        return $this->createSitterQueryBuilder()
            ->where('q.dateFrom < :time')
            ->andWhere('q.dateTo > :time')
            ->setParameter('time', time())
            ->getQuery()
            ->execute();
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
            ->where('q.user IN (:userIds)')
            ->andWhere('q.dateFrom < :time')
            ->andWhere('q.dateTo > :time')
            ->setParameter('time', time())
            ->setParameter('userIds', $userIds, ArrayParameterType::INTEGER)
            ->getQuery()
            ->execute();

        $entries = [];
        foreach ($data as $row) {
            $entries[(int)$row['user_id']] = new UserSitting($data);
        }

        return $entries;
    }

    /**
     * @return UserSitting[]
     */
    public function getWhereUser(int|User $user): array
    {
        return $this->findBy(['user'=>$user]);
    }

    /**
     * @return UserSitting[]
     */
    public function getWhereSitter(User $sitter): array
    {
        return $this->findBy(['sitter'=>$sitter]);
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
            ->leftJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->leftJoin('App:user', 'us', 'WITH', 'us.id = q.sitter')
            ->orderBy('q.dateFrom', 'DESC');
    }

    public function cancelEntry(UserSitting $userSitting): void
    {
        $userSitting->setDateTo(time());
        $this->save();
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
