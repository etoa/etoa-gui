<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\SolarType;

class SolarTypeRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SolarType::class);
    }

    /**
     * Returns an array of solar types names indexed by their id.
     *
     * @return array<int, string>
     */
    public function getSolarTypeNames(bool $showAll = false, bool $orderById = false): array
    {
        $qb = $this->createQueryBuilder('q')
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
        $data = $this->createQueryBuilder('q')
            ->select('s.*')
            ->from('sol_types', 's')
            ->andWhere('s.sol_type_consider = 1')
            ->orderBy('s.' . $order, $sort)
            ->fetchAllAssociative();

        return array_map(fn ($row) => new SolarType($row), $data);
    }

    public function getName(int $id): ?string
    {
        $data = $this->createQueryBuilder('q')
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
