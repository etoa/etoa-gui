<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

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

    /**
     * @return array<Entity>
     */
    public function findRandomByCodes(array $codes, int $limit): array
    {
        if (count($codes) == 0) {
            return [];
        }

        $data = $this->createQueryBuilder()
            ->select(
                'e.id',
                'c.id as cid',
                'code',
                'pos',
                'sx',
                'sy',
                'cx',
                'cy'
            )
            ->from('entities', 'e')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id')
            ->where('code IN (' . implode(',', array_fill(0, count($codes), '?')) . ')')
            ->andWhere('pos = 0')
            ->orderBy('RAND()')
            ->setParameters(array_values($codes))
            ->setMaxResults($limit)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new Entity($arr), $data);
    }

    public function findIncludeCell(int $id): ?Entity
    {
        $data = $this->createQueryBuilder()
            ->select(
                'e.id',
                'c.id as cid',
                'code',
                'pos',
                'sx',
                'sy',
                'cx',
                'cy'
            )
            ->from('entities', 'e')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id')
            ->where('e.id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Entity($data) : null;
    }

    public function findByCellAndPosition(int $cellId, int $position): ?Entity
    {
        $data = $this->createQueryBuilder()
            ->select(
                'e.id',
                'c.id as cid',
                'code',
                'pos',
                'sx',
                'sy',
                'cx',
                'cy'
            )
            ->from('entities', 'e')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id')
            ->where('e.cell_id = :cellId')
            ->andWhere('e.pos = :position')
            ->setParameters([
                'cellId' => $cellId,
                'position' => $position,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Entity($data) : null;
    }

    /**
     * @return array<Entity>
     */
    public function findAllIncludeCell(?int $pos = null): array
    {
        $qry = $this->createQueryBuilder()
            ->select(
                'e.id',
                'e.code',
                'e.pos',
                'c.id as cid',
                'c.sx',
                'c.cx',
                'c.sy',
                'c.cy'
            )
            ->from('entities', 'e')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id');

        if ($pos !== null) {
            $qry->where('pos = :pos')
                ->setParameter('pos', $pos);
        }

        $data = $qry->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new Entity($arr), $data);
    }

    public function add(int $cellId, string $code, int $pos = 0): int
    {
        $this->createQueryBuilder()
            ->insert('entities')
            ->values([
                'cell_id' => ':cell_id',
                'code' => ':code',
                'pos' => ':pos',
            ])
            ->setParameters([
                'cell_id' => $cellId,
                'code' => $code,
                'pos' => $pos,
            ])
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function updateCode(int $id, string $code): void
    {
        $this->createQueryBuilder()
            ->update('entities')
            ->set('code', ':code')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'code' => $code,
            ])
            ->execute();
    }
}
