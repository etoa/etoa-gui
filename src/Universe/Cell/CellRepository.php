<?php

declare(strict_types=1);

namespace EtoA\Universe\Cell;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Cell;

class CellRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cell::class);
    }

    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        return $this->createQueryBuilder('q')
            ->select("q.id")
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * @return array{x: int, y: int}
     */
    public function getSectorDimensions(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('MAX(q.sx) as sx', 'MAX(q.sy) as sy')
            ->getQuery()
            ->execute();

        return $data
            ? [
                'x' => (int) $data[0]['sx'],
                'y' => (int) $data[0]['sy'],
            ] : [
                'x' => 0,
                'y' => 0,
            ];
    }

    /**
     * @return array{x: int, y: int}
     */
    public function getCellDimensions(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('MAX(q.cx) as cx', 'MAX(q.cy) as cy')
            ->getQuery()
            ->execute();

        return $data
            ? [
                'x' => (int) $data[0]['cx'],
                'y' => (int) $data[0]['cy'],
            ] : [
                'x' => 0,
                'y' => 0,
            ];
    }

    /**
     * @return array<Cell>
     */
    public function findAllCoordinates(): array
    {
        return $this->findAll();
    }

    public function create(int $sx, int $sy, int $cx, int $cy): int
    {
        $this->createQueryBuilder('q')
            ->insert('cells')
            ->values([
                'sx' => ':sx',
                'sy' => ':sy',
                'cx' => ':cx',
                'cy' => ':cy',
            ])
            ->setParameters([
                'sx' => $sx,
                'sy' => $sy,
                'cx' => $cx,
                'cy' => $cy,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @param array<array<int>> $values
     */
    public function addMultiple(array $values): void
    {
        $this->getConnection()
            ->executeStatement(
                "INSERT INTO cells
                (
                    sx,
                    sy,
                    cx,
                    cy
                )
                VALUES " .
                    implode(',', array_fill(0, count($values), '(?, ?, ? ,?)')),
                array_merge(...$values)
            );
    }

    /**
     * @return array<CellPopulation>
     */
    public function getCellPopulation(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'q.sx',
                'q.cx',
                'q.sy',
                'q.cy',
                'COUNT(p.entity) AS cnt'
            )
            ->innerJoin('App:Entity', 'e', 'WITH', 'e.cell = q')
            ->innerJoin('App:Planet', 'p', 'WITH', 'p.entity = e AND p.user IS NOT NULL')
            ->groupBy('e.cell')
            ->getQuery()
            ->getArrayResult();

        return array_map(fn (array $arr) => new CellPopulation($arr), $data);
    }

    /**
     * @return int[]
     */
    public function getUserCellIds(int $userId): array
    {
        return $this->createQueryBuilder('q')
            ->select('DISTINCT q.id')
            ->innerJoin('App:Entity', 'e', 'WITH', 'q.id = e.cell')
            ->innerJoin('App:Planet', 'p', 'WITH', 'p.entity = e.id AND p.user = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * @return array<CellPopulation>
     */
    public function getCellPopulationForUser(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'q.sx',
                'q.cx',
                'q.sy',
                'q.cy',
                'COUNT(p.entity) AS cnt'
            )
            ->innerJoin('App:Entity', 'e', 'WITH', 'e.cell = q')
            ->innerJoin('App:Planet', 'p', 'WITH', 'p.entity = e AND p.user = :user')
            ->groupBy('e.cell')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getArrayResult();

        return array_map(fn (array $arr) => new CellPopulation($arr), $data);
    }

    /**
     * @return array<CellPopulation>
     */
    public function getCellPopulationForUserAlliance(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'q.sx',
                'q.cx',
                'q.sy',
                'q.cy',
                'COUNT(p.entity) AS cnt'
            )
            ->innerJoin('App:Entity', 'e', 'WITH', 'e.cell = q')
            ->innerJoin('App:Planet', 'p', 'WITH', 'p.entity = e')
            ->innerJoin('App:User', 'owner', 'WITH', 'p.user = owner')
            // the alliance of the given user, joined back onto every member's planets
            ->innerJoin('App:User', 'u', 'WITH', 'owner.alliance = u.alliance AND u.alliance IS NOT NULL AND u.id = :user')
            ->groupBy('e.cell')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getArrayResult();

        return array_map(fn (array $arr) => new CellPopulation($arr), $data);
    }

    public function getCellIdByCoordinates(int $sx, int $sy, int $cx, int $cy): ?Cell
    {
        return $this->findOneBy(['sy'=>$sy,'sx'=>$sx,'cx'=>$cx,'cy'=>$cy]);
    }
}
