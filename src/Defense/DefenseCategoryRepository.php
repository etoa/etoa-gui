<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;

class DefenseCategoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefenseCategory::class);
    }

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
