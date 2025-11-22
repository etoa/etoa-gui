<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

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
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('default_items')
            ->where('item_id = :id')
            ->setParameter('id', $itemId)
           ->fetchAssociative();

        return $data !== false ? DefaultItem::createFromData($data) : null;
    }

    public function deleteSet(int $setId): void
    {
        $this->createQueryBuilder('q')
            ->delete('default_items')
            ->where('item_set_id = :id')
            ->setParameter('id', $setId)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('default_item_sets')
            ->where('set_id = :id')
            ->setParameter('id', $setId)
            ->executeQuery();
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

    public function addItemToSet(int $setId, string $cat, int $objectId, int $count): bool
    {
        $exists = (bool) $this->createQueryBuilder('q')
            ->select('item_id')
            ->from('default_items')
            ->where('item_set_id = :setId')
            ->andWhere('item_cat = :cat')
            ->andWhere('item_object_id = :objectId')
            ->setParameters([
                'setId' => $setId,
                'cat' => $cat,
                'objectId' => $objectId,
            ])
            ->fetchOne();

        if ($exists) {
            return false;
        }

        return (bool) $this->createQueryBuilder('q')
            ->insert('default_items')
            ->values([
                'item_set_id' => ':setId',
                'item_cat' => ':cat',
                'item_object_id' => ':objectId',
                'item_count' => ':count',
            ])
            ->setParameters([
                'setId' => $setId,
                'cat' => $cat,
                'objectId' => $objectId,
                'count' => $count,
            ])
            ->executeQuery()
            ->rowCount();
    }

    public function getItemCount(int $itemId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('item_count')
            ->from('default_items')
            ->where('item_id = :id')
            ->setParameters([
                'id' => $itemId,
            ])
            ->fetchOne();
    }

    public function updateItemCount(int $itemId, int $count): void
    {
        $this->createQueryBuilder('q')
            ->update('default_items')
            ->set('item_count', ':count')
            ->where('item_id = :id')
            ->setParameters([
                'id' => $itemId,
                'count' => $count,
            ])
            ->executeQuery();
    }

    public function removeItem(int $itemId): void
    {
        $this->createQueryBuilder('q')
            ->delete('default_items')
            ->where('item_id = :id')
            ->setParameter('id', $itemId)
            ->executeQuery();
    }
}
