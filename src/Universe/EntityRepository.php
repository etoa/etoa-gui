<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class EntityRepository extends AbstractRepository
{
    public function findRandomId(string $code): ?int
    {
        $id = $this->createQueryBuilder()
            ->select('id')
            ->from('entities')
            ->where('code = :code')
            ->orderBy('RAND()')
            ->setParameters([
                'code' => $code,
            ])
            ->execute()
            ->fetchOne();
        return $id !== false ? (int) $id : null;
    }

    public function findRandomByCodes(array $codes, int $limit): ?array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('entities')
            ->where('code IN ('. implode(',', array_fill(0, count($codes), '?')).')')
            ->andWhere('post = 0')
            ->orderBy('RAND()')
            ->setParameters(array_values($codes))
            ->setMaxResults($limit)
            ->execute()
            ->fetchAllAssociative();
        return $data !== false ? $data : null;
    }

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
