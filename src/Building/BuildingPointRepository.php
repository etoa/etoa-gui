<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\AbstractRepository;

class BuildingPointRepository extends AbstractRepository
{
    /**
     * @return array<int, array<int, float>>
     */
    public function getAllMap(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('building_points')
            ->orderBy('bp_level', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        $points = array_map(fn (array $row) => new BuildingPoint($row), $data);

        $map = [];
        foreach ($points as $point) {
            $map[$point->buildingId][$point->level] = $point->points;
        }

        return $map;
    }

    public function areCalculated(): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('building_points')
            ->execute()
            ->fetchOne();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder()
            ->delete('building_points')
            ->execute();
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
