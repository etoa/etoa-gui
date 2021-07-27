<?php

declare(strict_types=1);

namespace EtoA\Notepad;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class NotepadRepository extends AbstractRepository
{
    public function count(int $userId): int
    {
        return (int) $this->getConnection()
            ->executeQuery(
                "SELECT COUNT(id)
                FROM notepad
                WHERE user_id = :userId;",
                ['userId' => $userId]
            )
            ->fetchOne();
    }

    public function find(int $noteId, int $userId): ?Note
    {
        $data = $this->getConnection()->executeQuery(
            "SELECT
                n.id,
                n.timestamp,
                nd.subject,
                nd.text
            FROM
                notepad as n
            INNER JOIN
                notepad_data as nd
                ON nd.id = n.id
                AND n.user_id = :userId
                AND n.id = :noteId",
            [
                'noteId' => $noteId,
                'userId' => $userId,
            ]
        )->fetchAssociative();

        return $data !== false ? new Note($data) : null;
    }

    /**
     * @return array<Note>
     */
    public function findAll(int $userId): array
    {
        $data = $this->getConnection()->executeQuery(
            "SELECT
                n.id,
                n.timestamp,
                nd.subject,
                nd.text
            FROM
                notepad as n
            INNER JOIN
                notepad_data as nd
                ON nd.id = n.id
                AND user_id = :userId
            ORDER BY
                timestamp DESC;",
            [
                'userId' => $userId,
            ]
        )->fetchAllAssociative();

        return array_map(fn (array $arr) => new Note($arr), $data);
    }

    public function add(string $subject, string $text, int $userId): int
    {
        $this->getConnection()->executeStatement(
            "INSERT INTO
                notepad
            (
                user_id,
                timestamp
            )
            VALUES
            (
                :userId,
                :timestamp
            );",
            [
                'userId' => $userId,
                'timestamp' => time(),
            ]
        );
        $id = (int) $this->getConnection()->lastInsertId();

        $this->getConnection()->executeStatement(
            "INSERT INTO
                notepad_data
            (
                id,
                subject,
                text
            )
            VALUES
            (
                :id, :subject, :text
            );",
            [
                'id' => $id,
                'subject' => $subject,
                'text' => $text,
            ]
        );

        return $id;
    }

    public function update(int $noteId, int $userId, string $subject, string $text): void
    {
        $affected = $this->getConnection()->executeStatement(
            "UPDATE
                notepad
            SET
                timestamp='" . time() . "'
            WHERE
                user_id = :userId
                AND id = :noteId
            ;",
            [
                'userId' => $userId,
                'noteId' => $noteId,
            ]
        );
        if ($affected > 0) {
            $this->getConnection()->executeStatement(
                "UPDATE
                    notepad_data
                SET
                    subject = :subject,
                    text = :text
                WHERE
                    id = :noteId;",
                [
                    'subject' => $subject,
                    'text' => $text,
                    'noteId' => $noteId,
                ]
            );
        }
    }

    public function delete(int $noteId, int $userId): void
    {
        $affected = $this->getConnection()
            ->executeStatement(
                "DELETE FROM notepad
                WHERE id = :noteId
                AND user_id = :userId;",
                [
                    'noteId' => $noteId,
                    'userId' => $userId,
                ]
            );
        if ($affected > 0) {
            $this->getConnection()
                ->executeStatement(
                    "DELETE FROM notepad_data
                    WHERE id = :noteId;",
                    ['noteId' => $noteId]
                );
        }
    }

    public function deleteAll(int $userId): void
    {
        foreach ($this->findAll($userId) as $note) {
            $this->delete($note->id, $userId);
        }
    }

    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(id)')
            ->from('notepad')
            ->where($qb->expr()->notIn('user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchOne();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function deleteOrphaned(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('notepad')
            ->where($qb->expr()->notIn('user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }
}
