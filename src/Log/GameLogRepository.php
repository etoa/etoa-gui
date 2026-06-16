<?php declare(strict_types=1);

namespace EtoA\Log;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\Entity;
use EtoA\Entity\GameLog;
use EtoA\Entity\User;

class GameLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameLog::class);
    }

    /**
     * @return GameLog[]
     */
    public function searchLogs(GameLogSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.timestamp', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function add(int $facility, int $severity, string $message, User $user, ?Alliance $alliance, Entity $entity, int $objectId = 0, int $status = 0, int $level = 0): void
    {
        $item = new GameLog();
        $item->setFacility($facility);
        $item->setSeverity($severity);
        $item->setMessage($message);
        $item->setTimestamp(time());
        $item->setUser($user);
        $item->setAlliance($alliance);
        $item->setEntity($entity);
        $item->setObject($objectId);
        $item->setStatus($status);
        $item->setLevel($level);
        $item->setIp((string) $_SERVER['REMOTE_ADDR']);

        $this->persist($item);
        $this->save();
    }

    public function cleanup(int $threshold): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.timestamp < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();
    }

    public function countBySearch(GameLogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
