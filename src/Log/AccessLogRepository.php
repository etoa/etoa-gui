<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\AbstractRepository;

class AccessLogRepository extends AbstractRepository
{
    public function add(string $target, string $sessionId, string $sub, string $domain): void
    {
        $this->createQueryBuilder()
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
            ->execute();
    }

    /**
     * @return array<string, int>
     */
    public function getCountsForDomain(string $domain): array
    {
        $data = $this->createQueryBuilder()
            ->select('target, COUNT(target) cnt')
            ->from('accesslog')
            ->where('domain = :domain')
            ->groupBy('target')
            ->orderBy('cnt', 'DESC')
            ->setParameter('domain', $domain)
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return array<string, int>
     */
    public function getCountsForTarget(string $domain, string $target): array
    {
        $data = $this->createQueryBuilder()
            ->select('sub, COUNT(target) cnt')
            ->from('accesslog')
            ->where('domain = :domain')
            ->andWhere('target = :target')
            ->groupBy('sub')
            ->orderBy('cnt', 'DESC')
            ->setParameters([
                'domain' => $domain,
                'target' => $target,
            ])
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder()
            ->delete('accesslog')
            ->execute();
    }
}
