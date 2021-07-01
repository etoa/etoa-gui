<?php

declare(strict_types=1);

namespace EtoA\Universe\Asteroids;

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

    public function find(int $id): ?array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('asteroids')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? $data : null;
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

    public function update(
        int $id,
        int $resMetal,
        int $resCrystal,
        int $resPlastic,
        int $resFuel,
        int $resFood,
        int $resPower
    ): bool {
        $affected = (int) $this->createQueryBuilder()
            ->update('asteroids')
            ->set('res_metal', ':res_metal')
            ->set('res_crystal', ':res_crystal')
            ->set('res_plastic', ':res_plastic')
            ->set('res_fuel', ':res_fuel')
            ->set('res_food', ':res_food')
            ->set('res_power', ':res_power')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'res_metal' => $resMetal,
                'res_crystal' => $resCrystal,
                'res_plastic' => $resPlastic,
                'res_fuel' => $resFuel,
                'res_food' => $resFood,
                'res_power' => $resPower,
            ])
            ->execute();

        return $affected > 0;
    }

    public function addResources(
        int $id,
        int $resMetal,
        int $resCrystal,
        int $resPlastic,
        int $resFuel,
        int $resFood,
        int $resPower
    ): bool {
        $affected = (int) $this->createQueryBuilder()
            ->update('asteroids')
            ->set('res_metal', 'res_metal + :res_metal')
            ->set('res_crystal', 'res_crystal + :res_crystal')
            ->set('res_plastic', 'res_plastic + :res_plastic')
            ->set('res_fuel', 'res_fuel + :res_fuel')
            ->set('res_food', 'res_food + :res_food')
            ->set('res_power', 'res_power + :res_power')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'res_metal' => $resMetal,
                'res_crystal' => $resCrystal,
                'res_plastic' => $resPlastic,
                'res_fuel' => $resFuel,
                'res_food' => $resFood,
                'res_power' => $resPower,
            ])
            ->execute();

        return $affected > 0;
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
