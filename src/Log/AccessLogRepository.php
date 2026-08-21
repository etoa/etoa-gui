<?php declare(strict_types=1);

namespace EtoA\Log;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AccessLog;

class AccessLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessLog::class);
    }

    public function add(string $target, string $sessionId, string $sub, string $domain): void
    {
        $this->createQueryBuilder('q')
            ->insert('accesslog')
            ->values([
                'target' => ':target',
                'timestamp' => ':now',
                'sid' => ':sessionId',
                'sub' => ':sub',
                'domain' => ':domain',
            ])
            ->setParameters([
                'target' => $target,
                'now' => time(),
                'sessionId' => $sessionId,
                'sub' => $sub,
                'domain' => $domain,
            ])
            ->executeQuery();
    }

    /**
     * @return array<string, int>
     */
    public function getCountsForDomain(string $domain): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('q.target, COUNT(q.target) cnt')
            ->where('q.domain = :domain')
            ->groupBy('q.target')
            ->orderBy('cnt', 'DESC')
            ->setParameter('domain', $domain)
            ->getQuery()
            ->execute();

        return array_column($data, 'cnt', 'target');
    }

    /**
     * @return array<string, int>
     */
    public function getCountsForTarget(string $domain, string $target): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('q.sub, COUNT(q.target) cnt')
            ->where('q.domain = :domain')
            ->andWhere('q.target = :target')
            ->groupBy('q.sub')
            ->orderBy('cnt', 'DESC')
            ->setParameters([
                'domain' => $domain,
                'target' => $target,
            ])
            ->getQuery()
            ->execute();

        return array_map('intval', array_column($data, 'cnt', 'sub'));
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
