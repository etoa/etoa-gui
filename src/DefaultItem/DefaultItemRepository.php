<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

use EtoA\Core\AbstractRepository;

class DefaultItemRepository extends AbstractRepository
{
    /**
     * @return DefaultItemSet[]
     */
    public function getSets(bool $activeOnly = true): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('default_item_sets')
            ->orderBy('set_name');

        if ($activeOnly) {
            $qb->where('set_active = true');
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new DefaultItemSet($row), $data);
    }

    public function getItem(int $itemId): ?DefaultItem
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('default_items')
            ->where('item_id = :id')
            ->setParameter('id', $itemId)
           ->fetchAssociative();

        return $data !== false ? DefaultItem::createFromData($data) : null;
    }

    public function getItemNames(): array
    {
        $qb = $this->createQueryBuilder()
            ->select('set_id, set_name')
            ->from('default_item_sets')
            ->andWhere('set_active = 1');

        return $qb
            ->orderBy('set_name')
            ->fetchAllKeyValue();
    }

    public function createSet(string $name): void
    {
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
            ->update('default_item_sets')
            ->set('set_active', '!set_active')
            ->where('set_id = :id')
            ->setParameter('id', $setId)
            ->executeQuery();
    }

    public function deleteSet(int $setId): void
    {
        $this->createQueryBuilder()
            ->delete('default_items')
            ->where('item_set_id = :id')
            ->setParameter('id', $setId)
            ->executeQuery();

        $this->createQueryBuilder()
            ->delete('default_item_sets')
            ->where('set_id = :id')
            ->setParameter('id', $setId)
            ->executeQuery();
    }

    /**
     * @return array<string, array<DefaultItem>>
     */
    public function getItemsGroupedByCategory(int $setId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('default_items')
            ->where('item_set_id = :id')
            ->setParameters([
                'id' => $setId,
            ])
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $result[$row['item_cat']][] = DefaultItem::createFromData($row);
        }

        return $result;
    }

    public function addItemToSet(int $setId, string $cat, int $objectId, int $count): bool
    {
        $exists = (bool) $this->createQueryBuilder()
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

        return (bool) $this->createQueryBuilder()
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
        return (int) $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
            ->delete('default_items')
            ->where('item_id = :id')
            ->setParameter('id', $itemId)
            ->executeQuery();
    }
}
