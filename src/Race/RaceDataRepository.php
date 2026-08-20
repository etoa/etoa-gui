<?php declare(strict_types=1);

namespace EtoA\Race;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Race;

class RaceDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Race::class);
    }

    /**
     * Returns an array of race names indexed by the race id.
     *
     * @return array<int, string>
     */
    public function getRaceNames(bool $showAll = false, bool $orderById = false): array
    {
        $constraints = $showAll ? []:['active'=>1];
        $order = $orderById ? ['id'=>'ASC']:['name'=>'ASC'];

        return $this->findBy($constraints,$order);
    }

    /**
     * Returns an array of race leader titles indexed by the race id.
     *
     * @return array<int, string>
     */
    public function getRaceLeaderTitles(): array
    {
        return $this->createQueryBuilder('q')
            ->select('r.race_id, r.race_leadertitle')
            ->from('races', 'r')
            ->andWhere('r.race_active = 1')
            ->orderBy('r.race_name')
            ->fetchAllKeyValue();
    }

    public function getRace(int $raceId): ?Race
    {
        return $this->findOneBy(['id'=>$raceId,'active'=>true]);
    }

    /**
     * @return Race[]
     */
    public function getActiveRaces(string $order = 'name', string $sort = 'ASC'): array
    {
        return $this->findBy(['active'=>1],[$order=>$sort]);
    }

    /**
     * @return array<int, array{name: string, cnt: string}>
     */
    public function getNumberOfRacesByType(): array
    {
        return $this->getConnection()
            ->fetchAllAssociative(
                "SELECT
                    races.race_name as name,
                    COUNT(users.user_race_id) as cnt
                FROM
                    users
                INNER JOIN
                    races
                ON
                    users.user_race_id = races.race_id
                    AND users.user_ghost = 0
                    AND users.user_hmode_from = 0
                    AND users.user_hmode_to = 0
                GROUP BY
                    races.race_id
                ORDER BY
                    cnt DESC;"
            );
    }

}
