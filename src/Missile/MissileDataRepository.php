<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class MissileDataRepository extends AbstractRepository
{
    private const MISSILES_NAMES = 'missiles.names';

    private CacheProvider $cache;

    public function __construct(Connection $connection, CacheProvider $cache)
    {
        parent::__construct($connection);
        $this->cache = $cache;
    }

    /**
     * @return array<int, string>
     */
    public function getMissileNames(bool $showAll = false): array
    {
        $qb = $this->createQueryBuilder()
            ->select('missile_id, missile_name')
            ->addSelect()
            ->from('missiles');

        if (!$showAll) {
            $qb->where('missile_show = 1');
        }

        return $qb
            ->orderBy('missile_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    public function getMissile(int $missileId): ?Missile
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('missiles')
            ->where('missile_show=1')
            ->andWhere('missile_id = :missileId')
            ->setParameter('missileId', $missileId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Missile($data) : null;
    }

    /**
     * @return Missile[]
     */
    public function getMissiles(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('missiles')
            ->where('missile_show=1')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Missile($row), $data);
    }
}
