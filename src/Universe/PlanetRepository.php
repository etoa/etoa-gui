<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class PlanetRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('planets')
            ->execute()
            ->fetchOne();
    }

    public function countWithUser(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('planets')
            ->where('planet_user_id > 0')
            ->execute()
            ->fetchOne();
    }

    public function find(int $id): ?array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('planets')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute();
        return $data !== false ? $data : null;
    }

    public function add(
        int $id,
        int $typeId,
        int $fields,
        string $image,
        int $tempFrom,
        int $tempTo
    ): void {
        $this->createQueryBuilder()
            ->insert('planets')
            ->values([
                'id' => ':id',
                'planet_type_id' => ':type_id',
                'planet_fields' => ':fields',
                'planet_image' => ':image',
                'planet_temp_from' => ':temp_from',
                'planet_temp_to' => ':temp_to',
            ])
            ->setParameters([
                'id' => $id,
                'type_id' => $typeId,
                'fields' => $fields,
                'image' => $image,
                'temp_from' => $tempFrom,
                'temp_from' => $tempTo,
            ])
            ->execute();
    }

    public function update(
        int $id,
        int $typeId,
        string $name,
        int $fields,
        int $extraFields,
        int $tempFrom,
        int $tempTo,
        int $resMetal,
        int $resCrystal,
        int $resPlastic,
        int $resFuel,
        int $resFood,
        int $wfMetal,
        int $wfCrystal,
        int $wfPlastic,
        int $people,
        string $description
    ): bool {
        $affected = (int) $this->createQueryBuilder()
            ->update('planets')
            ->set('planet_type_id', ':type_id')
            ->set('planet_name', ':name')
            ->set('planet_fields', ':fields')
            ->set('planet_fields_extra', ':extra_fields')
            ->set('planet_temp_from', ':temp_from')
            ->set('planet_temp_to', ':temp_to')
            ->set('planet_res_metal', ':res_metal')
            ->set('planet_res_crystal', ':res_crystal')
            ->set('planet_res_plastic', ':res_plastic')
            ->set('planet_res_fuel', ':res_fuel')
            ->set('planet_res_food', ':res_food')
            ->set('planet_wf_metal', ':wf_metal')
            ->set('planet_wf_crystal', ':wf_crystal')
            ->set('planet_wf_plastic', ':wf_plastic')
            ->set('planet_people', ':people')
            ->set('planet_desc', ':description')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'type_id' => $typeId,
                'name' => $name,
                'fields' => $fields,
                'extra_fields' => $extraFields,
                'temp_from' => $tempFrom,
                'temp_from' => $tempTo,
                'res_metal' => $resMetal,
                'res_crystal' => $resCrystal,
                'res_plastic' => $resPlastic,
                'res_fuel' => $resFuel,
                'res_food' => $resFood,
                'wf_metal' => $wfMetal,
                'wf_crystal' => $wfCrystal,
                'wf_plastic' => $wfPlastic,
                'people' => $people,
                'description' => $description,
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
        int $people
    ): bool {
        $affected = (int) $this->createQueryBuilder()
            ->update('asteroids')
            ->set('planet_res_metal', 'planet_res_metal + :res_metal')
            ->set('planet_res_crystal', 'planet_res_crystal + :res_crystal')
            ->set('planet_res_plastic', 'planet_res_plastic + :res_plastic')
            ->set('planet_res_fuel', 'planet_res_fuel + :res_fuel')
            ->set('planet_res_food', 'planet_res_food + :res_food')
            ->set('planet_people', 'planet_people + :people')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'res_metal' => $resMetal,
                'res_crystal' => $resCrystal,
                'res_plastic' => $resPlastic,
                'res_fuel' => $resFuel,
                'res_food' => $resFood,
                'people' => $people,
            ])
            ->execute();
        return $affected > 0;
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('planets')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
