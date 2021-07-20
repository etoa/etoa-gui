<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserWarningRepository extends AbstractRepository
{
    /**
     * @return array<array{userId: int, nick: string, count: int}>
     */
    public function getWarningCountsByUser(): array
    {
        $data = $this->createQueryBuilder()
            ->select('u.user_nick, u.user_id, COUNT(*) as cnt')
            ->from('user_warnings', 'w')
            ->innerJoin('w', 'users', 'u', 'u.user_id = w.warning_user_id')
            ->orderBy('u.user_nick')
            ->groupBy('w.warning_user_id')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => ['userId' => (int) $row['user_id'], 'nick' => $row['user_nick'], 'count' => (int) $row['cnt']], $data);
    }

    /**
     * @return UserWarning[]
     */
    public function getUserWarnings(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('w.*, a.user_nick as admin_user_nick')
            ->from('user_warnings', 'w')
            ->leftJoin('w', 'admin_users', 'a', 'a.user_id = w.warning_admin_id')
            ->where('w.warning_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('w.warning_date', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserWarning($row), $data);
    }

    public function getWarning(int $id): ?UserWarning
    {
        $data = $this->createQueryBuilder()
            ->select('w.*, a.user_nick as admin_user_nick')
            ->from('user_warnings', 'w')
            ->leftJoin('w', 'admin_users', 'a', 'a.user_id = w.warning_admin_id')
            ->where('w.warning_id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new UserWarning($data) : null;
    }

    /**
     * @return array{count: int, max: int}
     */
    public function getCountAndLatestWarning(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('COUNT(warning_id) count, MAX(warning_date) max')
            ->from('user_warnings')
            ->where('warning_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function deleteAllUserEntries(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('user_warnings')
            ->where('warning_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function addEntry(int $userId, string $text, int $adminId): void
    {
        $this->createQueryBuilder()
            ->insert('user_warnings')
            ->values([
                'warning_user_id' => ':userId',
                'warning_text' => ':text',
                'warning_date' => 'UNIX_TIMESTAMP()',
                'warning_admin_id' => ':adminId',

            ])
            ->setParameters([
                'userId' => $userId,
                'text' => $text,
                'adminId' => $adminId,
            ])
            ->execute();
    }

    public function updateEntry(int $id, string $text, int $adminId): void
    {
        $this->createQueryBuilder()
            ->update('user_warnings')
            ->set('warning_text', ':text')
            ->set('warning_admin_id', ':adminId')
            ->where('warning_id = :id')
            ->setParameters([
                'id' => $id,
                'text' => $text,
                'adminId' => $adminId,
            ])
            ->execute();
    }

    public function deleteEntry(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('user_warnings')
            ->where('warning_id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
