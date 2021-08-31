<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Alliance\Board\AllianceBoardCategoryRepository;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageRepository;
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
    private MessageRepository $messageRepository;
    private ConfigurationService $config;
    private AllianceMemberCosts $allianceMemberCosts;
    private FleetRepository $fleetRepository;
    private AllianceRightRepository $allianceRightRepository;

    public function __construct(AllianceRepository $repository, UserRepository $userRepository, AllianceHistoryRepository $allianceHistoryRepository, UserService $userService, AllianceDiplomacyRepository $allianceDiplomacyRepository, AllianceBoardCategoryRepository $allianceBoardCategoryRepository, AllianceApplicationRepository $allianceApplicationRepository, AllianceBoardTopicRepository $allianceBoardTopicRepository, AllianceBuildingRepository $allianceBuildingRepository, AlliancePointsRepository $alliancePointsRepository, AllianceNewsRepository $allianceNewsRepository, AlliancePollRepository $alliancePollRepository, AllianceRankRepository $allianceRankRepository, AllianceSpendRepository $allianceSpendRepository, AllianceTechnologyRepository $allianceTechnologyRepository, LogRepository $logRepository, MessageRepository $messageRepository, ConfigurationService $config, AllianceMemberCosts $allianceMemberCosts, FleetRepository $fleetRepository, AllianceRightRepository $allianceRightRepository)
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
        $this->messageRepository = $messageRepository;
        $this->config = $config;
        $this->allianceMemberCosts = $allianceMemberCosts;
        $this->fleetRepository = $fleetRepository;
        $this->allianceRightRepository = $allianceRightRepository;
    }

    public function create(string $tag, string $name, ?int $founderId): AllianceWithMemberCount
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

    public function addMember(AllianceWithMemberCount $alliance, User $user): bool
    {
        if ($alliance->id === $user->allianceId) {
            return false;
        }

        $newMemberCount = $alliance->memberCount + 1;
        $maxMemberCount = $this->config->getInt("alliance_max_member_count");
        if ($maxMemberCount > 0 && $newMemberCount + 1 > $maxMemberCount) {
            return false;
        }

        $this->messageRepository->createSystemMessage($user->id, MSG_ALLYMAIL_CAT, "Allianzaufnahme", "Du wurdest in die Allianz [b]" . $alliance->nameWithTag . "[/b] aufgenommen!");
        $this->allianceHistoryRepository->addEntry($alliance->id, "[b]" . $user->nick . "[/b] wurde als neues Mitglied aufgenommen");
        $this->allianceMemberCosts->increase($alliance->id, $alliance->memberCount, $newMemberCount);
        $this->userRepository->setAllianceId($user->id, $alliance->id);
        if ($user->allianceId > 0) {
            $previousAlliance = $this->repository->getAlliance($user->allianceId);
            $this->userService->addToUserLog($user->id, "alliance", "{nick} ist nun kein Mitglied mehr der Allianz [b]" . $previousAlliance->nameWithTag . "[/b].");
        }

        $alliance->memberCount++;

        return true;
    }

    public function kickMember(AllianceWithMemberCount $alliance, User $user, bool $kick = true): bool
    {
        if ($alliance->id !== $user->allianceId) {
            return false;
        }

        if ($this->allianceDiplomacyRepository->isAtWar($alliance->id)) {
            return false;
        }

        if ($this->fleetRepository->exists(FleetSearch::create()->user($user->id)->actionIn([FleetAction::ALLIANCE, FleetAction::SUPPORT]))) {
            return false;
        }

        if ($kick) {
            $this->messageRepository->createSystemMessage($user->id, MSG_ALLYMAIL_CAT, "Allianzausschluss", "Du wurdest aus der Allianz [b]" . $alliance->nameWithTag . "[/b] ausgeschlossen!");
        } else {
            $this->messageRepository->createSystemMessage($alliance->founderId, MSG_ALLYMAIL_CAT, "Allianzaustritt", "Der Spieler " . $user->nick . " trat aus der Allianz aus!");
        }

        $this->allianceHistoryRepository->addEntry($alliance->id, "[b]" . $user->nick . "[/b] ist nun kein Mitglied mehr von uns.");
        $this->userRepository->setAllianceId($user->id, $alliance->id, 0, time());
        $this->userService->addToUserLog($user->id, "alliance", "{nick} ist nun kein Mitglied mehr der Allianz " . $alliance->nameWithTag . ".");

        $alliance->memberCount--;

        return true;
    }

    public function changeFounder(Alliance $alliance, User $founder): bool
    {
        if ($alliance->id !== $founder->allianceId) {
            return false;
        }

        $this->allianceHistoryRepository->addEntry($alliance->id, "Der Spieler [b]" . $founder->nick . "[/b] wird zum Gründer befördert.");
        $this->messageRepository->createSystemMessage($founder->id, MSG_ALLYMAIL_CAT, "Gründer", "Du hast nun die Gründerrechte deiner Allianz!");
        $this->userService->addToUserLog($founder->id, "alliance", "{nick} ist nun Gründer der Allianz " . $alliance->nameWithTag);

        return true;
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

    public function getUserAlliancePermissions(Alliance $alliance, User $user): UserAlliancePermission
    {
        if ($alliance->founderId === $user->id) {
            return new UserAlliancePermission(true, []);
        }

        $userRights = [];
        $allianceRights = $this->allianceRightRepository->getRights();
        if (count($allianceRights) > 0) {
            $rightIds = $this->allianceRankRepository->getAvailableRightIds($alliance->id, $user->allianceRankId);

            foreach ($allianceRights as $right) {
                $userRights[$right->key] = in_array($right->id, $rightIds, true);
            }
        }

        return new UserAlliancePermission(false, array_keys(array_unique($userRights)));
    }
}
