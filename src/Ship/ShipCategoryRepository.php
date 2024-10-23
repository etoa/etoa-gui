<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\ShipCategory;

class ShipCategoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipCategory::class);
    }

    /**
     * @return ShipCategory[]
     */
    public function getAllCategories(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ship_cat')
            ->orderBy('cat_order')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new ShipCategory($row), $data);
    }

    /**
     * @return array<int, string>
     */
    public function getCategoryNames(): array
    {
        return $this->createQueryBuilder('q')
            ->select('cat_id, cat_name')
            ->from('ship_cat')
            ->orderBy('cat_order')
            ->fetchAllKeyValue();
    }

    public function getCategory(int $categoryId): ?ShipCategory
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ship_cat')
            ->where('cat_id = :id')
            ->setParameter('id', $categoryId)
            ->fetchAssociative();

        return $data !== false ? new ShipCategory($data) : null;
    }
}
