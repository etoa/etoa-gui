<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

use EtoA\Core\AbstractRepository;

class DefaultItemRepository extends AbstractRepository
{
    /**
     * @return DefaultItemSet[]
     */
    public function getSets(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('default_item_sets')
            ->orderBy('set_name')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new DefaultItemSet($row), $data);
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
            ->execute();

    }

    public function toggleSetActive(int $setId): void
    {
        $this->createQueryBuilder()
            ->update('default_item_sets')
            ->set('set_active', '!set_active')
            ->where('set_id = :id')
            ->setParameter('id', $setId)
            ->execute();

    }

    public function deleteSet(int $setId): void
    {
        $this->createQueryBuilder()
            ->delete('default_items')
            ->where('item_set_id = :id')
            ->setParameter('id', $setId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('default_item_sets')
            ->where('set_id = :id')
            ->setParameter('id', $setId)
            ->execute();
    }
}
