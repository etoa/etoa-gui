<?php declare(strict_types=1);

namespace EtoA\HostCache;

use EtoA\Core\AbstractRepository;

class HostCacheRepository extends AbstractRepository
{
    public function getAddr(string $host): ?string
    {
        $data = $this->createQueryBuilder('q')
            ->select('addr')
            ->from('hostname_cache')
            ->where('host = :host')
            ->andWhere('timestamp > :time')
            ->setParameters([
                'host' => $host,
                'time' => time() - 86400,
            ])
            ->setMaxResults(1)
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function getHost(string $ip): ?string
    {
        $data = $this->createQueryBuilder('q')
            ->select('host')
            ->from('hostname_cache')
            ->where('addr = :ip')
            ->andWhere('timestamp > :time')
            ->setParameters([
                'ip' => $ip,
                'time' => time() - 86400,
            ])
            ->setMaxResults(1)
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function store(string $host, string $ip): void
    {
        $this->getConnection()
            ->executeQuery('
                REPLACE INTO hostname_cache
                (addr, host, timestamp)
                VALUES (:addr, :host, :time)
            ', [
                'addr' => $ip,
                'host' => $host,
                'time' => time(),
            ]);
    }

    public function clear(): void
    {
        $this->createQueryBuilder('q')
            ->delete('v')
            ->where('timestamp < :time')
            ->setParameter(':time', time() - 86400);
    }
}
