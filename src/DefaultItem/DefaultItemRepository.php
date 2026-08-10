<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\DefaultItem;
use EtoA\Entity\DefaultItemSet;

class DefaultItemRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefaultItem::class);
    }

    public function getItem(int $itemId): ?DefaultItem
    {
        return $this->find($itemId);
    }

    public function deleteSet(int $setId): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.defaultItemSet = :setId')
            ->setParameter('setId', $setId)
            ->getQuery()
            ->execute();

        $this->getEntityManager()->createQueryBuilder()
            ->delete(DefaultItemSet::class, 'q')
            ->where('q.id = :setId')
            ->setParameter('setId', $setId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<string, array<DefaultItem>>
     */
    public function getItemsGroupedByCategory(DefaultItemSet $set): array
    {
        $data = $set->getDefaultItems();

        $result = [];
        foreach ($data as $row) {
            $result[$row->getCat()][] = $row;
        }

        return $result;
    }

    public function addItemToSet(DefaultItemSet $set, string $cat, int $objectId, int $count): bool
    {
        $exists = (bool) $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->where('q.defaultItemSet = :set')
            ->andWhere('q.cat = :cat')
            ->andWhere('q.objectId = :objectId')
            ->setParameters([
                'set' => $set,
                'cat' => $cat,
                'objectId' => $objectId,
            ])
            ->getQuery()
            ->getSingleScalarResult();

        if ($exists) {
            return false;
        }

        $item = (new DefaultItem())
            ->setCat($cat)
            ->setObjectId($objectId)
            ->setCount($count);

        // keeps the inverse side in sync, so a re-render sees the new item
        $set->addDefaultItem($item);

        $this->persist($item);
        $this->save();

        return true;
    }

    public function getItemCount(int $itemId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('q.count')
            ->where('q.id = :id')
            ->setParameter('id', $itemId)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    public function updateItemCount(int $itemId, int $count): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.count', ':count')
            ->where('q.id = :id')
            ->setParameters([
                'id' => $itemId,
                'count' => $count,
            ])
            ->getQuery()
            ->execute();
    }

    public function removeItem(int $itemId): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.id = :id')
            ->setParameter('id', $itemId)
            ->getQuery()
            ->execute();
    }
}
