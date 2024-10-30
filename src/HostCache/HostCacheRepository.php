<?php declare(strict_types=1);

namespace EtoA\HostCache;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\HostnameCache;

class HostCacheRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HostnameCache::class);
    }

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
        $result = $this->createQueryBuilder('q')
            ->select('q.host')
            ->where('q.addr = :ip')
            ->andWhere('q.timestamp > :time')
            ->setParameters([
                'ip' => $ip,
                'time' => time() - 86400,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result?$result['host']:$result;
    }

    public function store(string $host, string $ip): void
    {
        $hostModel = $this->findOneBy(['addr'=>$ip])??new HostnameCache();

        $hostModel->setAddr($ip);
        $hostModel->setTimestamp(time());
        $hostModel->setHost($host);

        $this->getEntityManager()->persist($hostModel);
        $this->save();
    }

    public function clear(): void
    {
        $this->createQueryBuilder('q')
            ->delete('v')
            ->where('timestamp < :time')
            ->setParameter(':time', time() - 86400);
    }
}
