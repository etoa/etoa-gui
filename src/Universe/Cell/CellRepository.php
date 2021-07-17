<?php

declare(strict_types=1);

namespace EtoA\Universe\Cell;

use EtoA\Core\AbstractRepository;

class CellRepository extends AbstractRepository
{
    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select("id")
            ->from('cells')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('cells')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return array{x: int, y: int}
     */
    public function getSectorDimensions(): array
    {
        $data = $this->createQueryBuilder()
            ->select('MAX(sx)', 'MAX(sy)')
            ->from('cells')
            ->execute()
            ->fetchNumeric();

        return $data !== false
            ? [
                'x' => (int) $data[0],
                'y' => (int) $data[1],
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
        $data = $this->createQueryBuilder()
            ->select('MAX(cx)', 'MAX(cy)')
            ->from('cells')
            ->execute()
            ->fetchNumeric();

        return $data !== false
            ? [
                'x' => (int) $data[0],
                'y' => (int) $data[1],
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
        $data = $this->createQueryBuilder()
            ->select(
                "id",
                "sx",
                "sy",
                "cx",
                "cy"
            )
            ->from('cells')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new Cell($arr), $data);
    }

    public function create(int $sx, int $sy, int $cx, int $cy): int
    {
        $this->createQueryBuilder()
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
            ->execute();

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
        $data = $this->createQueryBuilder()
            ->select(
                'c.sx',
                'c.cx',
                'c.sy',
                'c.cy',
                'COUNT(p.id) AS cnt'
            )
            ->from('cells', 'c')
            ->innerJoin('c', 'entities', 'e', 'e.cell_id = c.id')
            ->innerJoin('e', 'planets', 'p', 'p.id = e.id AND p.planet_user_id > 0')
            ->groupBy('e.cell_id')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new CellPopulation($arr), $data);
    }

    /**
     * @return array<CellPopulation>
     */
    public function getCellPopulationForUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new CellPopulation($arr), $data);
    }

    /**
     * @return array<CellPopulation>
     */
    public function getCellPopulationForUserAlliance(int $userId): array
    {
        $data = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new CellPopulation($arr), $data);
    }

    public function getCellIdByCoordinates(int $sx, int $sy, int $cx, int $cy): ?Cell
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('cells')
            ->where('sx = :sx')
            ->andWhere('sy = :sy')
            ->andWhere('cx = :cx')
            ->andWhere('cy = :cy')
            ->setParameters([
                'sx' => $sx,
                'sy' => $sy,
                'cx' => $cx,
                'cy' => $cy,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Cell($data) : null;
    }
}
