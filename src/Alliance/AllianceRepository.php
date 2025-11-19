<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\User;

class AllianceRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alliance::class);
    }

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

    /**
     * @return array<Alliance>
     */
    public function getAllianceNames(AllianceSearch $search = null, int $limit = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit)
            ->orderBy('q.name')
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, Alliance>
     */
    public function searchAlliances(AllianceSearch $search = null, int $limit = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit)
            ->groupBy('q.id')
            ->orderBy('q.name')
            ->addOrderBy('q.tag')
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, string>
     */
    public function getAllianceTags(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("q.id, q.tag")
            ->orderBy('q.name')
            ->getQuery()
            ->execute();

        return array_column($data, 'tag', 'id');
    }

    /**
     * @return array<int, string>
     */
    public function getAllianceNamesWithTags(AllianceSearch $search = null, int $limit = null): array
    {
        $rows = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit)
            ->orderBy('q.name')
            ->addOrderBy('q.tag')
            ->getQuery()
            ->execute();

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row->getId()] = sprintf('[%s] %s', $row->getTag(), $row->getName());
        }

        return $result;
    }

    /**
     * @return Alliance[]
     */
    public function getAlliances(): array
    {
        return $this->findBy([],['name'=>'DESC','tag'=>'DESC']);
    }

    /**
     * @return AllianceWithMemberCount[]
     */
    public function getAlliancesAcceptingApplications(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("q")
            ->addSelect('COUNT(u.id) as member_count')
            ->leftJoin('App:User', 'u', 'WITH', 'u.alliance=q.id')
            ->where('q.acceptApplications = 1')
            ->groupBy('q.id')
            ->orderBy('q.name')
            ->addOrderBy('q.tag')
            ->getQuery()
            ->execute();
        return array_map(fn (array $row) => new AllianceWithMemberCount($row), $data);
    }

    public function getAlliance(int $allianceId): ?AllianceWithMemberCount
    {
        if ($allianceId === 0) {
            return null;
        }

        $data = $this->createQueryBuilder('q')
            ->select("q")
            ->addSelect('COUNT(u.id) as member_count')
            ->leftJoin('App:User', 'u', 'WITH', 'u.alliance=q.id')
            ->where('q.id = :id')
            ->setParameter('id', $allianceId)
            ->groupBy('q.id')
            ->getQuery()
            ->getOneOrNullResult();

        return $data !== false ? new AllianceWithMemberCount($data) : null;
    }

    public function getFounderId(int $allianceId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('alliance_founder_id')
            ->from('alliances')
            ->where('alliance_id = :id')
            ->setParameter('id', $allianceId)
            ->fetchOne();
    }
    public function setFounder(Alliance $alliance, User $founder): void
    {
        $alliance->setFounder($founder);
        $this->save();
    }


    public function exists(string $tag, string $name, int $ignoreAllianceId = null): bool
    {


        $qb = $this->createQueryBuilder('q')
            ->select('q.id')
            ->where('q.tag = :tag OR q.name = :name')
            ->setParameters([
                'name' => $name,
                'tag' => $tag,
            ]);

        if ($ignoreAllianceId) {
            $qb
                ->andWhere('q.id <> :allianceId')
                ->setParameter('allianceId', $ignoreAllianceId);
        }

        return (bool) $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function create(string $tag, string $name, User $founder): Alliance
    {
        $alliance = new Alliance();
        $alliance->setTag($tag);
        $alliance->setName($name);
        $alliance->setFounder($founder);
        $alliance->setFoundationTimestamp(time());
        $alliance->setPublicMemberList(true);

        $this->persist($alliance);
        $this->save();

        return $alliance;
    }

    public function updateApplicationText(int $allianceId, string $template): void
    {
        $this->createQueryBuilder('q')
            ->update('alliances')
            ->set('alliance_application_template', ':template')
            ->where('alliance_id = :id')
            ->setParameters([
                'template' => $template,
                'allianceId' => $allianceId,
            ])
            ->executeQuery();
    }

    public function update(int $id, string $tag, string $name, ?string $text, ?string $template, ?string $url, int $founder, string $updatedAllianceImage = null, bool $acceptsApplications = null, bool $acceptsBnd = null, bool $publicMemberList = null): bool
    {
        $qb = $this->createQueryBuilder('q')
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
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function getPicture(int $allianceId): ?string
    {
        return $this->createQueryBuilder('q')
            ->select('alliance_img')
            ->from('alliances')
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->fetchOne();
    }

    public function clearPicture(Alliance $alliance): void
    {
        $alliance->setImage(null);
        $alliance->setImageCheck(false);

        $this->save();
    }

    public function markPictureChecked(int $allianceId): bool
    {
        $affected = $this->createQueryBuilder('q')
            ->update('alliances')
            ->set('alliance_img_check', ':check')
            ->where('alliance_id = :allianceId')
            ->setParameters([
                'allianceId' => $allianceId,
                'check' => 0,
            ])
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    /**
     * @return array<array{alliance_id: string, alliance_tag: string, alliance_name: string, alliance_img: string}>
     */
    public function findAllWithUncheckedPictures(): array
    {
        return $this->createQueryBuilder('q')
            ->select(
                'q.id',
                'q.tag',
                'q.name',
                'q.image'
            )
            ->where('q.imageCheck = 1')
            ->andWhere("q.image != ''")
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<array{alliance_id: string, alliance_tag: string, alliance_name: string, alliance_img: string}>
     */
    public function findAllWithPictures(): array
    {
        return $this->createQueryBuilder('q')
            ->select(
                'q.id',
                'q.tag',
                'q.name',
                'q.image'
            )
            ->where("q.image != ''")
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<array{alliance_id: string, alliance_name: string, alliance_tag: string}>
     */
    public function findAllWithoutFounder(): array
    {
        return $this->getConnection()
            ->fetchAllAssociative(
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
            );
    }

    /**
     * @return array<array{alliance_id: string, alliance_name: string, alliance_tag: string}>
     */
    public function findAllWithoutUsers(): array
    {
        return $this->getConnection()
            ->fetchAllAssociative(
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
            );
    }

    public function countUsers(int $allianceId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select("COUNT(q)")
            ->where('q.id = :id')
            ->setParameter('id', $allianceId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<array{user_id: string, user_nick: string, user_points: string, user_alliance_rank_id: string}>
     */
    public function findUsers(int $allianceId): array
    {
        $data = $this->createQueryBuilder('q')
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
            ->fetchAllAssociative();

        $users = [];
        foreach ($data as $row) {
            $users[$row['user_id']] = $row;
        }

        return $users;
    }

    public function assignRankToUser(int $rankId, int $userId): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_alliance_rank_id', ':rank')
            ->where('user_id = :user')
            ->setParameters([
                'rank' => $rankId,
                'user' => $userId,
            ])
            ->executeQuery();
    }

    public function removeUser(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->update('users')
            ->set('user_alliance_id', (string) 0)
            ->set('user_alliance_rank_id', (string) 0)
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }

    /**
     * @return array<int, string>
     */
    public function listSoloUsers(): array
    {
        return $this->createQueryBuilder('q')
            ->select("user_id", "user_nick")
            ->from('users')
            ->where('user_alliance_id = 0')
            ->orderBy('user_nick')
            ->fetchAllKeyValue();
    }

    /**
     * @return array<array{user_id: string, user_nick: string, user_email: string}>
     */
    public function findAllSoloUsers(): array
    {
        return $this->getConnection()
            ->fetchAllAssociative(
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
            );
    }

    public function updateResources(
        int $allianceId,
        int $metal,
        int $crystal,
        int $plastic,
        int $fuel,
        int $food
    ): void {
        $this->createQueryBuilder('q')
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
            ->executeQuery();
    }

    public function addResources(
        Alliance $alliance,
        int $addMetal,
        int $addCrystal,
        int $addPlastic,
        int $addFuel,
        int $addFood,
        int $newMemberCount = null
    ): void {
        $qb = $this->createQueryBuilder('q')
            ->update()
            ->set('q.resMetal', 'q.resMetal + :addMetal')
            ->set('q.resCrystal', 'q.resCrystal + :addCrystal')
            ->set('q.resPlastic', 'q.resPlastic + :addPlastic')
            ->set('q.resFuel', 'q.resFuel + :addFuel')
            ->set('q.resFood', 'q.resFood + :addFood')
            ->where('q.id = :id')
            ->setParameters([
                'id' => $alliance,
                'addMetal' => $addMetal,
                'addCrystal' => $addCrystal,
                'addPlastic' => $addPlastic,
                'addFuel' => $addFuel,
                'addFood' => $addFood,
            ]);

        if ($newMemberCount !== null) {
            $qb
                ->set('q.objectsForMembers', ':memberCount')
                ->setParameter('memberCount', $newMemberCount);
        }

        $qb
            ->getQuery()
            ->execute();
    }

    /**
     * @return array{alliance_tag: string, alliance_name: string, alliance_id: string, alliance_rank_current: string, cnt: string, upoints: string, uavg: string}[]
     */
    public function getAllianceStats(): array
    {
        return $this->createQueryBuilder('q')
            ->select('q.tag, q.name, q.id, q.currentRank')
            ->addSelect('COUNT(q) AS cnt, SUM(u.points) AS upoints, AVG(u.points) AS uavg')
            ->innerJoin('App:UserStat', 'u', 'WITH', 'u.alliance = q.id')
            ->groupBy('q.id')
            ->orderBy('SUM(u.points)', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function updatePointsAndRank(Alliance $alliance, int $points, int $rank, int $lastRank): void
    {
        $alliance->setPoints($points);
        $alliance->setCurrentRank($rank);
        $alliance->setLastRank($lastRank);

        $this->save();
    }

    public function removePointsByTimestamp(int $timestamp): int
    {
        return $this->createQueryBuilder('q')
            ->delete('alliance_points')
            ->where("point_timestamp < :timestamp")
            ->setParameter('timestamp', $timestamp)
            ->executeQuery()
            ->rowCount();
    }

    public function resetMother(Alliance $alliance): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.mother', ':zero')
            ->where('q.mother = :alliance OR q.motherRequest = :alliance')
            ->setParameters([
                'zero' => null,
                'alliance' => $alliance,
            ])
            ->getQuery()
            ->execute();
    }

    public function setMotherOrRequest(int $allianceId, int $motherId, int $motherRequestId): void
    {
        $this->createQueryBuilder('q')
            ->update('alliances')
            ->set('alliance_mother', ':motherId')
            ->set('alliance_mother_request', ':motherRequestId')
            ->where('alliance_id = :allianceId')
            ->setParameters([
                'allianceId' => $allianceId,
                'motherId' => $motherId,
                'motherRequestId' => $motherRequestId,
            ])
            ->executeQuery();
    }

    public function addVisit(int $allianceId, bool $external = false): void
    {
        $property = $external ? 'alliance_visits_ext' : 'alliance_visits';

        $this->createQueryBuilder('q')
            ->set($property, $property . ' + 1')
            ->where('q.id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->getQuery()
            ->execute();
    }
}
