<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('alliances')
            ->execute()
            ->fetchOne();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder()
            ->select("*")
            ->from('alliances')
            ->orderBy('alliance_tag')
            ->execute()
            ->fetchAllAssociative();
    }

    /**
     * @return array<int, string>
     */
    public function getAllianceNames(): array
    {
        return $this->createQueryBuilder()
            ->select("alliance_id, alliance_name")
            ->from('alliances')
            ->orderBy('alliance_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return array<int, string>
     */
    public function getAllianceNamesWithTags(): array
    {
        $rows = $this->createQueryBuilder()
            ->select("alliance_id, alliance_name, alliance_tag")
            ->from('alliances')
            ->orderBy('alliance_name')
            ->execute()
            ->fetchAllAssociative();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['alliance_id']] = sprintf('[%s] %s', $row['alliance_tag'], $row['alliance_name']);
        }

        return $result;
    }

    public function getAlliance(int $allianceId): ?Alliance
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('alliances')
            ->where('alliance_id = :id')
            ->setParameter('id', $allianceId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Alliance($data) : null;
    }

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

    public function create(string $tag, string $name, ?int $founderId): int
    {
        if ($name == "" || $tag == "") {
            throw new InvalidAllianceParametersException("Name/Tag fehlt!");
        }

        if (!preg_match('/^[^\'\"\?\<\>\$\!\=\;\&\\\\[\]]{1,6}$/i', $tag) > 0) {
            throw new InvalidAllianceParametersException("Ungültiger Tag! Die Länge muss zwischen 3 und 6 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]\\\\");
        }

        if (!preg_match('/([^\'\"\?\<\>\$\!\=\;\&\\\\[\]]{4,25})$/', $name) > 0) {
            throw new InvalidAllianceParametersException("Ungültiger Name! Die Länge muss zwischen 4 und 25 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]\\\\");
        }

        if ($founderId === null || $founderId <= 0) {
            throw new InvalidAllianceParametersException("Allianzgründer-ID fehlt!");
        }

        $exists = $this->createQueryBuilder()
            ->select('*')
            ->from('alliances')
            ->where('alliance_tag = :tag')
            ->orWhere('alliance_name = :name')
            ->setParameters([
                'name' => $name,
                'tag' => $tag,
            ])
            ->execute()
            ->fetchAssociative();
        if ($exists) {
            throw new InvalidAllianceParametersException("Eine Allianz mit diesem Tag oder Namen existiert bereits!");
        }

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

    public function update(int $id, string $tag, string $name, string $text, string $template, string $url, int $founder): bool
    {
        $affected = $this->createQueryBuilder()
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
            ])
            ->execute();

        return (int) $affected > 0;
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

    public function findRanks(int $allianceId): array
    {
        return $this->createQueryBuilder()
            ->select(
                'rank_id',
                'rank_level',
                'rank_name'
            )
            ->from('alliance_ranks')
            ->where('rank_alliance_id = :allianceId')
            ->orderBy('rank_level', 'DESC')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchAllAssociative();
    }

    public function updateRank(int $id, string $name, int $level): void
    {
        $this->createQueryBuilder()
            ->update('alliance_ranks')
            ->set('rank_name', ':name')
            ->set('rank_level', ':level')
            ->where('rank_id = :id')
            ->setParameters([
                'id' => $id,
                'name' => $name,
                'level' => $level,
            ])
            ->execute();
    }

    public function countOrphanedRanks(): int
    {
        return (int) $this->getConnection()
            ->executeQuery(
                "SELECT
                    COUNT(r.rank_id)
                FROM alliance_ranks r
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM alliances a
                    WHERE r.rank_alliance_id = a.alliance_id
                );"
            )
            ->fetchOne();
    }

    public function findDiplomacies(int $allianceId): array
    {
        return $this->createQueryBuilder()
            ->select(
                'alliance_bnd_id',
                'alliance_bnd_alliance_id1 as a1id',
                'alliance_bnd_alliance_id2 as a2id',
                'a1.alliance_name as a1name',
                'a2.alliance_name as a2name',
                'alliance_bnd_level as lvl',
                'alliance_bnd_name as name',
                'alliance_bnd_date as date'
            )
            ->from('alliance_bnd', 'b')
            ->leftJoin('b', 'alliances', 'a1', 'alliance_bnd_alliance_id1 = a1.alliance_id')
            ->leftJoin('b', 'alliances', 'a2', 'alliance_bnd_alliance_id2 = a2.alliance_id')
            ->where('alliance_bnd_alliance_id1 = :id')
            ->orWhere('alliance_bnd_alliance_id2 = :id')
            ->orderBy('alliance_bnd_level', 'DESC')
            ->addOrderBy('alliance_bnd_date', 'DESC')
            ->setParameters([
                'id' => $allianceId,
            ])
            ->execute()
            ->fetchAllAssociative();
    }

    public function deleteOrphanedRanks(): int
    {
        return $this->getConnection()
            ->executeStatement(
                "DELETE FROM alliance_ranks
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM alliances a
                    WHERE rank_alliance_id = a.alliance_id
                );"
            );
    }

    public function countOrphanedDiplomacies(): int
    {
        return (int) $this->getConnection()
            ->executeQuery(
                "SELECT
                    COUNT(b.alliance_bnd_id)
                FROM alliance_bnd b
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM alliances a
                    WHERE b.alliance_bnd_alliance_id1 = a.alliance_id
                )
                OR NOT EXISTS (
                    SELECT 1
                    FROM alliances a
                    WHERE b.alliance_bnd_alliance_id2 = a.alliance_id
                );"
            )
            ->fetchOne();
    }

    public function deleteOrphanedDiplomacies(): int
    {
        return $this->getConnection()
            ->executeStatement(
                "DELETE FROM alliance_bnd
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM alliances a
                    WHERE alliance_bnd_alliance_id1 = a.alliance_id
                )
                OR NOT EXISTS (
                    SELECT 1
                    FROM alliances a
                    WHERE alliance_bnd_alliance_id2 = a.alliance_id
                )"
            );
    }

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

    public function remove(int $id): bool
    {
        $affected = (int) $this->createQueryBuilder()
            ->delete('alliances')
            ->where('alliance_id = :id')
            ->setParameter('id', $id)
            ->execute();

        $this->deleteRanks($id);
        $this->deleteDiplomacies($id);

        return $affected > 0;
    }

    public function deleteRanks(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete("alliance_ranks")
            ->where('rank_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }

    public function updateDiplomacy(int $id, int $level, string $name): void
    {
        $this->createQueryBuilder()
            ->update('alliance_bnd')
            ->set('alliance_bnd_level', ':level')
            ->set('alliance_bnd_name', ':name')
            ->where('alliance_bnd_id = :id')
            ->setParameters([
                'id' => $id,
                'level' => $level,
                'name' => $name,
            ])
            ->execute();
    }

    public function deleteDiplomacy(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_bnd')
            ->where('alliance_bnd_id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function deleteDiplomacies(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_bnd')
            ->where('alliance_bnd_alliance_id1 = :id')
            ->orWhere('alliance_bnd_alliance_id2 = :id')
            ->setParameter('id', $allianceId)
            ->execute();
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

    public function assignRankToUser($rankId, $userId): void
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

    public function removeRank(int $rankId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_ranks')
            ->where('rank_id = :rankId')
            ->setParameter('rankId', $rankId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('alliance_rankrights')
            ->where('rr_rank_id = :rankId')
            ->setParameter('rankId', $rankId)
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

    public function findHistoryEntries(int $allianceId): array
    {
        return $this->createQueryBuilder()
            ->select('*')
            ->from('alliance_history')
            ->where('history_alliance_id = :allianceId')
            ->orderBy('history_timestamp', 'DESC')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchAllAssociative();
    }

    public function findBuildings(int $allianceId): array
    {
        return $this->createQueryBuilder()
            ->select(
                'bl.*',
                'b.alliance_building_name'
            )
            ->from('alliance_buildlist', 'bl')
            ->innerJoin('bl', 'alliance_buildings', 'b', 'b.alliance_building_id = bl.alliance_buildlist_building_id AND alliance_buildlist_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchAllAssociative();
    }

    public function findTechnologies(int $allianceId): array
    {
        return $this->createQueryBuilder()
            ->select(
                'tl.*',
                't.alliance_tech_name'
            )
            ->from('alliance_techlist', 'tl')
            ->innerJoin('tl', 'alliance_technologies', 't', 't.alliance_tech_id = tl.alliance_techlist_tech_id AND alliance_techlist_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute()
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
        int $addFood
    ): void {
        $this->createQueryBuilder()
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
}
