<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceRepository extends AbstractRepository
{
    /**
     * @return AllianceMember[]
     */
    public function getAllianceMembers(int $allianceId): array
    {
        $data = $this->getConnection()->fetchAllAssociative('
            SELECT u.user_id, u.user_points, u.user_nick, u.user_alliance_rank_id, p.id as planetId, x.time_action AS last_log, s.time_action, r.race_name
            FROM users u
            INNER JOIN planets p ON p.planet_user_id = u.user_id AND p.planet_user_main = 1
            INNER JOIN races r ON r.race_id = u.user_race_id
            LEFT JOIN user_sessions s ON s.user_id = u.user_id
            LEFT JOIN (
                SELECT user_id, MAX(time_action) as time_action FROM user_sessionlog GROUP BY user_id
            ) x ON x.user_id = u.user_id
            WHERE u.user_alliance_id = :allianceId
            ORDER BY u.user_points DESC, u.user_nick
        ', [
            'allianceId' => $allianceId,
        ]);

        return array_map(fn (array $row) => new AllianceMember($row), $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('alliances')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return array<int, string>
     */
    public function getAllianceNames(AllianceSearch $search = null, int $limit = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit)
            ->select("alliance_id, alliance_name")
            ->from('alliances')
            ->orderBy('alliance_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return array<int, string>
     */
    public function getAllianceTags(): array
    {
        return $this->createQueryBuilder()
            ->select("alliance_id, alliance_tag")
            ->from('alliances')
            ->orderBy('alliance_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return array<int, string>
     */
    public function getAllianceNamesWithTags(AllianceSearch $search = null): array
    {
        $rows = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select("alliance_id, alliance_name, alliance_tag")
            ->from('alliances')
            ->orderBy('alliance_name')
            ->addOrderBy('alliance_tag')
            ->execute()
            ->fetchAllAssociative();

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row['alliance_id']] = sprintf('[%s] %s', $row['alliance_tag'], $row['alliance_name']);
        }

        return $result;
    }

    /**
     * @return Alliance[]
     */
    public function getAlliances(): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('alliances')
            ->orderBy('alliance_name')
            ->addOrderBy('alliance_tag')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Alliance($row), $data);
    }

    /**
     * @return AllianceWithMemberCount[]
     */
    public function getAlliancesAcceptingApplications(): array
    {
        $data = $this->createQueryBuilder()
            ->select("a.*")
            ->addSelect('COUNT(u.user_id) as member_count')
            ->from('alliances', 'a')
            ->leftJoin('a', 'users', 'u', 'u.user_alliance_id=a.alliance_id')
            ->where('a.alliance_accept_applications = 1')
            ->groupBy('a.alliance_id')
            ->orderBy('a.alliance_name')
            ->addOrderBy('a.alliance_tag')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceWithMemberCount($row), $data);
    }

    public function getAlliance(int $allianceId): ?AllianceWithMemberCount
    {
        if ($allianceId === 0) {
            return null;
        }

        $data = $this->createQueryBuilder()
            ->select("a.*")
            ->addSelect('COUNT(u.user_id) as member_count')
            ->from('alliances', 'a')
            ->leftJoin('a', 'users', 'u', 'u.user_alliance_id=a.alliance_id')
            ->where('a.alliance_id = :id')
            ->setParameter('id', $allianceId)
            ->groupBy('a.alliance_id')
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new AllianceWithMemberCount($data) : null;
    }

    public function getFounderId(int $allianceId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('alliance_founder_id')
            ->from('alliances')
            ->where('alliance_id = :id')
            ->setParameter('id', $allianceId)
            ->execute()
            ->fetchOne();
    }
    public function setFounderId(int $allianceId, int $founder): void
    {
        $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_founder_id', ':founder')
            ->where('alliance_id = :id')
            ->setParameters([
                'id' => $allianceId,
                'founder' => $founder,
            ])
            ->execute();
    }

    /**
     * @param array<string, int|string|bool> $formData
     * @return array<array{alliance_id: string, alliance_name: string, alliance_tag:string, alliance_foundation_date:string, alliance_founder_id: string, cnt:string}>
     */
    public function findByFormData(array $formData): array
    {
        $qry = $this->createQueryBuilder()
            ->select(
                'alliance_id',
                'alliance_name',
                'alliance_tag',
                'alliance_foundation_date',
                'alliance_founder_id',
                'COUNT(u.user_id) AS cnt'
            )
            ->from('alliances', 'a')
            ->leftJoin('a', 'users', 'u', 'u.user_alliance_id = a.alliance_id')
            ->groupBy('alliance_id')
            ->orderBy('alliance_tag');

        if ($formData['alliance_id'] != "") {
            $qry->andWhere('alliance_id = :alliance_id')
                ->setParameter('alliance_id', $formData['alliance_id']);
        }
        if ($formData['alliance_tag'] != "") {
            $qry = fieldComparisonQuery($qry, $formData, 'alliance_tag', 'alliance_tag');
        }
        if ($formData['alliance_name'] != "") {
            $qry = fieldComparisonQuery($qry, $formData, 'alliance_name', 'alliance_name');
        }
        if ($formData['alliance_text'] != "") {
            $qry = fieldComparisonQuery($qry, $formData, 'alliance_text', 'alliance_text');
        }

        return $qry->execute()
            ->fetchAllAssociative();
    }

    public function exists(string $tag, string $name, int $ignoreAllianceId = null): bool
    {
        $qb = $this->createQueryBuilder()
            ->select('alliance_id')
            ->from('alliances')
            ->where('alliance_tag = :tag OR alliance_name = :name')
            ->setParameters([
                'name' => $name,
                'tag' => $tag,
            ]);

        if ($ignoreAllianceId !== null) {
            $qb
                ->andWhere('alliance_id <> :allianceId')
                ->setParameter(':allianceId', $ignoreAllianceId);
        }

        return (bool) $qb
            ->setMaxResults(1)
            ->execute()
            ->fetchOne();
    }

    public function create(string $tag, string $name, int $founderId): int
    {
        $this->createQueryBuilder()
            ->insert('alliances')
            ->values([
                'alliance_tag' => ':tag',
                'alliance_name' => ':name',
                'alliance_founder_id' => ':founder',
                'alliance_foundation_date' => time(),
                'alliance_public_memberlist' => 1,
            ])
            ->setParameters([
                'name' => $name,
                'tag' => $tag,
                'founder' => $founderId,
            ])->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function updateApplicationText(int $allianceId, string $template): void
    {
        $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_application_template', ':template')
            ->where('alliance_id = :id')
            ->setParameters([
                'template' => $template,
                'allianceId' => $allianceId,
            ])
            ->execute();
    }

    public function update(int $id, string $tag, string $name, string $text, string $template, string $url, int $founder, string $updatedAllianceImage = null, bool $acceptsApplications = null, bool $acceptsBnd = null, bool $publicMemberList = null): bool
    {
        $qb = $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_name', ':name')
            ->set('alliance_tag', ':tag')
            ->set('alliance_text', ':text')
            ->set('alliance_application_template', ':template')
            ->set('alliance_url', ':url')
            ->set('alliance_founder_id', ':founder')
            ->where('alliance_id = :id')
            ->setParameters([
                'id' => $id,
                'name' => $name,
                'tag' => $tag,
                'text' => $text,
                'template' => $template,
                'url' => $url,
                'founder' => $founder,
            ]);

        if ($updatedAllianceImage !== null) {
            $qb
                ->set('alliance_img', ':allianceImage')
                ->set('alliance_img_check', ':imageCheck')
                ->setParameter('allianceImage', $updatedAllianceImage)
                ->setParameter('imageCheck', $updatedAllianceImage !== '' ? 1 : 0);
        }

        if ($acceptsBnd !== null) {
            $qb
                ->set('alliance_accept_bnd', ':acceptsBnd')
                ->setParameter('acceptsBnd', (int) $acceptsBnd);
        }

        if ($acceptsApplications !== null) {
            $qb
                ->set('alliance_accept_applications', ':acceptsApplications')
                ->setParameter('acceptsApplications', (int) $acceptsApplications);
        }

        if ($publicMemberList !== null) {
            $qb
                ->set('alliance_public_memberlist', ':publicMemberList')
                ->setParameter('publicMemberList', (int) $publicMemberList);
        }

        $affected = $qb
            ->execute();

        return (int) $affected > 0;
    }

    public function remove(int $id): bool
    {
        $affected = (int) $this->createQueryBuilder()
            ->delete('alliances')
            ->where('alliance_id = :id')
            ->setParameter('id', $id)
            ->execute();

        return $affected > 0;
    }

    public function getPicture(int $allianceId): ?string
    {
        return $this->createQueryBuilder()
            ->select('alliance_img')
            ->from('alliances')
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchOne();
    }

    public function clearPicture(int $allianceId): bool
    {
        $affected = $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_img', '')
            ->set('alliance_img_check', (string) 0)
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();

        return (int) $affected > 0;
    }

    public function markPictureChecked(int $allianceId): bool
    {
        $affected = $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_img_check', (string) 0)
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();

        return (int) $affected > 0;
    }

    /**
     * @return array<array{alliance_id: string, alliance_tag: string, alliance_name: string, alliance_img: string}>
     */
    public function findAllWithUncheckedPictures(): array
    {
        return $this->createQueryBuilder()
            ->select(
                'alliance_id',
                'alliance_tag',
                'alliance_name',
                'alliance_img'
            )
            ->from('alliances')
            ->where('alliance_img_check = 1')
            ->andWhere("alliance_img != ''")
            ->execute()
            ->fetchAllAssociative();
    }

    /**
     * @return array<array{alliance_id: string, alliance_tag: string, alliance_name: string, alliance_img: string}>
     */
    public function findAllWithPictures(): array
    {
        return $this->createQueryBuilder()
            ->select(
                'alliance_id',
                'alliance_tag',
                'alliance_name',
                'alliance_img'
            )
            ->from('alliances')
            ->where("alliance_img != ''")
            ->execute()
            ->fetchAllAssociative();
    }

    /**
     * @return array<array{alliance_id: string, alliance_name: string, alliance_tag: string}>
     */
    public function findAllWithoutFounder(): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    alliance_id,
                    alliance_name,
                    alliance_tag
                FROM alliances a
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM users u
                    WHERE a.alliance_founder_id = u.user_id
                );"
            )
            ->fetchAllAssociative();
    }

    /**
     * @return array<array{alliance_id: string, alliance_name: string, alliance_tag: string}>
     */
    public function findAllWithoutUsers(): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    alliance_id,
                    alliance_name,
                    alliance_tag
                FROM alliances a
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM users u
                    WHERE a.alliance_id = u.user_alliance_id
                );"
            )
            ->fetchAllAssociative();
    }

    public function countUsers(int $allianceId): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('users')
            ->where('user_alliance_id = :id')
            ->setParameter('id', $allianceId)
            ->execute()
            ->fetchOne();
    }

    /**
     * @return array<array{user_id: string, user_nick: string, user_points: string, user_alliance_rank_id: string}>
     */
    public function findUsers(int $allianceId): array
    {
        return $this->createQueryBuilder()
            ->select(
                'user_id',
                'user_nick',
                'user_points',
                'user_alliance_rank_id'
            )
            ->from('users')
            ->where('user_alliance_id = :allianceId')
            ->orderBy('user_points', 'DESC')
            ->addOrderBy('user_nick')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchAllAssociative();
    }

    public function assignRankToUser(int $rankId, int $userId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_alliance_rank_id', ':rank')
            ->where('user_id = :user')
            ->setParameters([
                'rank' => $rankId,
                'user' => $userId,
            ])
            ->execute();
    }

    public function removeUser(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('users')
            ->set('user_alliance_id', (string) 0)
            ->set('user_alliance_rank_id', (string) 0)
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    /**
     * @return array<int, string>
     */
    public function listSoloUsers(): array
    {
        return $this->createQueryBuilder()
            ->select("user_id", "user_nick")
            ->from('users')
            ->where('user_alliance_id = 0')
            ->orderBy('user_nick')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return array<array{user_id: string, user_nick: string, user_email: string}>
     */
    public function findAllSoloUsers(): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    user_id,
                    user_nick,
                    user_email
                FROM users u
                WHERE
                    user_alliance_id != 0
                    AND NOT EXISTS (
                        SELECT 1
                        FROM alliances a
                        WHERE a.alliance_id = u.user_alliance_id
                    );"
            )
            ->fetchAllAssociative();
    }

    public function updateResources(
        int $allianceId,
        int $metal,
        int $crystal,
        int $plastic,
        int $fuel,
        int $food
    ): void {
        $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_res_metal', ':metal')
            ->set('alliance_res_crystal', ':crystal')
            ->set('alliance_res_plastic', ':plastic')
            ->set('alliance_res_fuel', ':fuel')
            ->set('alliance_res_food', ':food')
            ->where('alliance_id = :id')
            ->setParameters([
                'id' => $allianceId,
                'metal' => $metal,
                'crystal' => $crystal,
                'plastic' => $plastic,
                'fuel' => $fuel,
                'food' => $food,
            ])
            ->execute();
    }

    public function addResources(
        int $allianceId,
        int $addMetal,
        int $addCrystal,
        int $addPlastic,
        int $addFuel,
        int $addFood,
        int $newMemberCount = null
    ): void {
        $qb = $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_res_metal', 'alliance_res_metal + :addMetal')
            ->set('alliance_res_crystal', 'alliance_res_crystal + :addCrystal')
            ->set('alliance_res_plastic', 'alliance_res_plastic + :addPlastic')
            ->set('alliance_res_fuel', 'alliance_res_fuel + :addFuel')
            ->set('alliance_res_food', 'alliance_res_food + :addFood')
            ->where('alliance_id = :id')
            ->setParameters([
                'id' => $allianceId,
                'addMetal' => $addMetal,
                'addCrystal' => $addCrystal,
                'addPlastic' => $addPlastic,
                'addFuel' => $addFuel,
                'addFood' => $addFood,
            ]);

        if ($newMemberCount !== null) {
            $qb
                ->set('alliance_objects_for_members', ':memberCount')
                ->setParameter('memberCount', $newMemberCount);
        }

        $qb
            ->execute();
    }

    /**
     * @return array{alliance_tag: string, alliance_name: string, alliance_id: string, alliance_rank_current: string, cnt: string, upoints: string, uavg: string}[]
     */
    public function getAllianceStats(): array
    {
        return $this->createQueryBuilder()
            ->select('a.alliance_tag, a.alliance_name, a.alliance_id, a.alliance_rank_current')
            ->addSelect('COUNT(*) AS cnt, SUM(u.points) AS upoints, AVG(u.points) AS uavg')
            ->from('alliances', 'a')
            ->innerJoin('a', 'user_stats', 'u', 'u.alliance_id = a.alliance_id')
            ->groupBy('a.alliance_id')
            ->orderBy('SUM(u.points)', 'DESC')
            ->execute()
            ->fetchAllAssociative();
    }

    public function updatePointsAndRank(int $allianceId, int $points, int $rank, int $lastRank): void
    {
        $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_points', ':points')
            ->set('alliance_rank_current', ':rank')
            ->set('alliance_rank_last', ':lastRank')
            ->where('alliance_id = :id')
            ->setParameters([
                'id' => $allianceId,
                'points' => $points,
                'rank' => $rank,
                'lastRank' => $lastRank,
            ])
            ->execute();
    }

    public function removePointsByTimestamp(int $timestamp): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('alliance_points')
            ->where("point_timestamp < :timestamp")
            ->setParameter('timestamp', $timestamp)
            ->execute();
    }

    public function resetMother(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->update('alliances')
            ->set('alliance_mother', ':zero')
            ->set('alliance_mother', ':zero')
            ->where('alliance_mother = :allianceId OR alliance_mother_request = :allianceId')
            ->setParameters([
                'zero' => 0,
                'allianceId' => $allianceId,
            ])
            ->execute();
    }

    public function addVisit(int $allianceId, bool $external = false): void
    {
        $property = $external ? 'alliance_visits_ext' : 'alliance_visits';

        $this->createQueryBuilder()
            ->update('alliances')
            ->set($property, $property . ' + 1')
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }
}
