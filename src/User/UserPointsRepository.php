<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;
use EtoA\Entity\UserPoints;

class UserPointsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPoints::class);
    }

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
            ) VALUES ' . $insertRow, $parameters);
    }

    /**
     * @return UserPoints[]
     */
    public function getPoints(User $user, int $limit = null, int $start = null, int $end = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.points > 0')
            ->setParameter('user', $user)
            ->orderBy('q.timestamp', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($start > 0) {
            $qb
                ->andWhere('q.timestamp > :start')
                ->setParameter('start', $start);
        }

        if ($end > 0) {
            $qb
                ->andWhere('q.timestamp < :end')
                ->setParameter('end', $end);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder('q')
            ->delete('user_points')
            ->where('point_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }
}
