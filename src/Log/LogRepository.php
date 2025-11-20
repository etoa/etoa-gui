<?php declare(strict_types=1);

namespace EtoA\Log;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Log;
use Symfony\Component\HttpFoundation\RequestStack;

class LogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry,private readonly RequestStack $requestStack,)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * @return Log[]
     */
    public function searchLogs(LogSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.timestamp', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function add(int $facility, int $severity, string $message): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $log = new Log();
        $log->setFacility($facility);
        $log->setSeverity($severity);
        $log->setTimestamp(time());
        $log->setIp($request->server->get('REMOTE_ADDR'));
        $log->setMessage($message);

        $this->persist($log);
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

    public function countBySearch(LogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
