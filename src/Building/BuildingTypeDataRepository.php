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
        return $this->createQueryBuilder('q')
            ->select('type_id, type_name')
            ->from('building_types')
            ->orderBy('type_order')
            ->addOrderBy('type_name')
            ->fetchAllKeyValue();
    }
}
