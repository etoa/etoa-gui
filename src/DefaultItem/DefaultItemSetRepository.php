<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\DefaultItem;
use EtoA\Entity\DefaultItemSet;

class DefaultItemSetRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefaultItemSet::class);
    }

    public function getItemNames(): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('set_id, set_name')
            ->from('default_item_sets')
            ->andWhere('set_active = 1');

        return $qb
            ->orderBy('set_name')
            ->fetchAllKeyValue();
    }

    /**
     * @return DefaultItemSet[]
     */
    public function getSets(bool $activeOnly = true): array
    {
        return $this->findBy($activeOnly?['active'=>true]:null,['name'=>'ASC']);
    }

    public function createSet(string $name): void
    {
        $this->createQueryBuilder('q')
            ->insert('default_item_sets')
            ->values([
                'set_name' => ':name',
                'set_active' => 0,
            ])
            ->setParameter('name', $name)
            ->executeQuery();
    }

    public function toggleSetActive(int $setId): void
    {
        $this->createQueryBuilder('q')
            ->update('default_item_sets')
            ->set('set_active', '!set_active')
            ->where('set_id = :id')
            ->setParameter('id', $setId)
            ->executeQuery();
    }
}
