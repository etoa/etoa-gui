<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Core\AbstractRepository;

class PlanetTypeRepository extends AbstractRepository
{
    /**
     * Returns an array of planet types names indexed by their id.
     *
     * @return array<int, string>
     */
    public function getPlanetTypeNames(bool $showAll = false): array
    {
        $qb = $this->createQueryBuilder()
            ->select('p.type_id, p.type_name')
            ->from('planet_types', 'p');

        if (!$showAll) {
            $qb
                ->andWhere('p.type_consider = 1')
                ->andWhere('p.type_habitable = 1');
        }

        return $qb
            ->orderBy('p.type_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return PlanetType[]
     */
    public function getPlanetTypes(string $order = 'type_name', string $sort = 'ASC'): array
    {
        $data = $this->createQueryBuilder()
            ->select('p.*')
            ->from('planet_types', 'p')
            ->andWhere('p.type_consider = 1')
            ->orderBy('p.' . $order, $sort)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new PlanetType($row), $data);
    }

    public function find(int $id): ?PlanetType
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('planet_types')
            ->where('type_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new PlanetType($data) : null;
    }

    public function isHabitable(int $typeId): bool
    {
        $type = $this->find($typeId);

        return $type !== null && $type->habitable;
    }

    public function getName(int $id): ?string
    {
        $data = $this->createQueryBuilder()
            ->select('type_name')
            ->from('planet_types')
            ->where('type_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function get(int $id): ?PlanetType
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('planet_types')
            ->where('type_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new PlanetType($data) : null;
    }

    /**
     * @return array<int, array{name: string, cnt: int}>
     */
    public function getNumberOfOwnedPlanetsByType(): array
    {
        $data = $this->getConnection()
            ->executeQuery(
                "SELECT
                    planet_types.type_name as name,
                    COUNT(planets.planet_type_id) as cnt
                FROM
                    planet_types
                INNER JOIN
                    (
                        planets
                    INNER JOIN
                        users
                    ON
                        planet_user_id = user_id
                        AND user_ghost = 0
                        AND user_hmode_from = 0
                        AND user_hmode_to = 0
                    )
                ON
                    planet_type_id = type_id
                GROUP BY
                    planet_types.type_id
                ORDER BY
                    cnt DESC;"
            )
            ->fetchAllAssociative();

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'cnt' => (int) $arr['cnt'],
        ], $data);
    }
}
