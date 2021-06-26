<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class EntityRepository extends AbstractRepository
{
    public function add(int $cellId, string $code, int $pos = 0): int
    {
        return (int) $this->createQueryBuilder()
            ->insert('entities')
            ->values([
                'cell_id' => ':cell_id',
                'code' => ':code',
                'pos' => ':pos'
            ])
            ->setParameters([
                'cell_id' => $cellId,
                'code' => $code,
                'pos' => $pos,
            ])
            ->execute();
    }

    public function updateCode(int $id, string $code): void
    {
        $this->createQueryBuilder()
            ->update('cells')
            ->set('code', ':code')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'code' => $code,
            ])
            ->execute();
    }
}
