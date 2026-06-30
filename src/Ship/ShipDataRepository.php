<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Defense;
use EtoA\Entity\Race;
use EtoA\Entity\Ship;
use EtoA\Entity\ShipCategory;

/**
 * @extends ServiceEntityRepository<Ship>
 *
 * @method Ship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ship[]    findAll()
 * @method Ship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ship::class);
    }

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
        $qb = $this->createQueryBuilder('q');

        return $this->applySearchSortLimit($qb, $search, $orderBy ?? ShipSort::name(), $limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, float>
     */
    public function getShipPoints(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('q.id', 'q.points')
            ->getQuery()
            ->execute();

        return array_column($data, 'points', 'id');
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
        $qb = $this->createQueryBuilder('q');


        if (!$showAll) {
            $qb
                ->where('q.show = 1')
                ->andWhere('q.special = 0');
        }

        if ($oderBy !== null) {
            $qb->orderBy($oderBy, 'DESC');
        }

        return $qb
            ->addOrderBy('q.name')
            ->getQuery()
            ->execute();
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
        return $this->shipActionQueryBuilder($action)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, string>
     */
    public function getShipNamesWithAction(string $action): array
    {
        return $this->shipActionQueryBuilder($action)
            ->select('q.id, q.name')
            ->getQuery()
            ->getResult();
    }

    private function shipActionQueryBuilder(string $action): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->where('q.buildable=1')
            ->andWhere('q.special=0')
            ->andWhere('q.actions LIKE :end OR q.actions LIKE :begin OR q.actions LIKE :middle OR q.actions LIKE :only')
            ->setParameters([
                'begin' => '%,' . $action,
                'end' => $action . ',%',
                'middle' => '%,' . $action . ',%',
                'only' => $action,
            ])
            ->orderBy('q.name');
    }

    /**
     * @return Ship[]
     */
    public function getShipWithPowerProduction(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.powerProduction > 0')
            ->orderBy('q.order')
            ->getQuery()
            ->execute();
    }

    /**
     * @return Ship[]
     */
    public function getAllianceShips(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.allianceShipyardLevel > 0')
            ->orderBy('q.allianceShipyardLevel')
            ->getQuery()
            ->execute();
    }

    public function getShip(int $shipId, bool $onlyShipShow = true): ?Ship
    {
        $qb = $this->createQueryBuilder('q')
            ->andWhere('q.id = :shipId')
            ->setParameter('shipId', $shipId);

        if ($onlyShipShow) {
            $qb->andWhere('q.show = 1');
        }

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Ship[]
     */
    public function getShipsByCategory(int|ShipCategory $shipCategory, string $order = 'order', string $sort = 'ASC'): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.cat = :category')
            ->andWhere('q.show=1')
            ->setParameter('category', $shipCategory)
            ->orderBy("q.$order", $sort)
            ->getQuery()
            ->execute();
    }

    /**
     * @return Ship[]
     */
    public function getShipsByRace(int|Race $raceId): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.race = :raceId')
            ->andWhere('q.buildable = 1')
            ->andWhere('q.show = 1')
            ->andWhere('q.special = 0')
            ->setParameter('raceId', $raceId)
            ->orderBy('q.order')
            ->getQuery()
            ->execute();
    }

    public function getTransformedShipForDefense(int|Defense $defenseId): ?Ship
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('App:ShipTransform', 't', 'WITH', 't.ship=q.id')
            ->where('t.defense = :defenseId')
            ->setParameters([
                'defenseId' => $defenseId,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Ship[]
     */
    public function searchShips(ShipSearch $search = null, ShipSort $sort = null, int $limit = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort, $limit)
            ->select()
            ->getQuery()
            ->execute();
    }

    public function searchShip(ShipSearch $search = null): ?Ship
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
