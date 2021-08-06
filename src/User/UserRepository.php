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

    public function setAllianceId(int $userId, int $allianceId, int $rankId = null): void
    {
        $qb = $this->createQueryBuilder()
            ->update('users')
            ->set('user_alliance_id', ':allianceId')
            ->where('user_id = :id')
            ->setParameters([
                'id' => $userId,
                'allianceId' => $allianceId,
            ]);

        if ($rankId !== null) {
            $qb
                ->set('user_alliance_rank_id', ':rank')
                ->setParameter('rank', $rankId);
        }

        $qb
            ->execute();
    }


    public function hasUserRankId(int $allianceId, int $userId, int $rankId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('user_id')
            ->from('users')
            ->where('user_id = :userId')
            ->andWhere('user_alliance_id = :allianceId')
            ->andWhere('user_alliance_rank_id = :rankId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
                'rankId' => $rankId,
            ])
            ->setMaxResults(1)
            ->execute()
            ->fetchOne();
    }

    public function setLogoutTime(int $userId, ?int $time = null): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_logouttime', ':time')
            ->where('user_id = :id')
            ->setParameters([
                'id' => $userId,
                'time' => $time ?? time(),
            ])
            ->execute();
    }

    public function addSpecialistTime(int $userId, int $time): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_specialist_time', 'user_specialist_time + :time')
            ->where('user_id = :id')
            ->andWhere('user_specialist_id > 0')
            ->setParameters([
                'id' => $userId,
                'time' => $time,
            ])
            ->execute();
    }

    public function setSpecialist(int $userId, int $specialistId, int $time): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_specialist_time', ':time')
            ->set('user_specialist_id', ':specialistId')
            ->where('user_id = :id')
            ->setParameters([
                'id' => $userId,
                'specialistId' => $specialistId,
                'time' => $time,
            ])
            ->execute();
    }

    /**
     * @return array<int, int>
     */
    public function countUsersWithSpecialists(): array
    {
        $data = $this->createQueryBuilder()
            ->select('user_specialist_id, COUNT(user_id)')
            ->from('users')
            ->where('user_specialist_time > :now')
            ->setParameters([
                'now' => time(),
            ])
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function disableHolidayMode(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_hmode_from', (string) 0)
            ->set('user_hmode_to', (string) 0)
            ->where('user_id = :id')
            ->setParameters([
                'id' => $userId,
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

    public function saveDiscoveryMask(int $userId, string $mask): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('discoverymask', ":mask")
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'mask' => $mask,
            ])
            ->execute();
    }
    public function resetDiscoveryMask(): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('discoverymask', "''")
            ->set('user_setup', (string) 0)
            ->execute();
    }

    public function setSetupFinished(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_setup', (string) 1)
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->execute();
    }

    /**
     * @return User[]
     */
    public function getAllianceUsers(int $allianceId): array
    {
        return $this->searchUsers(UserSearch::create()->allianceId($allianceId));
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

    public function getUserByNick(string $nick): ?User
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('LCASE(user_nick) = :nick')
            ->setParameters([
                'nick' => strtolower($nick),
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new User($data) : null;
    }

    public function getUserByNickAndEmail(string $nick, string $emailFixed): ?User
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('LCASE(user_nick) = :nick')
            ->andWhere('user_email_fix = :emailFixed')
            ->setParameters([
                'nick' => strtolower($nick),
                'emailFixed' => $emailFixed,
            ])
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

        return array_map(fn ($row) => new User($row), $data);
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

        return array_map(fn ($row) => new User($row), $data);
    }

    /**
     * @return array<User>
     */
    public function findInactiveInHolidayMode(int $threshold): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('user_ghost = 0')
            ->andWhere('admin = 0')
            ->andWhere('user_blocked_to < :time')
            ->andWhere('user_hmode_from > 0')
            ->andWhere('user_hmode_from < :threshold')
            ->setParameters([
                'time' => time(),
                'threshold' => $threshold,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new User($row), $data);
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

        return array_map(fn ($row) => new User($row), $data);
    }

    public function markDeleted(int $userId, int $timestamp): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_deleted', ':timestamp')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'timestamp' => $timestamp,
            ])
            ->execute();
    }

    /**
     * @return array<string,string>
     */
    public function getEmailAddressesWithNickname(): array
    {
        return $this->createQueryBuilder()
            ->select('user_email', 'user_nick')
            ->from('users')
            ->orderBy('user_nick')
            ->execute()
            ->fetchAllKeyValue();
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

    public function setSittingDays(int $userId, int $days): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_sitting_days', ':days')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'days' => $days,
            ])
            ->execute();
    }

    public function setVerified(int $userId, bool $verified): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('verification_key', ':verificationKey')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'verificationKey' => $verified ? '' : generateRandomString(64),
            ])
            ->execute();
    }

    public function markAllianceShipPointsAsUsed(int $userId, int $shipCost): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_alliace_shippoints', 'user_alliace_shippoints - :costs')
            ->set('user_alliace_shippoints_used', 'user_alliace_shippoints_used + :costs')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'costs' => $shipCost,
            ])
            ->execute();
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

    public function exists(string $nick, string $email): bool
    {
        $data = $this->createQueryBuilder()
            ->select("user_id")
            ->from('users')
            ->where('user_nick = :nick')
            ->orWhere('user_email_fix = :email')
            ->setMaxResults(1)
            ->setParameters([
                'nick' => $nick,
                'email' => $email,
            ])
            ->execute()
            ->fetchOne();

        return $data !== false;
    }

    public function create(string $nick, string $name, string $email, string $password, int $race = 0, bool $ghost = false): int
    {
        $this->createQueryBuilder()
            ->insert('users')
            ->values([
                'user_nick' => ':nick',
                'user_name' => ':name',
                'user_email' => ':email',
                'user_email_fix' => ':email',
                'user_password' => ':password',
                'user_race_id' => ':race',
                'user_ghost' => ':ghost',
                'user_logouttime' => 'UNIX_TIMESTAMP()',
                'user_registered' => 'UNIX_TIMESTAMP()',
            ])
            ->setParameters([
                'nick' => $nick,
                'name' => $name,
                'email' => $email,
                'race' => $race,
                'password' => saltPasswort($password),
                'ghost' => $ghost ? 1 : 0,
            ])
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function updatePassword(int $userId, string $password): string
    {
        $saltedPassword = saltPasswort($password);
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_password', ':password')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'password' => $saltedPassword,
            ])
            ->execute();

        return $saltedPassword;
    }

    public function increaseMultiDeletes(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_multi_delets', 'user_multi_delets + 1')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->execute();
    }

    public function updateObserve(int $userId, ?string $observe): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_observe', ':observe')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'observe' => $observe,
            ])
            ->execute();
    }

    /**
     * @return array<int, string>
     */
    public function searchUserNicknames(UserSearch $search = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('user_id, user_nick')
            ->from('users')
            ->orderBy('user_nick');

        return $this->applySearchSortLimit($qb, $search, null, $limit)
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return User[]
     */
    public function searchUsers(UserSearch $search = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->orderBy('user_nick');

        $data = $this->applySearchSortLimit($qb, $search, null, $limit)
            ->execute()
            ->fetchAllAssociative();

        $users = [];
        foreach ($data as $row) {
            $user = new User($row);
            $users[$user->id] = $user;
        }

        return $users;
    }

    /**
     * @return Pillory[]
     */
    public function getPillory(): array
    {
        $data = $this->createQueryBuilder()
            ->select('u.user_nick, u.user_blocked_from, u.user_blocked_to, u.user_ban_reason')
            ->addSelect('a.user_nick AS admin_nick, a.user_email AS admin_email')
            ->from('users', 'u')
            ->leftJoin('u', 'admin_users', 'a', 'u.user_ban_admin_id = a.user_id')
            ->where('u.user_blocked_from < :time')
            ->andWhere('u.user_blocked_to > :time')
            ->orderBy('u.user_blocked_from', 'DESC')
            ->setParameter('time', time())
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Pillory($row), $data);
    }
}
