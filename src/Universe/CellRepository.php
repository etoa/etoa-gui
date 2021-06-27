<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class CellRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('cells')
            ->execute()
            ->fetchOne();
    }

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

    public function findAllCoordinates(): array
    {
        return $this->createQueryBuilder()
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
}
