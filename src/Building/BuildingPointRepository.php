<?php declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\BuildingPoint;

class BuildingPointRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildingPoint::class);
    }

    /**
     * @return array<int, array<int, float>>
     */
    public function getAllMap(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->orderBy('bp_level', 'ASC')
            ->getQuery()
            ->execute();

        $points = array_map(fn (array $row) => new BuildingPoint($row), $data);

        $map = [];
        foreach ($points as $point) {
            $map[$point->buildingId][$point->level] = $point->points;
        }

        return $map;
    }

    public function areCalculated(): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('1')
            ->from('building_points')
            ->fetchOne();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('q')
            ->delete('building_points')
            ->executeQuery();
    }

    /**
     * @param array<int, float> $points
     */
    public function add(int $buildingId, array $points): void
    {
        if (count($points) === 0) {
            return;
        }

        $sql = implode(',', array_fill(0, count($points), ('(?, ?, ?)')));
        $parameters = [];
        foreach ($points as $level => $point) {
            $parameters[] = $buildingId;
            $parameters[] = $level;
            $parameters[] = $point;
        }

        $this->getConnection()->executeQuery('INSERT INTO building_points (bp_building_id, bp_level, bp_points) VALUES' . $sql, $parameters);
    }
}
