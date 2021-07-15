<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;

class TechnologyPointRepository extends AbstractRepository
{
    /**
     * @return array<int, array<int, float>>
     */
    public function getAllMap(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('tech_points')
            ->orderBy('bp_level', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        $points = array_map(fn (array $row) => new TechnologyPoint($row), $data);

        $map = [];
        foreach ($points as $point) {
            $map[$point->technologyId][$point->level] = $point->points;
        }

        return $map;
    }

    public function areCalculated(): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('tech_points')
            ->execute()
            ->fetchOne();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder()
            ->delete('tech_points')
            ->execute();
    }

    /**
     * @param array<int, float> $points
     */
    public function add(int $technologyId, array $points): void
    {
        if (count($points) === 0) {
            return;
        }

        $sql = implode(',', array_fill(0, count($points), ('(?, ?, ?)')));
        $parameters = [];
        foreach ($points as $level => $point) {
            $parameters[] = $technologyId;
            $parameters[] = $level;
            $parameters[] = $point;
        }

        $this->getConnection()->executeQuery('INSERT INTO tech_points (bp_tech_id, bp_level, bp_points) VALUES' . $sql, $parameters);
    }
}
