<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\User\UserLogRepository;
use EtoA\User\UserRepository;
use Log;

class AllianceManagementService
{
    private AllianceRepository $repository;
    private AllianceHistoryRepository $historyRepository;
    private AllianceBoardRepository $boardRepository;
    private AllianceApplicationRepository $applicationRepository;
    private AllianceBuildingRepository $buildingRepository;
    private AllianceTechnologyRepository $technologyRepository;
    private AlliancePaymentRepository $paymentRepository;
    private AllianceNewsRepository $newsRepository;
    private AlliancePointRepository $pointRepository;
    private AlliancePollRepository $pollRepository;
    private UserRepository $userRepository;
    private UserLogRepository $userLogRepository;
    private $dispatcher;

    public function __construct(
        AllianceRepository $repository,
        AllianceHistoryRepository $historyRepository,
        AllianceBoardRepository $boardRepository,
        AllianceApplicationRepository $applicationRepository,
        AllianceBuildingRepository $buildingRepository,
        AllianceTechnologyRepository $technologyRepository,
        AlliancePaymentRepository $paymentRepository,
        AllianceNewsRepository $newsRepository,
        AlliancePointRepository $pointRepository,
        AlliancePollRepository $pollRepository,
        UserRepository $userRepository,
        UserLogRepository $userLogRepository,
        $dispatcher
    ) {
        $this->repository = $repository;
        $this->historyRepository = $historyRepository;
        $this->boardRepository = $boardRepository;
        $this->applicationRepository = $applicationRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->paymentRepository = $paymentRepository;
        $this->newsRepository = $newsRepository;
        $this->pointRepository = $pointRepository;
        $this->pollRepository = $pollRepository;
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

    public function remove(int $id, ?int $userId = null): bool
    {
        $boardCategories = $this->boardRepository->findCategoryIdsForAlliance($id);
        foreach ($boardCategories as $categoryId) {
            $this->boardRepository->removeCategoryRanksForCategory($categoryId);
            $topics = $this->boardRepository->findTopicIdsForCategory($categoryId);
            foreach ($topics as $topicId) {
                $this->boardRepository->removePostsForTopic($topicId);
            }
            $this->boardRepository->removeTopicsForCategory($categoryId);
        }
        $this->boardRepository->removeCategoriesForAlliance($id);

        $this->applicationRepository->removeForAlliance($id);

        $diplomacies = $this->repository->findDiplomacies($id);
        foreach ($diplomacies as $diplomacy) {
            $topics = $this->boardRepository->findTopicIdsForDiplomacy((int) $diplomacy['alliance_bnd_id']);
            foreach ($topics as $topicId) {
                $this->boardRepository->removePostsForTopic($topicId);
            }
            $this->boardRepository->removeTopicsForDiplomacy((int) $diplomacy['alliance_bnd_id']);
        }
        $this->repository->deleteDiplomacies($id);

        $this->buildingRepository->removeForAlliance($id);
        $this->technologyRepository->removeForAlliance($id);
        $this->paymentRepository->removeForAlliance($id);
        $this->newsRepository->removeForAlliance($id);
        $this->pointRepository->removeForAlliance($id);
        $this->pollRepository->removeForAlliance($id);

        $ranks = $this->repository->findRanks($id);
        foreach ($ranks as $rank) {
            $this->repository->removeRank((int) $rank['rank_id']);
        }
        $this->repository->deleteRanks($id);

        $this->repository->detachWings($id);

        $this->repository->removeAllUsers($id);

        $alliance = $this->repository->getAlliance($id);

        $removed = $this->repository->remove($id);

        if ($alliance !== null) {
            $user = $userId !== null ? $this->userRepository->getUser($userId) : null;
            if ($user !== null) {
                $this->userLogRepository->add($user, "alliance", "{nick} löst die Allianz [b]" . $alliance->toString() . "[/b] auf.");
                Log::add(Log::F_ALLIANCE, Log::INFO, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde von " . $user->nick . " aufgelöst!");
            } else {
                Log::add(Log::F_ALLIANCE, Log::INFO, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde gelöscht!");
            }
        }

        $this->historyRepository->removeForAlliance($id);

        return $removed;
    }
}
