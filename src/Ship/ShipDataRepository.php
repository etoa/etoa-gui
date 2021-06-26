<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class ShipDataRepository extends AbstractRepository
{
    private const SHIPS_NAMES = 'ships.names';

    private CacheProvider $cache;

    public function __construct(Connection $connection, CacheProvider $cache)
    {
        parent::__construct($connection);
        $this->cache = $cache;
    }

    /**
     * @return array<int, string>
     */
    public function getShipNames(): array
    {
        if (!$this->cache->contains(self::SHIPS_NAMES)) {
            $names = $this->createQueryBuilder()
                ->select('ship_id, ship_name')
                ->addSelect()
                ->from('ships')
                ->orderBy('ship_name')
                ->execute()
                ->fetchAllKeyValue();

            $this->cache->save(self::SHIPS_NAMES, $names);
        }

        return $this->cache->fetch(self::SHIPS_NAMES);
    }

    /**
     * @return array<int, string>
     */
    public function getShipNamesWithAction(string $action): array
    {
        return $this->createQueryBuilder()
            ->select('ship_id, ship_name')
            ->addSelect()
            ->from('ships')
            ->where('ship_buildable=1')
            ->andWhere('special_ship=0')
            ->andWhere('ship_actions LIKE :end OR ship_actions LIKE :begin OR ship_actions LIKE :middle OR ship_actions LIKE :only')
            ->setParameters([
                'begin' => '%,' . $action,
                'end' => $action . ',%',
                'middle' => '%,' . $action . ',%',
                'only' => $action,
            ])
            ->orderBy('ship_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return Ship[]
     */
    public function getShipWithPowerProduction(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->addSelect()
            ->from('ships')
            ->where('ship_prod_power > 0')
            ->orderBy('ship_order')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }
}
