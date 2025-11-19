<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\UserStat;

class UserStatRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserStat::class);
    }

    /**
     * @param UserStatistic[] $userStats
     */
    public function addEntries(array $userStats): void
    {
        if (count($userStats) === 0) {
            return;
        }

        $parameters = [];
        foreach ($userStats as $stats) {
            $userStat = new UserStat();

            $userStat->setId($stats->user->getId());
            $userStat->setPoints($stats->points);
            $userStat->setShipPoints($stats->shipPoints);
            $userStat->setTechPoints($stats->techPoints);
            $userStat->setBuildingPoints($stats->buildingPoints);
            $userStat->setExpPoints($stats->expPoints);
            $userStat->setNick($stats->nick);
            $userStat->setAllianceTag($stats->allianceTag);
            $userStat->setAlliance($stats->alliance);
            $userStat->setRaceName($stats->raceName);
            $userStat->setSx($stats->sx);
            $userStat->setSy($stats->sy);
            $userStat->setBlocked($stats->blocked);
            $userStat->setBlocked($stats->blocked);
            $userStat->setInactive($stats->inactive);
            $userStat->setHmod($stats->hmod);
            $userStat->setRank($stats->rank);
            $userStat->setRankShips($stats->rankShips);
            $userStat->setRankTech($stats->rankTech);
            $userStat->setRankBuildings($stats->rankBuildings);
            $userStat->setRankExp($stats->rankExp);
            $userStat->setShift($stats->rankShift);
            $userStat->setShiftShips($stats->rankShiftShips);
            $userStat->setShiftTechs($stats->rankShiftTech);
            $userStat->setShiftBuildings($stats->rankShiftBuilding);
            $userStat->setShiftExp($stats->rankShiftExp);

            $this->persist($userStat);
        }

        $this->save();
    }

    /**
     * @return array{id: string, rank: string, rank_ships: string, rank_tech: string, rank_buildings: string, rank_exp: string}[]
     */
    public function getUserRanks(): array
    {
        return $this->createQueryBuilder('q')
            ->select("q.id")
            ->addSelect('q.rank')
            ->addSelect('q.shipPoints')
            ->addSelect('q.techPoints')
            ->addSelect('q.buildingPoints')
            ->addSelect('q.expPoints')
            ->getQuery()
            ->execute();
    }

    /**
     * @return UserStat[]
     */
    public function searchStats(UserStatSearch $search, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('q.id', 'q.nick', 'q.blocked', 'q.hmod', 'q.inactive', 'q.raceName', 'q.allianceTag', 'q.sx', 'q.sy', 'q.shipPoints', 'q.techPoints', 'q.buildingPoints', 'q.expPoints')
            ->addSelect($search->order . ' AS ranking')
            ->addSelect($search->field . ' AS points')
            ->addSelect($search->shift . ' AS shift');

        if (isset($search->parameters['allianceId'])) {
            $qb->innerJoin('App:User', 'users', 'WITH', 'users.id = q.id');
        }

        if ($sort == null || count($sort->sorts) === 0) {
            $qb
                ->orderBy($search->order, 'ASC')
                ->addOrderBy('q.nick', 'ASC');
        }

        return $this->applySearchSortLimit($qb, $search, $sort, $limit, $offset)
            ->getQuery()
            ->execute();
    }

    public function truncate(): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
