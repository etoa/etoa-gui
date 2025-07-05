<?php

declare(strict_types=1);

namespace EtoA\Notepad;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Notepad;
use EtoA\Entity\NotepadData;
use EtoA\Entity\User;

class NotepadDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, private readonly NotepadRepository $notepadRepository)
    {
        parent::__construct($registry, NotepadData::class);
    }

    public function add(NotepadData $notepadData, User $user): void
    {
        $notepad = new Notepad();
        $notepad->setUser($user);
        $notepad->setTimestamp(time());

        $this->notepadRepository->persist($notepad);
        $this->notepadRepository->save();

        $notepadData->setId($notepad->getId());
        $this->persist($notepadData);
        $this->save();
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
