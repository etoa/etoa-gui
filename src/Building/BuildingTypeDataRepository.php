<?php declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\BuildingType;

class BuildingTypeDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildingType::class);
    }

    /**
     * @return array<int, string>
     */
    public function getTypeNames(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('q.id, q.name')
            ->orderBy('q.typeOrder')
            ->addOrderBy('q.name')
            ->getQuery()
            ->execute();

        return array_column($data, 'name', 'id');
    }
}
