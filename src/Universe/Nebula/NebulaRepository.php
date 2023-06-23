<?php

declare(strict_types=1);

namespace EtoA\Universe\Nebula;

use EtoA\Core\AbstractRepository;

class NebulaRepository extends AbstractRepository
{
    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select("id")
            ->from('nebulas')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('nebulas')
            ->fetchOne();
    }

    public function find(int $id): ?Nebula
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('nebulas')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchAssociative();

        return $data !== false ? new Nebula($data) : null;
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
            ->executeQuery();
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
        $affected = $this->createQueryBuilder()
            ->update('nebulas')
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
            ->executeQuery()
            ->rowCount();

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
        $affected = $this->createQueryBuilder()
            ->update('nebulas')
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
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('nebulas')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}
