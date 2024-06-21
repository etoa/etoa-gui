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
        $data = $this->createQueryBuilder()
            ->select($property)
            ->from('users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function count(): int
    {
        return (int)$this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('users')
            ->fetchOne();
    }

    public function setAllianceId(int $userId, int $allianceId, int $rankId = null, int $leaveTimestamp = null): void
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

        if ($leaveTimestamp !== null) {
            $qb
                ->set('user_alliance_leave', ':leave')
                ->setParameter('leave', $leaveTimestamp);
        }

        $qb
            ->executeQuery();
    }

    public function resetAllianceId(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_alliance_id', ':zero')
            ->set('user_alliance_rank_id', ':zero')
            ->where('user_alliance_id = :allianceId')
            ->setParameters([
                'zero' => 0,
                'allianceId' => $allianceId,
            ])
            ->executeQuery();
    }

    public function hasUserRankId(int $allianceId, int $userId, int $rankId): bool
    {
        return (bool)$this->createQueryBuilder()
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
            ->executeQuery();
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
            ->executeQuery();
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
            ->executeQuery();
    }

    /**
     * associative array: for each specialist (id), get number of users which have this specialist active (count)
     * @return array<int, int>
     */
    public function countUsersWithSpecialists(): array
    {
        $data = $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        return $this->createQueryBuilder()
            ->delete('user_points')
            ->where("point_timestamp < :timestamp")
            ->setParameter('timestamp', $timestamp)
            ->executeQuery()
            ->rowCount();
    }

    public function getUserIdByNick(string $nick): ?int
    {
        $result = $this->createQueryBuilder()
            ->select('user_id')
            ->from('users')
            ->where('user_nick = :nick')
            ->setParameter('nick', $nick)
            ->fetchOne();

        return $result !== false ? (int)$result : null;
    }

    public function markVerifiedByVerificationKey(string $verificationKey): bool
    {
        return (bool)$this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
            ->update('users')
            ->set('discoverymask', "''")
            ->set('user_setup', (string)0)
            ->executeQuery();
    }

    public function setSetupFinished(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_setup', (string)1)
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->executeQuery();
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
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('*')
            ->from('users')
            ->setMaxResults(1)
            ->fetchAssociative();

        return $data !== false ? new User($data) : null;
    }

    public function getUser(int $userId): ?User
    {
        return $this->findUser(UserSearch::create()->user($userId));
    }

    /**
     * @return UserAdminView[]
     */
    public function searchAdminView(UserSearch $search): array
    {
        $where = implode(' AND ', $search->parts);
        $data = $this->getConnection()->fetchAllAssociative('SELECT
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

        return array_map(fn($row) => new UserAdminView($row), $data);
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
            ->fetchAllAssociative();

        return array_map(fn($row) => new User($row), $data);
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
            ->fetchAllAssociative();

        return array_map(fn($row) => new User($row), $data);
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
            ->executeQuery();
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
            ->fetchAllKeyValue();
    }

    public function blockUser(int $userId, int $from, int $to, string $reason, int $adminId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_blocked_from', ':from')
            ->set('user_blocked_to', ':to')
            ->set('user_ban_reason', ':reason')
            ->set('user_ban_admin_id', ':adminId')
            ->where('user_id = :userId')
            ->setParameters([
                'from' => $from,
                'to' => $to,
                'reason' => $reason,
                'adminId' => $adminId,
                'userId' => $userId,
            ])
            ->executeQuery();
    }

    public function removeOldBans(): int
    {
        return $this->createQueryBuilder()
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
        $qb = $this->createQueryBuilder()
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
        $this->createQueryBuilder()
            ->update('users')
            ->set('`user_sitting_days`', '`user_sitting_days` + :days')
            ->setParameter('days', $days)
            ->executeQuery();
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
            ->executeQuery();
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
            ->executeQuery();
    }

    /**
     * @return array<int, int>
     */
    public function getUsedAllianceShipPoints(): array
    {
        return $this->createQueryBuilder()
            ->select('user_alliance_id, SUM(user_alliace_shippoints_used)')
            ->from('users')
            ->groupBy('user_alliance_id')
            ->fetchAllKeyValue();
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
            ->executeQuery();
    }

    public function addAllianceShipPoints(int $allianceId, int $points): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_alliace_shippoints', 'user_alliace_shippoints + :points')
            ->where('user_alliance_id = :allianceId')
            ->setParameters([
                'allianceId' => $allianceId,
                'points' => $points,
            ])
            ->executeQuery();
    }

    public function remove(int $id): bool
    {
        $affected = $this->createQueryBuilder()
            ->delete('users')
            ->where('user_id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function exists(UserSearch $search): bool
    {
        return (bool)$this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select("1")
            ->from('users')
            ->setMaxResults(1)
            ->fetchOne();
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
            ->executeQuery();

        return (int)$this->getConnection()->lastInsertId();
    }

    public function updatePassword(int $userId, string $password, bool $isHashedPassword = false): string
    {
        $saltedPassword = $isHashedPassword ? $password : saltPasswort($password);
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_changed_main_planet', '1')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->executeQuery();
    }

    public function save(User $user): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_name', ':name')
            ->set('npc', ':npc')
            ->set('user_nick', ':nick')
            ->set('user_email', ':email')
            ->set('user_password_temp', ':passwordTemp')
            ->set('user_email_fix', ':emailFix')
            ->set('dual_name', ':dualName')
            ->set('dual_email', ':dualEmail')
            ->set('user_race_id', ':raceId')
            ->set('user_alliance_id', ':allianceId')
            ->set('user_profile_text', ':profileText')
            ->set('user_signature', ':signature')
            ->set('user_multi_delets', ':multiDelets')
            ->set('user_sitting_days', ':sittingDays')
            ->set('user_chatadmin', ':chatAdmin')
            ->set('admin', ':admin')
            ->set('user_ghost', ':ghost')
            ->set('user_changed_main_planet', ':userChangedMainPlanet')
            ->set('user_profile_board_url', ':profileBoardUrl')
            ->set('user_alliace_shippoints', ':allianceShipPoints')
            ->set('user_alliace_shippoints_used', ':allianceShipPointsUsed')
            ->set('user_alliance_rank_id', ':allianceRankId')
            ->set('user_profile_img_check', ':profileImageCheck')
            ->set('user_specialist_time', ':specialistTime')
            ->set('user_specialist_id', ':specialistId')
            ->set('user_profile_img', ':profileImage')
            ->set('user_avatar', ':avatar')
            ->set('user_password', ':password')
            ->set('user_blocked_from', ':blockedFrom')
            ->set('user_blocked_to', ':blockedTo')
            ->set('user_ban_admin_id', ':banAdminId')
            ->set('user_ban_reason', ':banReason')
            ->set('user_hmode_from', ':hmodFrom')
            ->set('user_hmode_to', ':hmodTo')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $user->getId(),
                'name' => $user->getName(),
                'npc' => $user->getNpc(),
                'nick' => $user->getNick(),
                'email' => $user->getEmail(),
                'passwordTemp' => $user->getPasswordTemp(),
                'emailFix' => $user->getEmailFix(),
                'dualName' => $user->getDualName(),
                'dualEmail' => $user->getDualEmail(),
                'raceId' => $user->getRaceId(),
                'allianceId' => $user->getAllianceId(),
                'profileText' => $user->getProfileText(),
                'signature' => $user->getSignature(),
                'multiDelets' => $user->getDeleted(),
                'sittingDays' => $user->getSittingDays(),
                'chatAdmin' => $user->getChatAdmin(),
                'admin' => $user->getAdmin(),
                'ghost' => (int)$user->isGhost(),
                'userChangedMainPlanet' => (int)$user->getUserMainPlanetChanged(),
                'profileBoardUrl' => $user->getProfileBoardUrl(),
                'allianceShipPoints' => $user->getAllianceShipPoints(),
                'allianceShipPointsUsed' => $user->getAllianceShipPointsUsed(),
                'allianceRankId' => $user->getAllianceRankId(),
                'profileImageCheck' => (int)$user->isProfileImageCheck(),
                'specialistTime' => $user->getSpecialistTime(),
                'specialistId' => $user->getSpecialistId(),
                'profileImage' => $user->getProfileImage(),
                'avatar' => $user->getAvatar(),
                'password' => $user->getPassword(),
                'blockedFrom' => $user->getBlockedFrom(),
                'blockedTo' => $user->getBlockedTo(),
                'banAdminId' => $user->getBanAdminId(),
                'banReason' => $user->getBanReason(),
                'hmodFrom' => $user->getHmodFrom(),
                'hmodTo' => $user->getHmodTo(),
            ])
            ->executeQuery();
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
            ->fetchAllKeyValue();
    }

    /**
     * @return User[]
     */
    public function searchUsers(UserSearch $search = null, UserSort $sort = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('users');

        if ($sort == null || count($sort->sorts) === 0) {
            $qb->orderBy('user_nick');
        }

        if (isset($search->parameters['allianceLike'])) {
            $qb->innerJoin('users', 'alliances', 'alliances', 'user_alliance_id = alliances.alliance_id');
        }

        $data = $this->applySearchSortLimit($qb, $search, $sort, $limit)
            ->fetchAllAssociative();

        $users = [];
        foreach ($data as $row) {
            $user = new User($row);
            $users[$user->getId()] = $user;
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
            ->fetchAllAssociative();

        return array_map(fn(array $row) => new Pillory($row), $data);
    }

    public function updatePointsAndRank(UserStatistic $userStatistic, int $highestRank): void
    {
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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

    public function addVisit(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_visits', 'user_visits + 1')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }
}
