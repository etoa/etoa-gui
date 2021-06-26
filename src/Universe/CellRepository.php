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
