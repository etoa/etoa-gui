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
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('stars')
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
            ->fetchAssociative();

        return $data !== false ? new Star($data) : null;
    }

    public function findStarForCell(int $cellId): ?Star
    {
        $data = $this->createQueryBuilder()
            ->select('s.*')
            ->from('stars', 's')
            ->innerJoin('s', 'entities', 'e', 'e.id = s.id')
            ->where('e.cell_id = :cellId')
            ->andWhere('e.pos = 0')
            ->setParameters([
                'cellId' => $cellId,
            ])
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
            ->executeQuery();
    }

    public function update(int $id, ?string $name, int $typeId = null): bool
    {
        $qb = $this->createQueryBuilder()
            ->update('stars')
            ->set('name', ':name')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'name' => stripBBCode((string) $name),
            ]);

        if ($typeId !== null) {
            $qb
                ->set('type_id', ':type_id')
                ->setParameter('type_id', $typeId);
        }

        return (bool) $qb
            ->executeQuery()
            ->rowCount();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('stars')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}
