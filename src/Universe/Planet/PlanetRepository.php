<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Core\AbstractRepository;

class PlanetRepository extends AbstractRepository
{
    /**
     * @return Planet[]
     */
    public function getUserPlanets(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('planets')
            ->where('planet_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Planet($row), $data);
    }

    public function getPlanetUserId(int $planetId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('planet_user_id')
            ->from('planets')
            ->where('id = :planetId')
            ->setParameter('planetId', $planetId)
            ->execute()
            ->fetchOne();
    }

    public function getUserMainId(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('p.id')
            ->from('planets', 'p')
            ->where('p.planet_user_main = 1')
            ->andWhere('p.planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->execute()->fetchOne();
    }

    public function getPlanetCount(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from('planets', 'p')
            ->where('p.planet_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])->execute()->fetchOne();
    }

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

    public function find(int $id): ?Planet
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('planets')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Planet($data) : null;
    }

    public function getRandomFreePlanetId(int $sx = 0, int $sy = 0, ?int $minFields = null, ?int $planetType = null, ?int $starType = null): ?int
    {
        $qry = $this->createQueryBuilder()
            ->select('p.id')
            ->from('planets', 'p')
            ->where('p.planet_user_id = 0')
            ->innerJoin('p', 'planet_types', 't', 'p.planet_type_id = t.type_id AND t.type_habitable = 1')
            ->innerJoin('p', 'entities', 'e', 'p.id = e.id')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id')
            ->orderBy('RAND()')
            ->setMaxResults(1);

        if ($sx > 0) {
            $qry->andWhere('c.sx = :sx')
                ->setParameter('sx', $sx);
        }

        if ($sy > 0) {
            $qry->andWhere('c.sy = :sy')
                ->setParameter('sy', $sy);
        }

        if ($planetType > 0) {
            $qry->andWhere('p.planet_type_id = :planetType')
                ->setParameter('planetType', $planetType);
        }

        if ($minFields > 0) {
            $qry->andWhere('p.planet_fields > :minFields')
                ->setParameter('minFields', $minFields);
        }

        if ($starType > 0) {
            $qry->andWhere('e.cell_id = any (
                    select cell_id FROM entities WHERE id = any (
                        select id from stars where type_id = :starType
                    )
                )')
                ->setParameter('starType', $starType);
        }

        $data = $qry
            ->execute()
            ->fetchOne();

        return $data !== false ? (int) $data : null;
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
                'temp_to' => $tempTo,
            ])
            ->execute();
    }

    public function update(
        int $id,
        int $typeId,
        string $name,
        int $fields,
        int $extraFields,
        string $image,
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
        ?string $description
    ): bool {
        $affected = (int) $this->createQueryBuilder()
            ->update('planets')
            ->set('planet_type_id', ':type_id')
            ->set('planet_name', ':name')
            ->set('planet_fields', ':fields')
            ->set('planet_fields_extra', ':extra_fields')
            ->set('planet_image', ':image')
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
                'image' => $image,
                'temp_from' => $tempFrom,
                'temp_to' => $tempTo,
                'res_metal' => $resMetal,
                'res_crystal' => $resCrystal,
                'res_plastic' => $resPlastic,
                'res_fuel' => $resFuel,
                'res_food' => $resFood,
                'wf_metal' => $wfMetal,
                'wf_crystal' => $wfCrystal,
                'wf_plastic' => $wfPlastic,
                'people' => $people,
                'description' => $description !== null && strlen($description) > 0 ? $description : null,
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
            ->update('planets')
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

    public function resetUserChanged(int $id): void
    {
        $this->createQueryBuilder()
            ->update('planets')
            ->set('planet_user_changed', (string) 0)
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute();
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