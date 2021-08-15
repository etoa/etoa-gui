<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use EtoA\Core\AbstractRepository;

class StarRepository extends AbstractRepository
{
    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select("id")
            ->from('stars')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('stars')
            ->execute()
            ->fetchOne();
    }

    public function find(int $id): ?Star
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('stars')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Star($data) : null;
    }

    public function add(int $id, int $typeId): void
    {
        $this->createQueryBuilder()
            ->insert('stars')
            ->values([
                'id' => ':id',
                'type_id' => ':type_id',
            ])
            ->setParameters([
                'id' => $id,
                'type_id' => $typeId,
            ])
            ->execute();
    }

    public function update(int $id, string $name, int $typeId = null): bool
    {
        $qb = $this->createQueryBuilder()
            ->update('stars')
            ->set('name', ':name')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'name' => $name,
            ]);

        if ($typeId !== null) {
            $qb
                ->set('type_id', ':type_id')
                ->setParameter('type_id', $typeId);
        }

        return (bool) $qb
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('stars')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
