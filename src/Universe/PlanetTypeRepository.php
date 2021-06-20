<?php declare(strict_types=1);

namespace EtoA\Universe;

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

        return array_map(fn($row) => new PlanetType($row), $data);
    }
}
