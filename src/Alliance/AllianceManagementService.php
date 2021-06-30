<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use AllianceBuildList;
use AllianceTechlist;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Message\MessageRepository;
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
    private ConfigurationService $config;
    private MessageRepository $messageRepository;
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
        ConfigurationService $config,
        MessageRepository $messageRepository,
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
        $this->config = $config;
        $this->messageRepository = $messageRepository;
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

    public function dismissApplication(int $allianceId, int $userId, ?string $applicationAnswerText): void
    {
        $user = $this->userRepository->getUser($userId);
        if ($user === null) {
            return;
        }

        $this->messageRepository->createSystemMessage($userId, MSG_ALLYMAIL_CAT, "Bewerbung abgelehnt", "Deine Allianzbewerbung wurde abgelehnt!\n\n[b]Antwort:[/b]\n" . $applicationAnswerText);

        $this->historyRepository->addEntry($allianceId, "Die Bewerbung von [b]" . $user->nick . "[/b] wurde abgelehnt!");

        $this->applicationRepository->removeForAllianceAndUser($allianceId, $userId);
    }

    public function addMember(int $allianceId, int $userId, ?string $applicationAnswerText = null): bool
    {
        if ($this->repository->hasUser($allianceId, $userId)) {
            return false;
        }

        $maxMemberCount = $this->config->getInt("alliance_max_member_count");
        if ($maxMemberCount > 0 && $this->memberCount > $maxMemberCount) {
            return false;
        }

        $alliance = $this->repository->getAlliance($allianceId);
        if ($alliance === null) {
            return false;
        }

        $user = $this->userRepository->getUser($userId);
        if ($user === null) {
            return false;
        }

        $this->repository->addUser($allianceId, $userId);

        $fromApplication = $this->applicationRepository->removeForAllianceAndUser($allianceId, $userId);

        if ($fromApplication) {
            $this->messageRepository->createSystemMessage($userId, MSG_ALLYMAIL_CAT, "Bewerbung angenommen", "Deine Allianzbewerbung wurde angenommen!\n\n[b]Antwort:[/b]\n" . $applicationAnswerText);
            $this->allianceHistoryRepository->addEntry($allianceId, "Die Bewerbung von [b]" . $user->nick . "[/b] wurde akzeptiert!");

            Log::add(5, Log::INFO, "Der Spieler [b]" . $user->nick . "[/b] tritt der Allianz [b]" . $alliance->toString() . "[/b] bei!");
            $this->userLogRepository->add($user, "alliance", "{nick} ist nun ein Mitglied der Allianz " . $alliance->toString() . ".");
        } else {
            $this->messageRepository->createSystemMessage($userId, MSG_ALLYMAIL_CAT, "Allianzaufnahme", "Du wurdest in die Allianz [b]" . $alliance->toString() . "[/b] aufgenommen!");
            $this->historyRepository->addEntry($allianceId, "[b]" . $user->nick . "[/b] wurde als neues Mitglied aufgenommen");
        }

        $this->calcMemberCosts($allianceId);

        return true;
    }

    public function kickMember(int $allianceId, int $userId, $kicked = true): bool
    {
        $alliance = $this->repository->getAlliance($allianceId);
        if ($alliance === null) {
            return false;
        }

        $user = $this->userRepository->getUser($userId);
        if ($user === null) {
            return false;
        }

        if ($this->repository->isAtWar($allianceId)) {
            return false;
        }

        $res = dbquery("SELECT id FROM fleet WHERE user_id='" . $userId . "' AND (action='alliance' OR action='support') LIMIT 1;");
        if (mysql_num_rows($res) != 0) {
            return false;
        }

        $this->repository->removeUser($userId, true);
        if ($kicked) {
            $this->messageRepository->createSystemMessage($userId, MSG_ALLYMAIL_CAT, "Allianzausschluss", "Du wurdest aus der Allianz [b]" . $alliance->toString() . "[/b] ausgeschlossen!");
        } else {
            $this->messageRepository->createSystemMessage($alliance->founderId, MSG_ALLYMAIL_CAT, "Allianzaustritt", "Der Spieler " . $user->nick . " trat aus der Allianz aus!");
        }

        $this->historyRepository->addEntry($allianceId, "[b]" . $this->members[$userId] . "[/b] ist nun kein Mitglied mehr von uns.");

        return true;
    }

    public function calcMemberCosts(int $allianceId, bool $save = true, int $addMembers = 1): string
    {
        // Zählt aktuelle Memberanzahl und und läd den Wert, für welche Anzahl User die Allianzobjekte gebaut wurden
        $members = $this->repository->findUsers($allianceId);
        $newMemberCnt = count($members) + $addMembers;
        if ($save) {
            $newMemberCnt--;
        }

        // Allianzrohstoffe anpassen, wenn die Allianzobjekte nicht für diese Anzahl ausgebaut sind

        //Aktuelle, neue und zu zahlende Kosten
        $costs = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $newCosts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $toPay = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

        // Berechnet Kostendifferenz

        $buildlist = new AllianceBuildList($allianceId);
        $buildingIterator = $buildlist->getIterator();

        while ($buildingIterator->valid()) {
            if ($buildlist->getMemberFor($buildingIterator->key()) < $newMemberCnt) {
                // Wenn ein Gebäude in Bau ist, wird die Stufe zur berechnung bereits erhöht
                $level = $buildlist->getLevel($buildingIterator->key());
                if ($buildlist->isUnderConstruction($buildingIterator->key())) {
                    $level++;
                }

                // Berechnungen nur durchführen, wenn die Stufe >0 ist oder sich das Objekt in Bau befindet
                // Dies ist eine Sicherheit für den Fall, dass die Stufe manuel zurückgesetzt wird. Es würden falsche Kosten entstehen
                if ($level > 0 || $buildlist->isUnderConstruction($buildingIterator->key())) {
                    // Kosten von jedem Level des Gebäudes wird berechnet
                    for ($x = 1; $x <= $level; $x++) {
                        $buildCosts = $buildingIterator->current()->getCosts($x, $buildlist->getMemberFor($buildingIterator->key()));

                        foreach ($buildCosts as $rid => $cost) {
                            $costs[$rid] += $cost;
                        }

                        $buildCosts = $buildingIterator->current()->getCosts($x, $newMemberCnt);

                        foreach ($buildCosts as $rid => $cost) {
                            $newCosts[$rid] += $cost;
                        }
                    }
                }
            }
            $buildingIterator->next();
        }
        if ($save) {
            $this->buildingRepository->setMemberCountIfHigher($allianceId, $newMemberCnt);
        }

        $techlist = new AllianceTechlist($allianceId);
        $techIterator = $techlist->getIterator();

        while ($techIterator->valid()) {
            if ($techlist->getMemberFor($techIterator->key()) < $newMemberCnt) {
                // Wenn eine Techin Bau ist, wird die Stufe zur berechnung bereits erhöht
                $level = $techlist->getLevel($techIterator->key());
                if ($techlist->isUnderConstruction($techIterator->key())) {
                    $level++;
                }

                // Berechnungen nur durchführen, wenn die Stufe >0 ist oder sich das Objekt in Bau befindet
                // Dies ist eine Sicherheit für den Fall, dass die Stufe manuel zurückgesetzt wird. Es würden falsche Kosten entstehen
                if ($level > 0 || $techlist->isUnderConstruction($techIterator->key())) {
                    // Kosten von jedem Level des Gebäudes wird berechnet
                    for ($x = 1; $x <= $level; $x++) {
                        $buildCosts = $techIterator->current()->getCosts($x, $techlist->getMemberFor($techIterator->key()));

                        foreach ($buildCosts as $rid => $cost) {
                            $costs[$rid] += $cost;
                        }

                        $buildCosts = $techIterator->current()->getCosts($x, $newMemberCnt);

                        foreach ($buildCosts as $rid => $cost) {
                            $newCosts[$rid] += $cost;
                        }
                    }
                }
            }
            $techIterator->next();
        }
        if ($save) {
            $this->technologyRepository->setMemberCountIfHigher($allianceId, $newMemberCnt);
        }

        // Berechnet die zu zahlenden Rohstoffe
        foreach ($costs as $rid => $cost) {
            $toPay[$rid] = $newCosts[$rid] - $cost;
        }

        if ($save) {
            // Zieht Rohstoffe vom Allianzkonto ab und speichert Anzahl Members, für welche nun bezahlt ist
            if (array_sum($toPay) > 0) {
                $this->repository->addResources(
                    $allianceId,
                    -$toPay[1],
                    -$toPay[2],
                    -$toPay[3],
                    -$toPay[4],
                    -$toPay[5]
                );
                $this->repository->setObjectsForMembers($allianceId, $newMemberCnt);

                $this->historyRepository->addEntry($allianceId, "Dem Allianzkonto wurden folgende Rohstoffe abgezogen:\n[b]" . RES_METAL . "[/b]: " . nf($toPay[1]) . "\n[b]" . RES_CRYSTAL . "[/b]: " . nf($toPay[2]) . "\n[b]" . RES_PLASTIC . "[/b]: " . nf($toPay[3]) . "\n[b]" . RES_FUEL . "[/b]: " . nf($toPay[4]) . "\n[b]" . RES_FOOD . "[/b]: " . nf($toPay[5]) . "\n\nDie Allianzobjekte sind nun für " . $newMemberCnt . " Mitglieder verfügbar!");
            }
        }

        return text2html("Bei der Aufnahme von " . $addMembers . " Member werden dem Allianzkonto folgende Rohstoffe abgezogen:\n[b]" . RES_METAL . "[/b]: " . nf($toPay[1]) . "\n[b]" . RES_CRYSTAL . "[/b]: " . nf($toPay[2]) . "\n[b]" . RES_PLASTIC . "[/b]: " . nf($toPay[3]) . "\n[b]" . RES_FUEL . "[/b]: " . nf($toPay[4]) . "\n[b]" . RES_FOOD . "[/b]: " . nf($toPay[5]));
    }
}
