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
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('def_cat')
            ->orderBy('cat_order')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn($row) => new DefenseCategory($row), $data);
    }
}
