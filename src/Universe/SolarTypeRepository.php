<?php declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class SolarTypeRepository extends AbstractRepository
{
    /**
     * Returns an array of solar types names indexed by their id.
     *
     * @return array<int, string>
     */
    public function getSolarTypeNames(bool $showAll = false): array
    {
        $qb = $this->createQueryBuilder()
            ->select('s.sol_type_id, s.sol_type_name')
            ->from('sol_types', 's');

        if (!$showAll) {
            $qb->andWhere('s.sol_type_consider = 1');
        }

        return $qb
            ->orderBy('s.sol_type_name')
            ->execute()
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
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn($row) => new SolarType($row), $data);
    }
}
