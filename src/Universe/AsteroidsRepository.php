<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class AsteroidsRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('asteroids')
            ->execute()
            ->fetchOne();
    }

    public function add(int $id, int $resMetal, int $resCrystal, int $resPlastic): void
    {
        $this->createQueryBuilder()
            ->insert('asteroids')
            ->values([
                'id' => ':id',
                'res_metal' => ':res_metal',
                'res_crystal' => ':res_crystal',
                'res_plastic' => ':res_plastic',
            ])
            ->setParameters([
                'id' => $id,
                'res_metal' => $resMetal,
                'res_crystal' => $resCrystal,
                'res_plastic' => $resPlastic,
            ])
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('asteroids')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
