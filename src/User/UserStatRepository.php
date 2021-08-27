<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserStatRepository extends AbstractRepository
{
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
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('user_stats')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return array{id: int, rank: int, rank_ships: int, rank_tech: int, rank_buildings: int, rank_exp: int}[]
     */
    public function getUserRanks(): array
    {
        $data = $this->getConnection()->fetchAllAssociative("
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

        return array_map(fn (array $row) => array_map(fn ($value) => (int) $value, $row), $data);
    }

    /**
     * @return UserStat[]
     */
    public function searchStats(UserStatSearch $search, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('id', 'nick', 'blocked', 'hmod', 'inactive', 'race_name', 'alliance_tag', 'sx', 'sy', 'points_ships', 'points_tech', 'points_buildings', 'points_exp')
            ->addSelect($search->order . ' AS rank')
            ->addSelect($search->field . ' AS points')
            ->addSelect($search->shift . ' AS shift')
            ->from('user_stats');

        if (isset($search->parameters['allianceId'])) {
            $qb->innerJoin('user_stats', 'users', 'users', 'users.user_id = user_stats.id');
        }

        if ($sort == null || count($sort->sorts) === 0) {
            $qb
                ->orderBy($search->order, 'ASC')
                ->addOrderBy('nick', 'ASC');
        }

        $data = $this->applySearchSortLimit($qb, $search, $sort, $limit, $offset)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserStat($row), $data);
    }

    public function truncate(): void
    {
        $this->getConnection()
            ->executeStatement("TRUNCATE TABLE user_stats;");
    }
}
