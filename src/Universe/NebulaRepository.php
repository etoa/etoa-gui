<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class NebulaRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('nebulas')
            ->execute()
            ->fetchOne();
    }

    public function add(int $id, int $resCrystal): void
    {
        $this->createQueryBuilder()
            ->insert('nebulas')
            ->values([
                'id' => ':id',
                'res_crystal' => ':res_crystal',
            ])
            ->setParameters([
                'id' => $id,
                'res_crystal' => $resCrystal,
            ])
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('nebulas')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
