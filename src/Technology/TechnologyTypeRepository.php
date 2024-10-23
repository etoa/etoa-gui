<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;
use EtoA\Entity\TechnologyType;
use Doctrine\Persistence\ManagerRegistry;

class TechnologyTypeRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechnologyType::class);
    }

    /**
     * @return TechnologyType[]
     */
    public function getTypes(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('tech_types')
            ->orderBy('type_order')
            ->addOrderBy('type_name')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new TechnologyType($row), $data);
    }

    /**
     * @return array<int, string>
     */
    public function getTypeNames(): array
    {
        return $this->createQueryBuilder('q')
            ->select('type_id, type_name')
            ->from('tech_types')
            ->orderBy('type_order')
            ->addOrderBy('type_name')
            ->fetchAllKeyValue();
    }
}
