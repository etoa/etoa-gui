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
                flatten($values)
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
                'COUNT(DISTINCT(p.id)) AS cnt'
            )
            ->innerJoin('App:Entity', 'e', 'WITH', 'e.cellId = q.id')
            ->innerJoin('App:Planet', 'p', 'WITH', 'p.id = e.id AND p.user > 0')
            ->groupBy('e.cellId')
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
            ->innerJoin('App:Entity', 'e', 'WITH', 'q.id = e.id')
            ->innerJoin('App:Planet', 'p', 'WITH', 'p.entity = e.id AND p.user = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<CellPopulation>
     */
    public function getCellPopulationForUser(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'c.sx',
                'c.cx',
                'c.sy',
                'c.cy',
                'COUNT(p.id) AS cnt'
            )
            ->from('cells', 'c')
            ->innerJoin('c', 'entities', 'e', 'e.cell_id = c.id')
            ->innerJoin('e', 'planets', 'p', 'p.id = e.id AND p.planet_user_id = :user')
            ->groupBy('e.cell_id')
            ->setParameter('user', $userId)
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new CellPopulation($arr), $data);
    }

    /**
     * @return array<CellPopulation>
     */
    public function getCellPopulationForUserAlliance(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'c.sx',
                'c.cx',
                'c.sy',
                'c.cy',
                'COUNT(p.id) AS cnt'
            )
            ->from('cells', 'c')
            ->innerJoin('c', 'entities', 'e', 'e.cell_id = c.id')
            ->innerJoin('e', 'planets', 'p', 'p.id = e.id')
            ->innerJoin('p', 'users', 'a', 'p.planet_user_id = a.user_id')
            ->innerJoin('a', 'users', 'u', 'a.user_alliance_id=u.user_alliance_id AND u.user_alliance_id > 0 AND u.user_id = :user')
            ->groupBy('e.cell_id')
            ->setParameter('user', $userId)
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new CellPopulation($arr), $data);
    }

    public function getCellById(int $id): ?Cell
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('cells')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchAssociative();

        return $data !== false ? new Cell($data) : null;
    }

    public function getCellIdByCoordinates(int $sx, int $sy, int $cx, int $cy): ?Cell
    {
        return $this->findOneBy(['sy'=>$sy,'sx'=>$sx,'cx'=>$cx,'cy'=>$cy]);
    }
}
