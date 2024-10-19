<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\AbstractRepository;

class ShipDataRepository extends AbstractRepository
{
    /**
     * @return array<int, string>
     */
    public function getShipNames(bool $showAll = false, ShipSort $orderBy = null): array
    {
        $search = !$showAll ? ShipSearch::create()->show(true)->special(false) : null;

        return $this->searchShipNames($search, $orderBy);
    }

    /**
     * @return array<int, string>
     */
    public function searchShipNames(ShipSearch $search = null, ShipSort $orderBy = null, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('ship_id', 'ship_name')
            ->from('ships');

        return $this->applySearchSortLimit($qb, $search, $orderBy ?? ShipSort::name(), $limit)
            ->fetchAllKeyValue();
    }

    /**
     * @return array<int, float>
     */
    public function getShipPoints(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('ship_id', 'ship_points')
            ->from('ships')
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (float) $value, $data);
    }

    public function updateShipPoints(int $shipId, float $points): void
    {
        $this->createQueryBuilder('q')
            ->update('ships')
            ->set('ship_points', ':points')
            ->where('ship_id = :shipId')
            ->setParameters([
                'shipId' => $shipId,
                'points' => $points,
            ])
            ->executeQuery();
    }

    /**
     * @return Ship[]
     */
    public function getAllShips(bool $showAll = false, string $oderBy = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
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
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ships')
            ->andWhere('special_ship = 1')
            ->orderBy('ship_name')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    /**
     * @return array<int, string>
     */
    public function getFakeableShipNames(): array
    {
        return $this->createQueryBuilder('q')
            ->select('*')
            ->from('ships')
            ->andWhere('ship_fakeable = 1')
            ->orderBy('ship_name')
            ->fetchAllKeyValue();
    }

    /**
     * @return Ship[]
     */
    public function getShipsWithAction(string $action): array
    {
        $data = $this->shipActionQueryBuilder($action)
            ->select('*')
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
            ->fetchAllKeyValue();
    }

    private function shipActionQueryBuilder(string $action): QueryBuilder
    {
        return $this->createQueryBuilder('q')
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
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ships')
            ->where('ship_prod_power > 0')
            ->orderBy('ship_order')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    /**
     * @return Ship[]
     */
    public function getAllianceShips(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ships')
            ->where('ship_alliance_shipyard_level > 0')
            ->orderBy('ship_alliance_shipyard_level')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    public function getShip(int $shipId, bool $onlyShipShow = true): ?Ship
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ships')
            ->andWhere('ship_id = :shipId')
            ->setParameter('shipId', $shipId);

        if ($onlyShipShow) {
            $qb->andWhere('ship_show = 1');
        }

        $data = $qb
            ->fetchAssociative();

        return $data !== false ? new Ship($data) : null;
    }

    /**
     * @return Ship[]
     */
    public function getShipsByCategory(int $shipCategory, string $order = 'ship_order', string $sort = 'ASC'): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ships')
            ->where('ship_cat_id = :category')
            ->andWhere('ship_show=1')
            ->setParameter('category', $shipCategory)
            ->orderBy($order, $sort)
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    /**
     * @return Ship[]
     */
    public function getShipsByRace(int $raceId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ships')
            ->where('ship_race_id = :raceId')
            ->andWhere('ship_buildable = 1')
            ->andWhere('ship_show = 1')
            ->andWhere('special_ship = 0')
            ->setParameter('raceId', $raceId)
            ->orderBy('ship_order')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Ship($row), $data);
    }

    public function getTransformedShipForDefense(int $defenseId): ?Ship
    {
        $data = $this->createQueryBuilder('q')
            ->select('s.*')
            ->from('ships', 's')
            ->innerJoin('s', 'obj_transforms', 't', 't.ship_id=s.ship_id')
            ->where('t.def_id = :defenseId')
            ->setParameters([
                'defenseId' => $defenseId,
            ])
            ->fetchAssociative();

        return $data !== false ? new Ship($data) : null;
    }

    /**
     * @return Ship[]
     */
    public function searchShips(ShipSearch $search = null, ShipSort $sort = null, int $limit = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort, $limit)
            ->select('*')
            ->from('ships')
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $ship = new Ship($row);
            $result[$ship->id] = $ship;
        }

        return $result;
    }

    public function searchShip(ShipSearch $search = null): ?Ship
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('*')
            ->from('ships')
            ->setMaxResults(1)
            ->fetchAssociative();

        return $data !== false ? new Ship($data) : null;
    }
}
