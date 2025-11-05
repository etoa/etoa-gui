<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AdminUser;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceRank;
use EtoA\Entity\Race;
use EtoA\Entity\User;

class UserRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getDiscoverMask(int $userId): string
    {
        return $this->getUserProperty($userId, 'discoverymask');
    }

    public function getPoints(int $userId): int
    {
        return (int)$this->getUserProperty($userId, 'user_points');
    }

    public function getAllianceId(int $userId): int
    {
        return (int)$this->getUserProperty($userId, 'user_alliance_id');
    }

    public function getSpecialistId(int $userId): int
    {
        return (int)$this->getUserProperty($userId, 'user_specialist_id');
    }

    public function getNick(int $userId): ?string
    {
        return $this->getUserProperty($userId, 'user_nick');
    }

    private function getUserProperty(int $userId, string $property): ?string
    {
        $data = $this->createQueryBuilder('q')
            ->select($property)
            ->from('users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function setAlliance(User $user, Alliance $alliance, AllianceRank $rank = null, int $leaveTimestamp = null): void
    {
        $user->setAlliance($alliance);

        if ($rank) {
            $user->setAllianceRank($rank);
        }

        if ($leaveTimestamp) {
            $user->setAllianceLeave($leaveTimestamp);
        }

        $this->save();
    }

    public function resetAlliance(Alliance $alliance): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.alliance', ':zero')
            ->set('q.allianceRank', ':zero')
            ->where('q.alliance = :alliance')
            ->setParameters([
                'zero' => null,
                'alliance' => $alliance,
            ])
            ->getQuery()
            ->execute();
    }

    public function hasUserRankId(int $allianceId, int $userId, int $rankId): bool
    {
        return (bool)$this->createQueryBuilder('q')
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
            ->fetchOne();
    }

    public function setLogoutTime(User $user, ?int $time = null): void
    {
        $user->setLogoutTime($time??time());

        $this->save();
    }

    public function addSpecialistTime(int $userId, int $time): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_specialist_time', 'user_specialist_time + :time')
            ->where('user_id = :id')
            ->andWhere('user_specialist_id > 0')
            ->setParameters([
                'id' => $userId,
                'time' => $time,
            ])
            ->executeQuery();
    }

    public function setSpecialist(int $userId, int $specialistId, int $time): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_specialist_time', ':time')
            ->set('user_specialist_id', ':specialistId')
            ->where('user_id = :id')
            ->setParameters([
                'id' => $userId,
                'specialistId' => $specialistId,
                'time' => $time,
            ])
            ->executeQuery();
    }

    /**
     * associative array: for each specialist (id), get number of users which have this specialist active (count)
     * @return array<int, int>
     */
    public function countUsersWithSpecialists(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('user_specialist_id, COUNT(user_id)')
            ->from('users')
            ->where('user_specialist_time > :now')
            ->groupBy('user_specialist_id')
            ->setParameters([
                'now' => time(),
            ])
            ->fetchAllKeyValue();

        return array_map(fn($value) => (int)$value, $data);
    }

    public function activateHolidayMode(int $userId, int $from, int $to): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_hmode_from', (string)$from)
            ->set('user_hmode_to', (string)$to)
            ->set('user_logouttime', (string)$from)
            ->where('user_id = :id')
            ->setParameters([
                'id' => $userId,
            ])
            ->executeQuery();
    }

    public function disableHolidayMode(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_hmode_from', (string)0)
            ->set('user_hmode_to', (string)0)
            ->set('user_logouttime', (string)time())
            ->where('user_id = :id')
            ->setParameters([
                'id' => $userId,
            ])
            ->executeQuery();
    }

    public function removePointsByTimestamp(int $timestamp): int
    {
        return $this->createQueryBuilder('q')
            ->delete('user_points')
            ->where("point_timestamp < :timestamp")
            ->setParameter('timestamp', $timestamp)
            ->executeQuery()
            ->rowCount();
    }

    public function getUserIdByNick(string $nick): ?int
    {
        $result = $this->createQueryBuilder('q')
            ->select('user_id')
            ->from('users')
            ->where('user_nick = :nick')
            ->setParameter('nick', $nick)
            ->fetchOne();

        return $result !== false ? (int)$result : null;
    }

    public function markVerifiedByVerificationKey(string $verificationKey): bool
    {
        return (bool)$this->createQueryBuilder('q')
            ->update('users')
            ->set('verification_key', ':updatedKey')
            ->where('verification_key = :key')
            ->setParameter('key', $verificationKey)
            ->setParameter('updatedKey', '')
            ->setMaxResults(1)
            ->executeQuery()
            ->rowCount();
    }

    public function saveDiscoveryMask(int $userId, string $mask): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('discoverymask', ":mask")
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'mask' => $mask,
            ])
            ->executeQuery();
    }

    public function resetDiscoveryMask(): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.discoveryMask', ":mask")
            ->set('q.setup', ":setup")
            ->setParameters([
                'mask' => '',
                'setup' => false,
            ])
            ->getQuery()
            ->execute();
    }

    public function setSetupFinished(User $user): void
    {
        $user->setSetup(true);
        $this->save();
    }

    /**
     * @return User[]
     */
    public function getAllianceUsers(int $allianceId): array
    {
        return $this->searchUsers(UserSearch::create()->allianceId($allianceId));
    }

    public function findUser(UserSearch $search): ?User
    {
        $query = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->setMaxResults(1)
            ->getQuery();


        return $query->getOneOrNullResult();
    }

    public function getUser(int $userId): ?User
    {
        return $this->find($userId);
    }

    /**
     * @return UserAdminView[]
     */
    public function searchAdminView(UserSearch $search): array
    {
        $where = implode(' AND ', $search->parts);
        return $this->getConnection()->fetchAllAssociative('SELECT
                users.*,
                user_sessionlog.time_action AS time_log,
                user_sessionlog.ip_addr AS ip_log,
                user_sessionlog.user_agent AS agent_log,
                user_sessions.time_action,
                user_sessions.user_agent,
                user_sessions.ip_addr
            FROM users
            LEFT JOIN user_sessionlog ON users.user_id = user_sessionlog.user_id AND user_sessionlog.time_action = (SELECT MAX(time_action) FROM user_sessionlog WHERE user_sessionlog.user_id = users.user_id)
            LEFT JOIN user_sessions ON users.user_id = user_sessions.user_id
            WHERE ' . $where . '
            ORDER BY users.user_nick', $search->parameters);
    }

    public function getUserAdminView(int $userId): ?UserAdminView
    {
        $data = $this->getConnection()->fetchAssociative('SELECT
                users.*,
                user_sessionlog.time_action AS time_log,
                user_sessionlog.ip_addr AS ip_log,
                user_sessionlog.user_agent AS agent_log,
                user_sessions.time_action,
                user_sessions.user_agent,
                user_sessions.ip_addr
            FROM users
            LEFT JOIN user_sessionlog ON users.user_id = user_sessionlog.user_id
            LEFT JOIN user_sessions ON users.user_id = user_sessions.user_id
            WHERE users.user_id = :userId
            ORDER BY user_sessionlog.time_action DESC
            LIMIT 1', ['userId' => $userId]);

        return $data !== false ? new UserAdminView($data) : null;
    }

    public function getUserByNick(string $nick): ?User
    {
        return $this->findUser(UserSearch::create()->nick($nick));
    }

    public function getUserByNickAndEmail(string $nick, string $emailFixed): ?User
    {
        return $this->findUser(UserSearch::create()->nick($nick)->emailFix($emailFixed));
    }

    /**
     * @return array<User>
     */
    public function findInactive(int $registerTime, int $onlineTime): array
    {
        $data = $this->createQueryBuilder('q')
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
            ->fetchAllAssociative();

        return array_map(fn($row) => new User($row), $data);
    }

    /**
     * @return array<User>
     */
    public function findLongInactive(int $logoutTimeFrom, int $logoutTimeTo): array
    {
        $data = $this->createQueryBuilder('q')
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
            ->fetchAllAssociative();

        return array_map(fn($row) => new User($row), $data);
    }

    /**
     * @return array<User>
     */
    public function findInactiveInHolidayMode(int $threshold): array
    {
        $data = $this->createQueryBuilder('q')
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
            ->fetchAllAssociative();

        return array_map(fn($row) => new User($row), $data);
    }

    /**
     * @return array<User>
     */
    public function findDeleted(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.deleted > 0')
            ->andWhere('q.deleted < :time')
            ->setParameters([
                'time' => time(),
            ])
            ->getQuery()
            ->execute();
    }

    public function markDeleted(User $user, int $timestamp): void
    {
        $user->setDeleted($timestamp);
        $this->save();
    }

    /**
     * @return array<string,string>
     */
    public function getEmailAddressesWithNickname(): array
    {
        return $this->createQueryBuilder('q')
            ->select('user_email', 'user_nick')
            ->from('users')
            ->orderBy('user_nick')
            ->fetchAllKeyValue();
    }

    public function blockUser(User $user, int $from, int $to, string $reason, AdminUser $admin): void
    {
        $user->setBlockedFrom($from);
        $user->setBlockedTo($to);
        $user->setBanReason($reason);
        $user->setBanAdmin($admin);

        $this->save();
    }

    public function removeOldBans(): int
    {
        return $this->createQueryBuilder('q')
            ->update('users')
            ->set('`user_blocked_from`', '0')
            ->set('`user_blocked_to`', '0')
            ->set('`user_ban_reason`', ':banReason')
            ->set('`user_ban_admin_id`', '0')
            ->where('`user_blocked_to` < :blockedBefore')
            ->setParameters([
                'blockedBefore' => time(),
                'banReason' => '',
            ])
            ->executeQuery()
            ->rowCount();
    }

    public function updateImgCheck(int $userId, bool $check, string $image = null): bool
    {
        $qb = $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_profile_img_check', ':check')
            ->where('user_id = :userId')
            ->setParameters([
                'check' => (int)$check,
                'userId' => $userId,
            ]);

        if ($image !== null) {
            $qb
                ->set('user_profile_img', ':image')
                ->setParameter('image', $image);
        }

        return (bool)$qb->executeQuery()->rowCount();
    }

    public function addSittingDays(int $days): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('`user_sitting_days`', '`user_sitting_days` + :days')
            ->setParameter('days', $days)
            ->executeQuery();
    }

    public function setSittingDays(User $user, int $days): void
    {
        $user->setSittingDays($days);
        $this->save();
    }

    public function setVerified(User $user, bool $verified): void
    {
        $user->setVerificationKey($verified ? '' : generateRandomString(64));
    }

    /**
     * @return array<int, int>
     */
    public function getUsedAllianceShipPoints(): array
    {
        return $this->createQueryBuilder('q')
            ->select('user_alliance_id, SUM(user_alliace_shippoints_used)')
            ->from('users')
            ->groupBy('user_alliance_id')
            ->fetchAllKeyValue();
    }

    public function markAllianceShipPointsAsUsed(User $user, int $shipCost): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.allianceShipPoints', 'q.allianceShipPoints - :costs')
            ->set('q.allianceShipPointsUsed', 'q.allianceShipPointsUsed + :costs')
            ->where('q.id = :user')
            ->setParameters([
                'user' => $user->getId(),
                'costs' => $shipCost,
            ])
            ->getQuery()
            ->execute();
    }

    public function addAllianceShipPoints(int $allianceId, int $points): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_alliace_shippoints', 'user_alliace_shippoints + :points')
            ->where('user_alliance_id = :allianceId')
            ->setParameters([
                'allianceId' => $allianceId,
                'points' => $points,
            ])
            ->executeQuery();
    }

    public function exists(UserSearch $search): bool
    {
        return (bool)$this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->setMaxResults(1)
            ->getQuery()
            ->execute();
    }

    public function create(string $nick, string $name, string $email, string $password, ?Race $race = null, bool $ghost = false): User
    {
        $user = new User();

        $user->setNick($nick);
        $user->setName($name);
        $user->setEmail($email);
        $user->setEmailFix($email);
        $user->setPassword($password);
        $user->setRace($race);
        $user->setGhost($ghost);
        $user->setLogoutTime(time());
        $user->setRegistered(time());

        $this->persist($user);
        $this->save();

        return $user;
    }

    public function updatePassword(int $userId, string $password, bool $isHashedPassword = false): string
    {
        $saltedPassword = $isHashedPassword ? $password : saltPasswort($password);
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_password', ':password')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'password' => $saltedPassword,
            ])
            ->executeQuery();

        return $saltedPassword;
    }

    public function increaseMultiDeletes(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_multi_delets', 'user_multi_delets + 1')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->executeQuery();
    }

    public function updateObserve(int $userId, ?string $observe): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_observe', ':observe')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'observe' => $observe,
            ])
            ->executeQuery();
    }

    public function markMainPlanetChanged(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_changed_main_planet', '1')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->executeQuery();
    }

    /**
     * @return array<int, User>
     */
    public function searchUserNicknames(UserSearch $search = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->orderBy('q.nick');

        return $this->applySearchSortLimit($qb, $search, null, $limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @return User[]
     */
    public function searchUsers(UserSearch $search = null, UserSort $sort = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('q');

        if ($sort == null || count($sort->sorts) === 0) {
            $qb->orderBy('q.nick');
        }

        if (isset($search->parameters['allianceLike'])) {
            $qb->innerJoin('App:Alliance', 'alliances', 'WITH', 'q.alliance = alliance.id');
        }

        return $this->applySearchSortLimit($qb, $search, $sort, $limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @return Pillory[]
     */
    public function getPillory(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.blockedFrom < :time')
            ->andWhere('q.blockedTo > :time')
            ->orderBy('q.blockedFrom', 'DESC')
            ->setParameter('time', time())
            ->getQuery()
            ->execute();
    }

    public function updatePointsAndRank(UserStatistic $userStatistic, int $highestRank): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_rank', ':rank')
            ->set('user_points', ':points')
            ->set('user_rank_highest', ':highestRank')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userStatistic->userId,
                'rank' => $userStatistic->rank,
                'points' => $userStatistic->points,
                'highestRank' => $highestRank,
            ])
            ->executeQuery();
    }

    public function updateUserBoost(int $userId, float $productionBoost, float $buildingBoost): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('boost_bonus_production', ':production')
            ->set('boost_bonus_building', ':building')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'production' => $productionBoost,
                'building' => $buildingBoost,
            ])
            ->executeQuery();
    }

    public function resetBoost(): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('boost_bonus_production', ':zero')
            ->set('boost_bonus_building', ':zero')
            ->setParameters([
                'zero' => 0,
            ])
            ->executeQuery();
    }

    /**
     * @return array{user_blocked_from: string, user_blocked_to: string, user_hmode_from: string, user_deleted: string, admin: string, user_ghost: string, user_alliance_id: string, user_id: string, user_points: string, user_nick: string, time_log: string, time_action: string|null, user_name: string, user_email: string, user_email_fix: string, user_multi_delets: string}[]
     */
    public function getUsersWithIp(string $ip): array
    {
        return $this->getConnection()->fetchAllAssociative('
            SELECT
                users.user_blocked_from,
                users.user_blocked_to,
                users.user_hmode_from,
                users.user_deleted,
                users.admin,
                users.user_ghost,
                users.user_alliance_id,
                users.user_id,
                users.user_points,
                users.user_nick,
                user_sessionlog.time_action AS time_log,
                user_sessions.time_action,
                users.user_name,
                users.user_email,
                users.user_email_fix,
                users.user_multi_delets
            FROM
                users
                LEFT JOIN
                    user_sessions
                ON
                users.user_id=user_sessions.user_id
            INNER JOIN
                user_sessionlog
            ON
                users.user_id=user_sessionlog.user_id
                INNER JOIN (
                    SELECT
                        user_id,
                        MAX( time_action ) AS last_action
                    FROM
                        user_sessionlog
                    GROUP BY
                        user_id
                ) AS log
                ON
                    user_sessionlog.user_id = log.user_id
                    AND user_sessionlog.time_action = log.last_action
                    AND (user_sessions.ip_addr = :ip OR user_sessionlog.ip_addr = :ip)
            ORDER BY
                time_log DESC
        ', [
            'ip' => $ip,
        ]);
    }

    public function addVisit(User $user): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.visits', 'q.visits + 1')
            ->where('q.id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, array{name: string, cnt: string}>
     */
    public function getNumberOfRacesByType(): array
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(q.id) as cnt')
            ->addSelect('t.name as name')
            ->innerJoin('App:Race', 't', 'WITH', 'q.race = t.id')
            ->where('q.ghost = 0')
            ->andWhere('q.hmodFrom = 0')
            ->andWhere('q.hmodTo = 0')
            ->groupBy('t.id')
            ->orderBy('cnt')
            ->getQuery()
            ->execute();
    }
}
