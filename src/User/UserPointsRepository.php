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

        foreach ($userStats as $stats) {
            $points = new UserPoints();
            $points->setUser($stats->user);
            $points->setTimestamp($timestamp);
            $points->setPoints($stats->points);
            $points->setShipPoints($stats->shipPoints);
            $points->setTechPoints($stats->techPoints);
            $points->setBuildingPoints($stats->buildingPoints);

            $this->persist($points);
        }

        $this->save();
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

    public function removeForUser(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function removePointsByTimestamp(int $timestamp): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where("q.timestamp < :timestamp")
            ->setParameter('timestamp', $timestamp)
            ->getQuery()
            ->execute();
    }
}
