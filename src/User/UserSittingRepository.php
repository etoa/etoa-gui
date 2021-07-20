<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\AbstractRepository;

class UserSittingRepository extends AbstractRepository
{
    public function addEntry(int $userId, int $sitterId, string $password, int $dateFrom, int $dateTo): void
    {
        $this->createQueryBuilder()
            ->insert('user_sitting')
            ->values([
                'user_id' => ':userId',
                'sitter_id' => ':sitterId',
                'password' => ':password',
                'date_from' => ':dateFrom',
                'date_to' => ':dateTo',
            ])
            ->setParameters([
                'userId' => $userId,
                'sitterId' => $sitterId,
                'password' => $password,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ])->execute();
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
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSitting($row), $data);
    }

    public function getActiveUserEntry(int $userId): ?UserSitting
    {
        $data = $this->createSitterQueryBuilder()
            ->where('s.user_id = :userId')
            ->andWhere('s.date_from < :time')
            ->andWhere('s.date_to > :time')
            ->setParameter('time', time())
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new UserSitting($data) : null;
    }

    /**
     * @return UserSitting[]
     */
    public function getWhereUser(int $userId): array
    {
        $data = $this->createSitterQueryBuilder()
            ->where('s.user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSitting($row), $data);
    }

    /**
     * @return UserSitting[]
     */
    public function getWhereSitter(int $userId): array
    {
        $data = $this->createSitterQueryBuilder()
            ->where('s.sitter_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSitting($row), $data);
    }

    public function existsEntry(int $userId, string $password): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('user_sitting')
            ->where('user_id = :userId')
            ->andWhere('password = :password')
            ->setParameters([
                'userId' => $userId,
                'password' => $password,
            ])
            ->execute()
            ->fetchOne();
    }

    public function hasSittingEntryForTimeSpan(int $userId, int $from, int $to): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('user_sitting')
            ->where('user_id = :userId')
            ->andWhere('(date_from < :from AND :from < date_to) OR (date_from < :to AND :to < date_to)')
            ->setParameters([
                'userId' => $userId,
                'from' => $from,
                'to' => $to,
            ])
            ->execute()
            ->fetchOne();
    }

    public function getUsedSittingTime(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('SUM(CEIL((date_to - date_from) / 86400))')
            ->from('user_sitting')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->execute()
            ->fetchOne();
    }

    private function createSitterQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select('s.*', 'u.user_nick as user_nick', 'us.user_nick as sitter_nick')
            ->from('user_sitting', 's')
            ->leftJoin('s', 'users', 'u', 'u.user_id = s.user_id')
            ->leftJoin('s', 'users', 'us', 'us.user_id = s.user_id')
            ->orderBy('s.date_from', 'DESC');
    }

    public function cancelEntry(int $id): void
    {
        $this->createQueryBuilder()
            ->update('user_sitting')
            ->set('date_to', 'UNIX_TIMESTAMP()')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function cancelUserEntry(int $id, int $userId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->update('user_sitting')
            ->set('date_to', 'UNIX_TIMESTAMP()')
            ->where('id = :id')
            ->andWhere('userId = :userId')
            ->andWhere('date_from < :time')
            ->andWhere('date_to > :time')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'time' => time(),
            ])
            ->execute();
    }

    public function deleteFutureUserEntry(int $id, int $userId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->delete('user_sitting')
            ->where('id = :id')
            ->andWhere('userId = :userId')
            ->andWhere('date_from > :time')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'time' => time(),
            ])
            ->execute();
    }

    public function deleteAllUserEntries(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('user_sitting')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}
