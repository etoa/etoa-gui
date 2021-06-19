<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Config;
use EtoA\Core\AbstractRepository;
use Log;

class AllianceRepository extends AbstractRepository
{
    function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('alliances')
            ->execute()
            ->fetchOne();
    }

    function findAll(): array
    {
        return $this->createQueryBuilder()
            ->select("*")
            ->from('alliances')
            ->orderBy('alliance_tag')
            ->execute()
            ->fetchAllAssociative();
    }

    function find(int $id): ?array
    {
        return $this->createQueryBuilder()
            ->select("*")
            ->from('alliances')
            ->where('alliance_id = ?')
            ->setParameter(0, $id)
            ->execute()
            ->fetchAssociative();
    }

    function findByFormData(array $formData): array
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

    function create(string $tag, string $name, ?int $founderId): int
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

        if ($founderId == null || $founderId <= 0) {
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

    function update(int $id, array $data): bool
    {
        $affected = $this->getConnection()
            ->executeStatement(
                "UPDATE
                    alliances
                SET
                    alliance_name = :name,
                    alliance_tag = :tag,
                    alliance_text = :text,
                    alliance_application_template = :template,
                    alliance_url = :url,
                    alliance_founder_id = :founder
                WHERE
                    alliance_id = :id;",
                [
                    'id' => $id,
                    'name' => $data['name'],
                    'tag' => $data['tag'],
                    'text' => $data['text'],
                    'template' => $data['template'],
                    'url' => $data['url'],
                    'founder' => $data['founder'],
                ]
            );

        return $affected > 0;
    }

    function getPicture(int $allianceId): ?string
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT alliance_img
                FROM alliances
                WHERE alliance_id = ?;",
                [$allianceId]
            )
            ->fetchOne();
    }

    function clearPicture(int $allianceId): bool
    {
        $affected = $this->getConnection()
            ->executeStatement(
                "UPDATE alliances
                SET alliance_img = '',
                    alliance_img_check = 0
                WHERE alliance_id = ?;",
                [$allianceId]
            );
        return $affected > 0;
    }

    function markPictureChecked(int $allianceId): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE alliances
				SET alliance_img_check = 0
				WHERE alliance_id = ?;",
                [$allianceId]
            );
    }

    function findAllWithUncheckedPictures(): array
    {
        return $this->getConnection()
            ->executeQuery("SELECT
					alliance_id,
					alliance_tag,
					alliance_name,
					alliance_img
				FROM
					alliances
				WHERE
					alliance_img_check = 1
					AND alliance_img != '';")
            ->fetchAllAssociative();
    }

    function findAllWithPictures(): array
    {
        return $this->getConnection()
            ->executeQuery("SELECT
					alliance_id,
					alliance_name,
					alliance_img
				FROM
					alliances
				WHERE
					alliance_img != ''")
            ->fetchAllAssociative();
    }

    function findRanks(int $allianceId): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    rank_id,
                    rank_level,
                    rank_name
                FROM
                    alliance_ranks
                WHERE
                    rank_alliance_id = ?
                ORDER BY
                    rank_level DESC;",
                [$allianceId]
            )
            ->fetchAllAssociative();
    }

    function updateRank(int $id, string $name, int $level): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE
                    alliance_ranks
                SET
                    rank_name = :name,
                    rank_level = :level
                WHERE
                    rank_id = :id;",
                [
                    'id' => $id,
                    'name' => $name,
                    'level' => $level,
                ]
            );
    }

    function countOrphanedRanks(): int
    {
        return (int) $this->getConnection()
            ->executeQuery("SELECT
					COUNT(r.rank_id)
				FROM alliance_ranks r
				WHERE NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE r.rank_alliance_id = a.alliance_id
				);")
            ->fetchOne();
    }

    function findDiplomacies(int $allianceId): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                        alliance_bnd_id,
                        alliance_bnd_alliance_id1 as a1id,
                        alliance_bnd_alliance_id2 as a2id,
                        a1.alliance_name as a1name,
                        a2.alliance_name as a2name,
                        alliance_bnd_level as lvl,
                        alliance_bnd_name as name,
                        alliance_bnd_date as date
                    FROM
                        alliance_bnd
                    LEFT JOIN
                        alliances a1 on alliance_bnd_alliance_id1 = a1.alliance_id
                    LEFT JOIN
                        alliances a2 on alliance_bnd_alliance_id2 = a2.alliance_id
                    WHERE
                        alliance_bnd_alliance_id1 = :id
                        OR alliance_bnd_alliance_id2 = :id
                    ORDER BY
                        alliance_bnd_level DESC,
                        alliance_bnd_date DESC;",
                [
                    'id' => $allianceId,
                ]
            )
            ->fetchAllAssociative();
    }

    function deleteOrphanedRanks(): int
    {
        return $this->getConnection()
            ->executeStatement("DELETE FROM alliance_ranks
				WHERE NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE rank_alliance_id = a.alliance_id
				);");
    }

    function countOrphanedDiplomacies(): int
    {
        return (int) $this->getConnection()
            ->executeQuery("SELECT
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
				);")
            ->fetchOne();
    }

    function deleteOrphanedDiplomacies(): int
    {
        return $this->getConnection()
            ->executeStatement("DELETE FROM alliance_bnd
				WHERE NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE alliance_bnd_alliance_id1 = a.alliance_id
				)
				OR NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE alliance_bnd_alliance_id2 = a.alliance_id
				)");
    }

    function findAllWithoutFounder(): array
    {
        return $this->getConnection()
            ->executeQuery("SELECT
					alliance_id,
					alliance_name,
					alliance_tag
				FROM alliances a
				WHERE NOT EXISTS (
					SELECT 1
					FROM users u
					WHERE a.alliance_founder_id = u.user_id
				);")
            ->fetchAllAssociative();
    }

    function findAllWithoutUsers(): array
    {
        return $this->getConnection()
            ->executeQuery("SELECT
					alliance_id,
					alliance_name,
					alliance_tag
				FROM alliances a
				WHERE NOT EXISTS (
					SELECT 1
					FROM users u
					WHERE a.alliance_id = u.user_alliance_id
				);")
            ->fetchAllAssociative();
    }

    function remove(int $id): bool
    {
        $affected = $this->getConnection()
            ->delete("alliances", [
                'alliance_id' => $id,
            ]);

        $this->deleteRanks($id);
        $this->deleteDiplomacies($id);

        return $affected > 0;
    }

    function deleteRanks(int $allianceId): void
    {
        $this->getConnection()
            ->delete("alliance_ranks", [
                'rank_alliance_id' => $allianceId,
            ]);
    }

    function updateDiplomacy(int $id, int $level, string $name): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE
                    alliance_bnd
                SET
                    alliance_bnd_level = ?,
                    alliance_bnd_name = ?
                WHERE
                    alliance_bnd_id = ?;",
                [
                    'id' => $id,
                    'level' => $level,
                    'name' => $name,
                ]
            );
    }

    function deleteDiplomacy(int $id): void
    {
        $this->getConnection()
            ->executeStatement(
                "DELETE FROM alliance_bnd
                WHERE alliance_bnd_id = ?;",
                [$id]
            );
    }

    function deleteDiplomacies(int $allianceId): void
    {
        $this->getConnection()
            ->executeStatement(
                "DELETE FROM alliance_bnd
				WHERE alliance_bnd_alliance_id1 = :id
					OR alliance_bnd_alliance_id2 = :id;",
                ['id' => $allianceId]
            );
    }

    function countUsers(int $allianceId): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('users')
            ->where('user_alliance_id = :id')
            ->setParameter('id', $allianceId)
            ->execute()
            ->fetchOne();
    }

    function findUsers(int $allianceId): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
					user_id,
					user_nick,
					user_points,
                    user_alliance_rank_id
				FROM users
				WHERE user_alliance_id = ?
				ORDER BY
                    user_points DESC,
                    user_nick;",
                [$allianceId]
            )
            ->fetchAllAssociative();
    }

    function assignRankToUser($rankId, $userId): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE
                    users
                SET
                    user_alliance_rank_id = :rank
                WHERE
                    user_id = :user;",
                [
                    'rank' => $rankId,
                    'user' => $userId,
                ]
            );
    }

    function removeRank(int $rankId): void
    {
        $this->getConnection()
            ->executeStatement(
                "DELETE FROM alliance_ranks
                WHERE rank_id = ?;",
                [$rankId]
            );
        $this->getConnection()
            ->executeStatement(
                "DELETE FROM alliance_rankrights
                WHERE rr_rank_id = ?;",
                [$rankId]
            );
    }

    function removeUser(int $userId): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE
                    users
                SET
                    user_alliance_id = 0,
                    user_alliance_rank_id = 0
                WHERE
                    user_id = ?;",
                [$userId]
            );
    }

    function listSoloUsers(): array
    {
        $res = $this->getConnection()
            ->executeQuery("SELECT user_id, user_nick
				FROM users
				WHERE user_alliance_id = 0
				ORDER BY user_nick;");
        $data = [];
        while ($arr = $res->fetchAssociative()) {
            $data[$arr['user_id']] = $arr['user_nick'];
        }
        return $data;
    }

    function findAllSoloUsers(): array
    {
        return $this->getConnection()
            ->executeQuery("SELECT
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
					);")
            ->fetchAllAssociative();
    }

    function findHistoryEntries(int $allianceId): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    *
                FROM
                    alliance_history
                WHERE
                    history_alliance_id = ?
                ORDER BY
                    history_timestamp
                DESC;",
                [$allianceId]
            )
            ->fetchAllAssociative();
    }

    function findBuildings(int $allianceId): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    alliance_buildlist.*,
                    alliance_buildings.alliance_building_name
                FROM
                    alliance_buildlist
                INNER JOIN
                    alliance_buildings
                ON
                    alliance_buildings.alliance_building_id = alliance_buildlist.alliance_buildlist_building_id
                    AND	alliance_buildlist_alliance_id = ?;",
                [$allianceId]
            )
            ->fetchAllAssociative();
    }

    function findTechnologies(int $allianceId): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    alliance_techlist.*,
                    alliance_technologies.alliance_tech_name
                FROM
                    alliance_techlist
                INNER JOIN
                    alliance_technologies
                ON
                    alliance_technologies.alliance_tech_id = alliance_techlist.alliance_techlist_tech_id
                    AND	alliance_techlist_alliance_id = ?;",
                [$allianceId]
            )
            ->fetchAllAssociative();
    }

    function updateResources(int $allianceId, array $data): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE
                    alliances
                SET
                    alliance_res_metal = :metal,
                    alliance_res_crystal = :crystal,
                    alliance_res_plastic = :plastic,
                    alliance_res_fuel = :fuel,
                    alliance_res_food = :food,
                    alliance_res_metal = alliance_res_metal + :addmetal,
                    alliance_res_crystal = alliance_res_crystal + :addcrystal,
                    alliance_res_plastic = alliance_res_plastic + :addplastic,
                    alliance_res_fuel = alliance_res_fuel + :addfuel,
                    alliance_res_food = alliance_res_food + :addfood
                WHERE
                    alliance_id = :id
                LIMIT 1;",
                [
                    'id' => $allianceId,
                    'metal' => $data['metal'],
                    'crystal' => $data['crystal'],
                    'plastic' => $data['plastic'],
                    'fuel' => $data['fuel'],
                    'food' => $data['food'],
                    'addmetal' => $data['addmetal'],
                    'addcrystal' => $data['addcrystal'],
                    'addplastic' => $data['addplastic'],
                    'addfuel' => $data['addfuel'],
                    'addfood' => $data['addfood'],
                ]
            );
    }

    public function cleanUpPoints(int $threshold = 0): int
    {
        // TODO
        $cfg = Config::getInstance();

        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * (int) $cfg->get('log_threshold_days'));

        $affected = (int) $this->createQueryBuilder()
            ->delete('alliance_points')
            ->where("point_timestamp<" . $timestamp)
            ->execute();

        Log::add("4", Log::INFO, "$affected Allianzpunkte-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $affected;
    }
}
