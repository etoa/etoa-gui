<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\AbstractRepository;

class DefenseCategoryRepository extends AbstractRepository
{
    /**
     * @return DefenseCategory[]
     */
    public function getAllCategories(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('def_cat')
            ->orderBy('cat_order')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new DefenseCategory($row), $data);
    }

    /**
     * @return array<int, string>
     */
    public function getCategoryNames(): array
    {
        return $this->createQueryBuilder('q')
            ->select('cat_id, cat_name')
            ->from('def_cat')
            ->orderBy('cat_order')
            ->fetchAllKeyValue();
    }
}
