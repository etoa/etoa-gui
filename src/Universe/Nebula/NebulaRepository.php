<?php

declare(strict_types=1);

namespace EtoA\Universe\Nebula;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Entity;
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
        return $this->createQueryBuilder('q')
            ->select("IDENTITY(q.entity)")
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function add(Entity $entity, int $resCrystal, bool $flush = true): void
    {
        $nebula = new Nebula();
        $nebula->setResCrystal($resCrystal);

        $entity->setNebula($nebula);

        if ($flush) {
            $this->save();
        }
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
}
