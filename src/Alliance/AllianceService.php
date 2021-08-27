<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Alliance\Board\AllianceBoardCategoryRepository;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\User\User;
use EtoA\User\UserRepository;
use EtoA\User\UserService;

class AllianceService
{
    private AllianceRepository $repository;
    private UserRepository $userRepository;
    private AllianceHistoryRepository $allianceHistoryRepository;
    private UserService $userService;
    private AllianceDiplomacyRepository $allianceDiplomacyRepository;
    private AllianceBoardCategoryRepository $allianceBoardCategoryRepository;
    private AllianceApplicationRepository $allianceApplicationRepository;
    private AllianceBoardTopicRepository $allianceBoardTopicRepository;
    private AllianceBuildingRepository $allianceBuildingRepository;
    private AlliancePointsRepository $alliancePointsRepository;
    private AllianceNewsRepository $allianceNewsRepository;
    private AlliancePollRepository $alliancePollRepository;
    private AllianceRankRepository $allianceRankRepository;
    private AllianceSpendRepository $allianceSpendRepository;
    private AllianceTechnologyRepository $allianceTechnologyRepository;
    private LogRepository $logRepository;

    public function __construct(AllianceRepository $repository, UserRepository $userRepository, AllianceHistoryRepository $allianceHistoryRepository, UserService $userService, AllianceDiplomacyRepository $allianceDiplomacyRepository, AllianceBoardCategoryRepository $allianceBoardCategoryRepository, AllianceApplicationRepository $allianceApplicationRepository, AllianceBoardTopicRepository $allianceBoardTopicRepository, AllianceBuildingRepository $allianceBuildingRepository, AlliancePointsRepository $alliancePointsRepository, AllianceNewsRepository $allianceNewsRepository, AlliancePollRepository $alliancePollRepository, AllianceRankRepository $allianceRankRepository, AllianceSpendRepository $allianceSpendRepository, AllianceTechnologyRepository $allianceTechnologyRepository, LogRepository $logRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->allianceHistoryRepository = $allianceHistoryRepository;
        $this->userService = $userService;
        $this->allianceDiplomacyRepository = $allianceDiplomacyRepository;
        $this->allianceBoardCategoryRepository = $allianceBoardCategoryRepository;
        $this->allianceApplicationRepository = $allianceApplicationRepository;
        $this->allianceBoardTopicRepository = $allianceBoardTopicRepository;
        $this->allianceBuildingRepository = $allianceBuildingRepository;
        $this->alliancePointsRepository = $alliancePointsRepository;
        $this->allianceNewsRepository = $allianceNewsRepository;
        $this->alliancePollRepository = $alliancePollRepository;
        $this->allianceRankRepository = $allianceRankRepository;
        $this->allianceSpendRepository = $allianceSpendRepository;
        $this->allianceTechnologyRepository = $allianceTechnologyRepository;
        $this->logRepository = $logRepository;
    }

    public function create(string $tag, string $name, ?int $founderId): Alliance
    {
        if (!filled($name) || !filled($tag)) {
            throw new InvalidAllianceParametersException("Name/Tag fehlt!");
        }
        $name = trim($name);
        $tag = trim($tag);

        if (!preg_match('/^[^\'\"\?\<\>\$\!\=\;\&\\\\[\]]{1,6}$/i', $tag) > 0) {
            throw new InvalidAllianceParametersException("Ungültiger Tag! Die Länge muss zwischen 3 und 6 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]\\\\");
        }

        if (!preg_match('/([^\'\"\?\<\>\$\!\=\;\&\\\\[\]]{4,25})$/', $name) > 0) {
            throw new InvalidAllianceParametersException("Ungültiger Name! Die Länge muss zwischen 4 und 25 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]\\\\");
        }

        if ($founderId === null || $founderId <= 0) {
            throw new InvalidAllianceParametersException("Allianzgründer-ID fehlt!");
        }
        $founder = $this->userRepository->getUser($founderId);
        if ($founder === null) {
            throw new InvalidAllianceParametersException("Allianzgründer fehlt!");
        }

        if ($this->repository->exists($tag, $name)) {
            throw new InvalidAllianceParametersException("Eine Allianz mit diesem Tag oder Namen existiert bereits!");
        }

        $id = $this->repository->create($tag, $name, $founderId);
        $alliance = $this->repository->getAlliance($id);

        $this->userRepository->setAllianceId($founderId, $id);

        $this->userService->addToUserLog($founderId, "alliance", "{nick} hat die Allianz [b]" . $alliance->toString() . "[/b] gegründet.");
        $this->allianceHistoryRepository->addEntry($id, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde von [b]" . $founder->nick . "[/b] gegründet!");

        return $alliance;
    }

    public function delete(Alliance $alliance, User $user = null): bool
    {
        if (!$this->allianceDiplomacyRepository->isAtWar($alliance->id)) {
            $this->allianceBoardCategoryRepository->deleteAllCategories($alliance->id);
            $this->allianceApplicationRepository->deleteAllianceApplication($alliance->id);
            $diplomacies = $this->allianceDiplomacyRepository->getDiplomacies($alliance->id);
            foreach ($diplomacies as $diplomacy) {
                $this->allianceBoardTopicRepository->deleteBndTopic($diplomacy->id);
            }

            $this->allianceDiplomacyRepository->deleteAllianceDiplomacies($alliance->id);

            $this->allianceBuildingRepository->removeForAlliance($alliance->id);
            $this->allianceHistoryRepository->removeForAlliance($alliance->id);
            $this->alliancePointsRepository->removeForAlliance($alliance->id);
            $this->allianceNewsRepository->deleteAllianceEntries($alliance->id);
            $this->alliancePollRepository->deleteAllianceEntries($alliance->id);
            $this->allianceRankRepository->deleteAllianceRanks($alliance->id);
            $this->allianceSpendRepository->deleteAllianceEntries($alliance->id);
            $this->allianceTechnologyRepository->removeForAlliance($alliance->id);

            $this->repository->resetMother($alliance->id);

            // Set user alliance link to null
            $this->userRepository->resetAllianceId($alliance->id);

            // Daten löschen
            $this->repository->remove($alliance->id);

            //Log schreiben
            if ($user !== null) {
                $this->userService->addToUserLog($user->id, "alliance", "{nick} löst die Allianz [b]" . $alliance->nameWithTag . "[/b] auf.");
                $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Die Allianz [b]" . $alliance->nameWithTag . "[/b] wurde von " . $user->nick . " aufgelöst!");
            } else {
                $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Die Allianz [b]" . $alliance->nameWithTag . "[/b] wurde gelöscht!");
            }

            return true;
        }

        return false;
    }
}
