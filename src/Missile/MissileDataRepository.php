<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class MissileDataRepository extends AbstractRepository
{
    private const MISSILES_NAMES = 'missiles.names';

    /** @var CacheProvider */
    private $cache;

    public function __construct(Connection $connection, CacheProvider $cache)
    {
        parent::__construct($connection);
        $this->cache = $cache;
    }

    public function getMissileNames(): array
    {
        if (!$this->cache->contains(self::MISSILES_NAMES)) {
            $names = $this->createQueryBuilder()
                ->select('missile_id, missile_name')
                ->addSelect()
                ->from('missiles')
                ->execute()
                ->fetchAll(\PDO::FETCH_KEY_PAIR);

            $this->cache->save(self::MISSILES_NAMES, $names);
        }

        return $this->cache->fetch(self::MISSILES_NAMES);
    }
}
