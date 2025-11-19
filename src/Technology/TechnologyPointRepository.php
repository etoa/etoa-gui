<?php declare(strict_types=1);

namespace EtoA\Technology;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\TechnologyPoint;

class TechnologyPointRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechnologyPoint::class);
    }

    /**
     * @return array<int, array<int, float>>
     */
    public function getAllMap(): array
    {

        $points = $this->createQueryBuilder('q')
            ->orderBy('q.level', 'ASC')
            ->getQuery()
            ->execute();

        $map = [];
        foreach ($points as $point) {
            $map[$point->getTechnology()->getId()][$point->getLevel()] = $point->getPoints();
        }

        return $map;
    }

    public function areCalculated(): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('1')
            ->getQuery()
            ->execute();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->getQuery()
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
