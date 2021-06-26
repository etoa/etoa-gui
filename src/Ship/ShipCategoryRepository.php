<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\AbstractRepository;

class ShipCategoryRepository extends AbstractRepository
{
    /**
     * @return ShipCategory[]
     */
    public function getAllCategories(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('ship_cat')
            ->orderBy('cat_order')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new ShipCategory($row), $data);
    }

    public function getCategory(int $categoryId): ?ShipCategory
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('ship_cat')
            ->where('cat_id = :id')
            ->setParameter('id', $categoryId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new ShipCategory($data) : null;
    }
}
