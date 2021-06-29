<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Alliance;
use EtoA\User\UserRepository;
use User;

class AllianceManagementService
{
    private AllianceRepository $repository;
    private AllianceHistoryRepository $historyRepository;
    private UserRepository $userRepository;

    public function __construct(
        AllianceRepository $repository,
        AllianceHistoryRepository $historyRepository,
        UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->historyRepository = $historyRepository;
        $this->userRepository = $userRepository;
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

        if ($this->repository->exists($tag, $name)) {
            throw new InvalidAllianceParametersException("Eine Allianz mit diesem Tag oder Namen existiert bereits!");
        }

        if ($founderId === null || $founderId <= 0) {
            throw new InvalidAllianceParametersException("Allianzgründer-ID fehlt!");
        }

        // TODO refactor
        $founder = new User($founderId);

        $id = $this->repository->add($tag, $name, $founderId);

        // TODO refactor
        $alliance = new Alliance($id);
        $founder->alliance = $alliance;
        $founder->addToUserLog("alliance", "{nick} hat die Allianz [b]" . $alliance->__toString() . "[/b] gegründet.");

        $this->historyRepository->addEntry($id, "Die Allianz [b]" . $alliance->__toString() . "[/b] wurde von [b]" . $founder . "[/b] gegründet!");

        return $id;
    }
}
