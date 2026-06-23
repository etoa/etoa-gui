<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\DefenseCategory;

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
        return $this->findBy([],['order'=>'DESC']);
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
