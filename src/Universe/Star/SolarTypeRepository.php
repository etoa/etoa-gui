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
        $constraints = [];

        if (!$showAll) {
            $constraints=['consider'=>1];
        }

        $order = $orderById ? ['id'=>'ASC']:['name'=>'ASC'];

        return $this->findBy($constraints,$order);
    }

    /**
     * @return SolarType[]
     */
    public function getSolarTypes(string $order = 'name', string $sort = 'ASC'): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.consider = 1')
            ->orderBy("q.$order" , $sort)
            ->getQuery()
            ->execute();
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
