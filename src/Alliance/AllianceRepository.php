<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

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
        return $this->getConnection()
            ->executeQuery("SELECT *
				FROM alliances
				ORDER BY alliance_tag;")
            ->fetchAllAssociative();
    }

    function find(int $id): ?array
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
					user_points
				FROM users
				WHERE user_alliance_id = ?
				ORDER BY user_nick;",
                [$allianceId]
            )
            ->fetchAllAssociative();
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
}
