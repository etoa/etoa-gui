<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function getDiscoverMask(int $userId): string
    {
        return $this->getUserProperty($userId, 'discoverymask');
    }

    public function getPoints(int $userId): int
    {
        return (int) $this->getUserProperty($userId, 'user_points');
    }

    public function getAllianceId(int $userId): int
    {
        return (int) $this->getUserProperty($userId, 'user_alliance_id');
    }

    public function getSpecialistId(int $userId): int
    {
        return (int) $this->getUserProperty($userId, 'user_specialist_id');
    }

    public function getNick(int $userId): ?string
    {
        return $this->getUserProperty($userId, 'user_nick');
    }

    private function getUserProperty(int $userId, string $property): ?string
    {
        $data = $this->createQueryBuilder()
            ->select($property)
            ->from('users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('users')
            ->execute()
            ->fetchOne();
    }

    public function setLogoutTime(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_logouttime', ':time')
            ->where('user_id = :id')
            ->setParameters([
                'id' => $userId,
                'time' => time(),
            ])
            ->execute();
    }

    public function removePointsByTimestamp(int $timestamp): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('user_points')
            ->where("point_timestamp < :timestamp")
            ->setParameter('timestamp', $timestamp)
            ->execute();
    }

    public function getUserIdByNick(string $nick): ?int
    {
        $result = $this->createQueryBuilder()
            ->select('user_id')
            ->from('users')
            ->where('user_nick = :nick')
            ->setParameter('nick', $nick)
            ->execute()
            ->fetchOne();

        return $result !== false ? (int) $result : null;
    }

    public function markVerifiedByVerificationKey(string $verificationKey): bool
    {
        return (bool) $this->createQueryBuilder()
            ->update('users')
            ->set('verification_key', ':updatedKey')
            ->where('verification_key = :key')
            ->setParameter('key', $verificationKey)
            ->setParameter('updatedKey', '')
            ->setMaxResults(1)
            ->execute();
    }

    /**
     * @return array<int, string>
     */
    public function getUserNicknames(): array
    {
        return $this->createQueryBuilder()
            ->select('user_id, user_nick')
            ->from('users')
            ->orderBy('user_nick')
            ->execute()
            ->fetchAllKeyValue();
    }

    public function resetDiscoveryMask(): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('discoverymask', "''")
            ->set('user_setup', (string) 0)
            ->execute();
    }

    public function getUser(int $userId): ?User
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new User($data) : null;
    }

    /**
     * @return array<User>
     */
    public function findInactive(int $registerTime, int $onlineTime): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('user_ghost = 0')
            ->andWhere('admin = 0')
            ->andWhere('user_blocked_to < :time')
            ->andWhere('((user_registered < :registerTime AND user_points = 0)
                OR (user_logouttime < :onlineTime AND user_logouttime > 0 AND user_hmode_from = 0))')
            ->setParameters([
                'time' => time(),
                'registerTime' => $registerTime,
                'onlineTime' => $onlineTime,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn($row) => new User($row), $data);
    }

    /**
     * @return array<User>
     */
    public function findLongInactive(int $logoutTimeFrom, int $logoutTimeTo): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('user_ghost = 0')
            ->andWhere('admin = 0')
            ->andWhere('user_blocked_to < :time')
            ->andWhere('user_logouttime > :logoutTimeFrom')
            ->andWhere('user_logouttime < :logoutTimeTo')
            ->andWhere('user_hmode_from = 0')
            ->setParameters([
                'time' => time(),
                'logoutTimeFrom' => $logoutTimeFrom,
                'logoutTimeTo' => $logoutTimeTo,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn($row) => new User($row), $data);
    }

    /**
     * @return array<User>
     */
    public function findDeleted(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('user_deleted > 0')
            ->andWhere('user_deleted < :time')
            ->setParameters([
                'time' => time(),
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn($row) => new User($row), $data);
    }

    /**
     * @return array<int,string>
     */
    public function getEmailAddressesWithDisplayName(): array
    {
        $data = $this->createQueryBuilder()
            ->select('user_id', 'user_nick', 'user_email')
            ->from('users')
            ->orderBy('user_nick')
            ->execute()
            ->fetchAllAssociative();

        $recipients = [];
        foreach ($data as $item) {
            $recipients[(int) $item['user_id']] = $item['user_nick']."<".$item['user_email'].">";
        }

        return $recipients;
    }

    public function removeOldBans(): void
    {
        $this->getConnection()->executeQuery("
            UPDATE
                users
            SET
                user_blocked_from = 0,
                user_blocked_to = 0,
                user_ban_reason = '',
                user_ban_admin_id = 0
            WHERE
                user_blocked_to < :blockedBefore';
        ", [
            'blockedBefore' => time(),
        ]);
    }

    public function addSittingDays(int $days): void
    {
        $this->getConnection()->executeQuery("
            UPDATE
                users
            SET
                user_sitting_days = user_sitting_days + :days';
        ", [
            'days' => $days,
        ]);
    }

    public function remove(int $id): bool
    {
        $affected = (int) $this->createQueryBuilder()
            ->delete('users')
            ->where('user_id = :id')
            ->setParameter('id', $id)
            ->execute();

        return $affected > 0;
    }
}
