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
            $parameters[] = $stats->userId;
            $parameters[] = $stats->points;
            $parameters[] = $stats->shipPoints;
            $parameters[] = $stats->techPoints;
            $parameters[] = $stats->buildingPoints;
            $parameters[] = $stats->expPoints;
            $parameters[] = $stats->nick;
            $parameters[] = $stats->allianceTag ?? '';
            $parameters[] = $stats->allianceId;
            $parameters[] = $stats->raceName ?? '';
            $parameters[] = $stats->sx;
            $parameters[] = $stats->sy;
            $parameters[] = $stats->blocked ? 1 : 0;
            $parameters[] = $stats->inactive ? 1 : 0;
            $parameters[] = $stats->hmod ? 1 : 0;
            $parameters[] = $stats->rank;
            $parameters[] = $stats->rankShips;
            $parameters[] = $stats->rankTech;
            $parameters[] = $stats->rankBuildings;
            $parameters[] = $stats->rankExp;
            $parameters[] = $stats->rankShift;
            $parameters[] = $stats->rankShiftShips;
            $parameters[] = $stats->rankShiftTech;
            $parameters[] = $stats->rankShiftBuilding;
            $parameters[] = $stats->rankShiftExp;
        }

        $insertRow = implode(',', array_fill(0, count($userStats), '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'));

        $this->getConnection()->executeQuery('
            INSERT INTO user_stats (
                id,
                points,
                points_ships,
                points_tech,
                points_buildings,
                points_exp,
                nick,
                alliance_tag,
                alliance_id,
                race_name,
                sx,
                sy,
                blocked,
                inactive,
                hmod,
                rank,
                rank_ships,
                rank_tech,
                rank_buildings,
                rank_exp,
                rankshift,
                rankshift_ships,
                rankshift_tech,
                rankshift_buildings,
                rankshift_exp
            ) VALUES ' . $insertRow, $parameters);
    }

    /**
     * @return array{id: string, rank: string, rank_ships: string, rank_tech: string, rank_buildings: string, rank_exp: string}[]
     */
    public function getUserRanks(): array
    {
        return $this->getConnection()->fetchAllAssociative("
            SELECT
                id,
                rank,
                rank_ships,
                rank_tech,
                rank_buildings,
                rank_exp
            FROM
                user_stats;
        ");
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
        $this->getConnection()
            ->executeStatement("TRUNCATE TABLE user_stats;");
    }
}
