<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceRepository extends AbstractRepository
{
    function numberOfAlliances(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('alliances')
            ->execute()
            ->fetchOne();
    }

    function numberOfUsersInAlliance(int $allianceId): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('users')
            ->where('user_alliance_id = :id')
            ->setParameter('id', $allianceId)
            ->execute()
            ->fetchOne();
    }

    function fetchUsersInAlliance(int $allianceId): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
					user_id,
					user_nick,
					user_points
				FROM users
				WHERE user_alliance_id = ?
				ORDER BY user_nick;",
                [$allianceId]
            )
            ->fetchAllAssociative();
    }

    function fetchAlliances(): array
    {
        return $this->getConnection()
            ->executeQuery("SELECT *
				FROM alliances
				ORDER BY alliance_tag;")
            ->fetchAllAssociative();
    }

    function fetchAlliance(?int $id): ?array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT *
				FROM alliances
				WHERE alliance_id = ?;",
                [$id]
            )
            ->fetchAssociative();
    }

    function findAlliances(array $formData): array
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

    function usersWithoutAllianceList(): array
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

    function getAlliancePicture(int $allianceId): ?string
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

    function clearAlliancePicture(int $allianceId): bool
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

    function markAlliancePictureChecked(int $allianceId): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE alliances
				SET alliance_img_check = 0
				WHERE alliance_id = ?;",
                [$allianceId]
            );
    }

    function fetchAlliancesWithUncheckedPictures(): array
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

    function fetchAlliancesWithPictures(): array
    {
        return $this->getConnection()
            ->executeQuery("SELECT
					alliance_id,
					alliance_name,
					alliance_img
				FROM
					alliances
				WHERE
					alliance_img!=''")
            ->fetchAllAssociative();
    }

    function numberOfRanksWithoutAlliance(): int
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

    function deleteRanksWithoutAlliance(): int
    {
        return $this->getConnection()
            ->executeStatement("DELETE FROM alliance_ranks
				WHERE NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE rank_alliance_id = a.alliance_id
				);");
    }

    function numberOfDiplomaciesWithoutAlliance(): int
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

    function deleteDiplomacyWithoutAlliance(): int
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

    function fetchAlliancesWithoutFounder(): array
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

    function fetchUsersWithoutAlliance(): array
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

    function fetchAlliancesWithoutUsers(): array
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

    function deleteAlliance(int $id): bool
    {
        $affected = $this->getConnection()
            ->delete("alliances", [
                'alliance_id' => $id,
            ]);

        $this->deleteRanks($id);
        $this->deleteDiplomacy($id);

        return $affected > 0;
    }

    function deleteRanks(int $allianceId): void
    {
        $this->getConnection()
            ->delete("alliance_ranks", [
                'rank_alliance_id' => $allianceId,
            ]);
    }

    function deleteDiplomacy(int $allianceId): void
    {
        $this->getConnection()
            ->executeStatement(
                "DELETE FROM alliance_bnd
				WHERE alliance_bnd_alliance_id1 = :id
					OR alliance_bnd_alliance_id2 = :id;",
                ['id' => $allianceId]
            );
    }
}
