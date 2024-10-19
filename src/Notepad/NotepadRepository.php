<?php

declare(strict_types=1);

namespace EtoA\Notepad;

use EtoA\Core\AbstractRepository;

class NotepadRepository extends AbstractRepository
{
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
        $this->getConnection()
            ->executeStatement(
                "DELETE FROM notepad
                WHERE id = :noteId
                AND user_id = :userId;",
                [
                    'noteId' => $noteId,
                    'userId' => $userId,
                ]
            );
    }

    public function deleteAll(int $userId): void
    {
        foreach ($this->findAll($userId) as $note) {
            $this->delete($note->id, $userId);
        }
    }
}
