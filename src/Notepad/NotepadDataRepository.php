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

        // NotepadData is identified by its Notepad (foreign identity), so the
        // association has to be set - there is no plain id to assign
        $notepadData->setNotepad($notepad);
        $notepad->setData($notepadData);

        $this->persist($notepadData);
        $this->save();
    }
}
