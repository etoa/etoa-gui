<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\PlanetType;

class PlanetTypeRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanetType::class);
    }

    /**
     * Returns an array of planet types names indexed by their id.
     *
     * @return array<int, string>
     */
    public function getPlanetTypeNames(bool $showAll = false): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('p.type_id, p.type_name')
            ->from('planet_types', 'p');

        if (!$showAll) {
            $qb
                ->andWhere('p.type_consider = 1')
                ->andWhere('p.type_habitable = 1');
        }

        return $qb
            ->orderBy('p.type_name')
            ->fetchAllKeyValue();
    }

    /**
     * @return PlanetType[]
     */
    public function getPlanetTypes(string $order = 'type_name', string $sort = 'ASC'): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('p.*')
            ->from('planet_types', 'p')
            ->andWhere('p.type_consider = 1')
            ->orderBy('p.' . $order, $sort)
            ->fetchAllAssociative();

        return array_map(fn ($row) => new PlanetType($row), $data);
    }

    public function isHabitable(int $typeId): bool
    {
        $type = $this->find($typeId);

        return $type !== null && $type->isHabitable();
    }

    public function getName(int $id): ?string
    {
        $data = $this->createQueryBuilder('q')
            ->select('type_name')
            ->from('planet_types')
            ->where('type_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchOne();

        return $data !== false ? $data : null;
    }

    public function get(int $id): ?PlanetType
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('planet_types')
            ->where('type_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchAssociative();

        return $data !== false ? new PlanetType($data) : null;
    }

    /**
     * @return array<int, array{name: string, cnt: string}>
     */
    public function getNumberOfOwnedPlanetsByType(): array
    {
        return $this->getConnection()
            ->fetchAllAssociative(
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
            );
    }
}
