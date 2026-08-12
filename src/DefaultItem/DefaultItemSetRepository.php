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
        return $this->findBy(['active'=>1],['name'=>'ASC']);
    }

    /**
     * @return DefaultItemSet[]
     */
    public function getSets(bool $activeOnly = true): array
    {
        return $this->findBy($activeOnly?['active'=>true]:[],['name'=>'ASC']);
    }

    public function createSet(string $name): void
    {
        $set = new DefaultItemSet();
        $set->setName($name);
        $set->setActive(false);

        $this->persist($set);
        $this->save();
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
