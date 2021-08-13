<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
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
    public function getShipNames(bool $showAll = false, ShipSort $orderBy = null): array
    {
        $search = $showAll ? ShipSearch::create()->show(true)->special(false) : null;

        return $this->searchShipNames($search, $orderBy);
    }

    /**
     * @return array<int, string>
     */
    public function searchShipNames(ShipSearch $search = null, ShipSort $orderBy = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('ship_id', 'ship_name')
            ->addSelect()
            ->from('ships');

        return $this->applySearchSortLimit($qb, $search, $orderBy ?? ShipSort::name(), $limit)
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return array<int, float>
     */
    public function getShipPoints(): array
    {
        $data = $this->createQueryBuilder()
            ->select('ship_id, ship_points')
            ->from('ships')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (float) $value, $data);
    }

    /**
     * @return Ship[]
     */
    public function getAllShips(bool $showAll = false, string $oderBy = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->addSelect()
            ->from('ships');

        if (!$showAll) {
            $qb
                ->where('ship_show = 1')
                ->andWhere('special_ship = 0');
        }

        if ($oderBy !== null) {
            $qb->orderBy($oderBy, 'DESC');
        }

        $data = $qb
            ->addOrderBy('ship_name')
            ->execute()
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $ship = new Ship($row);
            $result[$ship->id] = $ship;
        }

        return $result;
    }

    /**
     * @return Ship[]
     */
    public function getSpecialShips(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->addSelect()
            ->from('ships')
            ->andWhere('special_ship = 1')
            ->orderBy('ship_name')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    /**
     * @return array<int, string>
     */
    public function getFakeableShipNames(): array
    {
        return $this->createQueryBuilder()
            ->select('*')
            ->addSelect()
            ->from('ships')
            ->andWhere('ship_fakeable = 1')
            ->orderBy('ship_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return Ship[]
     */
    public function getShipsWithAction(string $action): array
    {
        $data = $this->shipActionQueryBuilder($action)
            ->select('*')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    /**
     * @return array<int, string>
     */
    public function getShipNamesWithAction(string $action): array
    {
        return $this->shipActionQueryBuilder($action)
            ->select('ship_id, ship_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    private function shipActionQueryBuilder(string $action): QueryBuilder
    {
        return $this->createQueryBuilder()
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
            ->orderBy('ship_name');
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

    /**
     * @return Ship[]
     */
    public function getAllianceShips(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->addSelect()
            ->from('ships')
            ->where('ship_alliance_shipyard_level > 0')
            ->orderBy('ship_alliance_shipyard_level')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    public function getShip(int $shipId): ?Ship
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('ships')
            ->where('ship_show = 1')
            ->andWhere('ship_id = :shipId')
            ->setParameter('shipId', $shipId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Ship($data) : null;
    }

    /**
     * @return Ship[]
     */
    public function getShipsByCategory(int $shipCategory, string $order = 'ship_order', string $sort = 'ASC'): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('ships')
            ->where('ship_cat_id = :category')
            ->andWhere('ship_show=1')
            ->setParameter('category', $shipCategory)
            ->orderBy($order, $sort)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    /**
     * @return Ship[]
     */
    public function getShipsByRace(int $raceId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('ships')
            ->where('ship_race_id = :raceId')
            ->andWhere('ship_buildable = 1')
            ->andWhere('ship_show = 1')
            ->andWhere('special_ship = 0')
            ->setParameter('raceId', $raceId)
            ->orderBy('ship_order')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    public function getTransformedShipForDefense(int $defenseId): ?Ship
    {
        $data = $this->createQueryBuilder()
            ->select('s.*')
            ->from('ships', 's')
            ->innerJoin('s', 'obj_transforms', 't', 't.ship_id=s.ship_id')
            ->where('t.def_id = :defenseId')
            ->setParameters([
                'defenseId' => $defenseId,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Ship($data) : null;
    }

    /**
     * @return Ship[]
     */
    public function searchShips(ShipSearch $search = null, ShipSort $sort = null, int $limit = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, $sort, $limit)
            ->select('*')
            ->from('ships')
            ->execute()
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $ship = new Ship($row);
            $result[$ship->id] = $ship;
        }

        return $result;
    }
}
