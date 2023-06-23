<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use EtoA\Core\AbstractRepository;

class SolarTypeRepository extends AbstractRepository
{
    /**
     * Returns an array of solar types names indexed by their id.
     *
     * @return array<int, string>
     */
    public function getSolarTypeNames(bool $showAll = false, bool $orderById = false): array
    {
        $qb = $this->createQueryBuilder()
            ->select('sol_type_id', 'sol_type_name')
            ->from('sol_types');

        if (!$showAll) {
            $qb->andWhere('sol_type_consider = 1');
        }

        return $qb
            ->orderBy($orderById ? 'sol_type_id' : 'sol_type_name')
            ->fetchAllKeyValue();
    }

    /**
     * @return SolarType[]
     */
    public function getSolarTypes(string $order = 'sol_type_name', string $sort = 'ASC'): array
    {
        $data = $this->createQueryBuilder()
            ->select('s.*')
            ->from('sol_types', 's')
            ->andWhere('s.sol_type_consider = 1')
            ->orderBy('s.' . $order, $sort)
            ->fetchAllAssociative();

        return array_map(fn ($row) => new SolarType($row), $data);
    }

    public function find(int $id): ?SolarType
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('sol_types')
            ->where('sol_type_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchAssociative();

        return $data !== false ? new SolarType($data) : null;
    }

    public function getName(int $id): ?string
    {
        $data = $this->createQueryBuilder()
            ->select('sol_type_name')
            ->from('sol_types')
            ->where('sol_type_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    /**
     * @return array<int, array{name: string, cnt: string}>
     */
    public function getNumberOfNamedSystemsByType(): array
    {
        return $this->getConnection()
            ->fetchAllAssociative(
                "SELECT
                    t.sol_type_name as name,
                    COUNT(id) as cnt
                FROM
                    stars s
                INNER JOIN
                    sol_types t
                ON
                    s.type_id = t.sol_type_id
                    AND s.name != ''
                GROUP BY
                    s.type_id
                ORDER BY
                    cnt DESC;"
            );
    }
}
