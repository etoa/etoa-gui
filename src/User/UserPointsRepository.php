<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserPointsRepository extends AbstractRepository
{
    /**
     * @param UserStatistic[] $userStats
     */
    public function addEntries(array $userStats, int $timestamp): void
    {
        if (count($userStats) === 0) {
            return;
        }

        $parameters = [];
        foreach ($userStats as $stats) {
            $parameters[] = $stats->userId;
            $parameters[] = $timestamp;
            $parameters[] = $stats->points;
            $parameters[] = $stats->shipPoints;
            $parameters[] = $stats->techPoints;
            $parameters[] = $stats->buildingPoints;
        }

        $insertRow = implode(',', array_fill(0, count($userStats), '(?, ?, ?, ?, ?, ?)'));

        $this->getConnection()->executeQuery('
            INSERT INTO user_points (
                point_user_id,
                point_timestamp,
                point_points,
                point_ship_points,
                point_tech_points,
                point_building_points
            ) VALUES ' . $insertRow, $parameters)
        ->execute();
    }

    /**
     * @return UserPoints[]
     */
    public function getPoints(int $userId, int $limit = null, int $start = null, int $end = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select("*")
            ->from('user_points')
            ->where('point_user_id = :userId')
            ->andWhere('point_points > 0')
            ->setParameter('userId', $userId)
            ->orderBy('point_timestamp', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($start > 0) {
            $qb
                ->andWhere('point_timestamp > :start')
                ->setParameter('start', $start);
        }

        if ($end > 0) {
            $qb
                ->andWhere('point_timestamp < :end')
                ->setParameter('end', $end);
        }

        $data = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserPoints($row), $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_points')
            ->execute()
            ->fetchOne();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('user_points')
            ->where('point_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}
