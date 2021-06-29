<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\User\UserLogRepository;
use EtoA\User\UserRepository;

class AllianceManagementService
{
    private AllianceRepository $repository;
    private AllianceHistoryRepository $historyRepository;
    private UserRepository $userRepository;
    private UserLogRepository $userLogRepository;
    private $dispatcher;

    public function __construct(
        AllianceRepository $repository,
        AllianceHistoryRepository $historyRepository,
        UserRepository $userRepository,
        UserLogRepository $userLogRepository,
        $dispatcher)
    {
        $this->repository = $repository;
        $this->historyRepository = $historyRepository;
        $this->userRepository = $userRepository;
        $this->userLogRepository = $userLogRepository;
        $this->dispatcher = $dispatcher;
    }

    public function create(string $tag, string $name, ?int $founderId): Alliance
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

        if ($this->repository->exists($tag, $name)) {
            throw new InvalidAllianceParametersException("Eine Allianz mit diesem Tag oder Namen existiert bereits!");
        }

        if ($founderId === null || $founderId <= 0) {
            throw new InvalidAllianceParametersException("Allianzgründer-ID fehlt!");
        }

        $founder = $this->userRepository->getUser($founderId);
        if ($founder === null) {
            throw new InvalidAllianceParametersException("Allianzgründer existiert nicht!");
        }

        $id = $this->repository->add($tag, $name, $founderId);
        $this->repository->addUser($id, $founderId);

        $alliance = $this->repository->getAlliance($id);

        $this->userLogRepository->add($founder, "alliance", "{nick} hat die Allianz [b]" . $alliance->toString() . "[/b] gegründet.");
        $this->historyRepository->addEntry($id, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde von [b]" . $founder->nick . "[/b] gegründet!");

        $this->dispatcher->dispatch(new Event\AllianceCreate(), Event\AllianceCreate::CREATE_SUCCESS);

        return $alliance;
    }

    public function remove(int $id): bool
    {
        $removed = $this->repository->remove($id);

        $this->repository->deleteRanks($id);
        $this->repository->deleteDiplomacies($id);

        $this->allianceHistoryRepository->removeForAlliance($id);

        return $removed;
    }
}
