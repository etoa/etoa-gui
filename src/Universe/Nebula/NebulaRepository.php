<?php

declare(strict_types=1);

namespace EtoA\Universe\Nebula;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Nebula;
use EtoA\Entity\User;

class NebulaRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nebula::class);
    }

    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("id")
            ->from('nebulas')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    public function add(int $id, int $resCrystal): void
    {
        $this->createQueryBuilder('q')
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
        $affected = $this->createQueryBuilder('q')
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
        $affected = $this->createQueryBuilder('q')
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
        $this->createQueryBuilder('q')
            ->delete('nebulas')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}
