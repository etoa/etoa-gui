<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceStats;

class AllianceStatsRepository extends AbstractRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceStats::class);
    }

    /**
     * @return AllianceStats[]
     */
    public function getStats(AllianceStatsSort $sort): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('alliance_stats');

        $data = $this->applySearchSortLimit($qb, null, $sort)
            ->addOrderBy('alliance_name', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => AllianceStats::createFromDbRow($row), $data);
    }

    public function add(AllianceStats $stats): void
    {
        $this->persist($stats);
        $this->save();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
