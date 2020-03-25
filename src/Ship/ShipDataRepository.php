<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class ShipDataRepository extends AbstractRepository
{
    private const SHIPS_NAMES = 'ships.names';

    /** @var CacheProvider */
    private $cache;

    public function __construct(Connection $connection, CacheProvider $cache)
    {
        parent::__construct($connection);
        $this->cache = $cache;
    }

    public function getShipNames(): array
    {
        if (!$this->cache->contains(self::SHIPS_NAMES)) {
            $names = $this->createQueryBuilder()
                ->select('ship_id, ship_name')
                ->addSelect()
                ->from('ships')
                ->execute()
                ->fetchAll(\PDO::FETCH_KEY_PAIR);

            $this->cache->save(self::SHIPS_NAMES, $names);
        }

        return $this->cache->fetch(self::SHIPS_NAMES);
    }
}
