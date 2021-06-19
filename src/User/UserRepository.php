<?php

declare(strict_types=1);

namespace EtoA\User;

use Config;
use EtoA\Core\AbstractRepository;
use Log;

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
        return !is_bool($data) ? $data : null;
    }

    function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('users')
            ->execute()
            ->fetchOne();
    }

    function countActiveSessions(int $timeout): int
    {
        return (int) $this->getConnection()
            ->executeQuery(
                "SELECT COUNT(*)
                FROM user_sessions
                WHERE time_action > ?;",
                [(time() - $timeout)]
            )
            ->fetchOne();
    }

    public function cleanUpPoints(int $threshold = 0): int
    {
        // TODO
        $cfg = Config::getInstance();

        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * (int) $cfg->get('log_threshold_days'));

        $affected = (int) $this->createQueryBuilder()
            ->delete('user_points')
            ->where("point_timestamp<" . $timestamp)
            ->execute();

        Log::add("4", Log::INFO, "$affected Userpunkte-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $affected;
    }
}
