<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Alliance\Board\AllianceBoardCategoryRepository;
use EtoA\Alliance\Board\AllianceBoardPostRepository;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceBuilding;
use EtoA\Entity\AllianceTechnology;
use EtoA\Entity\User;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use EtoA\Support\ValidationUtils;

class AllianceService
{
    public function __construct(
        private readonly AllianceRepository $repository,
        private readonly UserRepository $userRepository,
        private readonly AllianceHistoryRepository $allianceHistoryRepository,
        private readonly UserService $userService,
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly AllianceApplicationRepository $allianceApplicationRepository,
        private readonly AllianceBoardPostRepository $allianceBoardPostRepository,
        private readonly AlliancePollRepository $alliancePollRepository,
        private readonly LogRepository $logRepository,
        private readonly MessageRepository $messageRepository,
        private readonly ConfigurationService $config,
        private readonly FleetRepository $fleetRepository,
        private readonly AllianceRightRepository $allianceRightRepository,
        private readonly Security $security,
        private readonly UrlGeneratorInterface $router,
        private readonly AllianceRankRightRepository    $allianceRankRightRepository,
        private readonly MessageCategoryRepository $messageCategoryRepository,
    )
    {}

    public function create(string $tag, string $name, ?User $founder): Alliance
    {
        if (!ValidationUtils::filled($name) || !ValidationUtils::filled($tag)) {
            throw new InvalidAllianceParametersException("Name/Tag fehlt!");
        }
        $name = trim($name);
        $tag = trim($tag);

        if (!preg_match('/^[^\'\"?<>$!=;&\\\\[\]]{1,6}$/i', $tag) > 0) {
            throw new InvalidAllianceParametersException("Ungültiger Tag! Die Länge muss zwischen 3 und 6 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]\\\\");
        }

        if (!preg_match('/([^\'\"?<>$!=;&\\\\[\]]{4,25})$/', $name) > 0) {
            throw new InvalidAllianceParametersException("Ungültiger Name! Die Länge muss zwischen 4 und 25 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]\\\\");
        }

        if ($founder === null) {
            throw new InvalidAllianceParametersException("Allianzgründer fehlt!");
        }

        if ($this->repository->exists($tag, $name)) {
            throw new InvalidAllianceParametersException("Eine Allianz mit diesem Tag oder Namen existiert bereits!");
        }

        $alliance = $this->repository->create($tag, $name, $founder);
        $this->userRepository->setAlliance($founder, $alliance);
        $this->userService->addToUserLog($founder, "alliance", "{nick} hat die Allianz [b]" . $alliance->toString() . "[/b] gegründet.");
        $this->allianceHistoryRepository->addEntry($alliance, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde von [b]" . $founder->getNick() . "[/b] gegründet!");

        return $alliance;
    }

    public function checkRename(Alliance $alliance):bool
    {
        $tag = $alliance->getTag();
        $name =$alliance->getName();

        if (!preg_match('/^[^\'\"?<>$!=;&\\\\[\]]{1,6}$/i', $tag) > 0) {
            throw new InvalidAllianceParametersException("Ungültiger Tag! Die Länge muss zwischen 3 und 6 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]\\\\");
        }

        if (!preg_match('/([^\'\"?<>$!=;&\\\\[\]]{4,25})$/', $name) > 0) {
            throw new InvalidAllianceParametersException("Ungültiger Name! Die Länge muss zwischen 4 und 25 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]\\\\");
        }

        if ($this->repository->exists($tag, $name, $alliance->getId())) {
            throw new InvalidAllianceParametersException("Eine Allianz mit diesem Tag oder Namen existiert bereits!");
        }

        return true;
    }

/*
    public function addMember(AllianceWithMemberCount $alliance, User $user): bool
    {
        if ($alliance->getId() === $user->getAlliance()->getId()) {
            return false;
        }

        $newMemberCount = $alliance->memberCount + 1;
        $maxMemberCount = $this->config->getInt("alliance_max_member_count");
        if ($maxMemberCount > 0 && $newMemberCount + 1 > $maxMemberCount) {
            return false;
        }

        $this->messageRepository->createSystemMessage($user->getId(), MessageCategoryId::ALLIANCE, "Allianzaufnahme", "Du wurdest in die Allianz [b]" . $alliance->toString() . "[/b] aufgenommen!");
        $this->allianceHistoryRepository->addEntry($alliance->getId(), "[b]" . $user->getNick() . "[/b] wurde als neues Mitglied aufgenommen");
        $this->allianceMemberCosts->increase($alliance->getId(), $alliance->memberCount, $newMemberCount);
        $this->userRepository->setAllianceId($user->getId(), $alliance->getId());
        if ($user->getAlliance()->getId() > 0) {
            $previousAlliance = $this->repository->getAlliance($user->getAlliance()->getId());
            $this->userService->addToUserLog($user->getId(), "alliance", "{nick} ist nun kein Mitglied mehr der Allianz [b]" . $previousAlliance->toString() . "[/b].");
        }

        $alliance->memberCount++;

        return true;
    }
*/
    public function kickMember(Alliance $alliance, User $user, bool $kick = true): bool
    {
        if ($alliance !== $user->getAlliance()) {
            return false;
        }

        if($this->security->getUser()->getData() === $alliance->getFounder()) {
            return false;
        }

        if ($this->allianceDiplomacyRepository->isAtWar($alliance)) {
            return false;
        }

        if ($this->fleetRepository->exists(FleetSearch::create()->user($user)->actionIn([FleetAction::ALLIANCE, FleetAction::SUPPORT]))) {
            return false;
        }

        if ($kick) {
            $this->messageRepository->createSystemMessage($user, $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Allianzausschluss", "Du wurdest aus der Allianz [b]" . $alliance->toString() . "[/b] ausgeschlossen!");
        } else {
            $this->messageRepository->createSystemMessage($alliance->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Allianzaustritt", "Der Spieler " . $user->getNick() . " trat aus der Allianz aus!");
        }

        $this->allianceHistoryRepository->addEntry($alliance, "[b]" . $user->getNick() . "[/b] ist nun kein Mitglied mehr von uns.");

        $user->setAlliance(null);
        $user->setAllianceRank(null);
        $user->setAllianceLeave(time());

        $this->userRepository->save();

        $this->userService->addToUserLog($user, "alliance", "{nick} ist nun kein Mitglied mehr der Allianz " . $alliance->toString() . ".");

        return true;
    }

    public function changeFounder(Alliance $alliance, User $founder): bool
    {
        if ($alliance !== $founder->getAlliance()) {
            return false;
        }

        $alliance->setFounder($founder);

        $this->allianceHistoryRepository->addEntry($alliance, "Der Spieler [b]" . $founder->getNick() . "[/b] wird zum Gründer befördert.");
        $this->messageRepository->createSystemMessage($founder, $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Gründer", "Du hast nun die Gründerrechte deiner Allianz!");
        $this->userService->addToUserLog($founder, "alliance", "{nick} ist nun Gründer der Allianz " . $alliance->toString());

        return true;
    }

    public function delete(Alliance $alliance, User $user = null): bool
    {
        if (!$this->allianceDiplomacyRepository->isAtWar($alliance)) {
            if ($user) {
                $this->userService->addToUserLog($user, "alliance", "{nick} löst die Allianz [b]" . $alliance->toString() . "[/b] auf.");
                $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde von " . $user->getNick() . " aufgelöst!");
            } else {
                $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde gelöscht!");
            }

            // Daten löschen
            $this->repository->remove($alliance);

            $this->repository->save();


            return true;
        }

        return false;
    }

    public function getUserAlliancePermissions(Alliance $alliance, User $user): UserAlliancePermission
    {
        if ($alliance->getFounder() === $user) {
            return new UserAlliancePermission(true, []);
        }

        $userRights = [];
        if ($this->allianceRightRepository->findAll()) {
            $rankRights = $this->allianceRankRightRepository->findBy(['rank'=>$user->getAllianceRank()]);

            foreach ($rankRights as $rankRight) {
                $userRights[] = $rankRight->getRight()->getKey();
            }
        }

        return new UserAlliancePermission(false, $userRights);
    }

    public function getAveragePoints(Alliance $alliance):float
    {
        $memberCount = $this->userRepository->count(['alliance'=>$alliance->getId()]);
        if ($memberCount > 0) {
            return floor($alliance->getPoints() / $memberCount);
        }

        return 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOverviewData(Alliance $alliance): array
    {
        $cu = $this->security->getUser()->getData();
        $userAlliancePermission = $this->getUserAlliancePermissions($alliance, $cu);
        $myRankId = $cu->getAllianceRank()?->getId();
        $isFounder = $alliance->getFounder() == $cu;

        $latestPost = $userAlliancePermission->hasRights(AllianceRights::ALLIANCE_BOARD)
            ? $this->allianceBoardPostRepository->getLatestAlliancePost($alliance->getId())
            : $this->allianceBoardPostRepository->getLatestAlliancePost($alliance->getId(), $myRankId);

        $allowWings = $this->config->getBoolean('allow_wings');
        $warDuration = 3600 * $this->config->getInt('alliance_war_time');

        $adminLinks = [];
        if ($userAlliancePermission->hasRights(AllianceRights::VIEW_MEMBERS)) {
            $adminLinks["Mitglieder anzeigen"] = $this->router->generate('game.alliance.members');
        }
        $adminLinks["Allianzbasis"] = $this->router->generate('game.alliance.base.buildings');
        if ($allowWings && $userAlliancePermission->hasRights(AllianceRights::WINGS)) {
            $adminLinks["Wings verwalten"] = "?page=&action=wings";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::HISTORY)) {
            $adminLinks["Geschichte"] = $this->router->generate('game.alliance.history');
        }
        if ($userAlliancePermission->hasRights(AllianceRights::ALLIANCE_NEWS)) {
            $adminLinks["Allianznews (Rathaus)"] = $this->router->generate('game.alliance.news');
        }
        if ($userAlliancePermission->hasRights(AllianceRights::RELATIONS)) {
            $adminLinks["Diplomatie"] = $this->router->generate('game.alliance.diplomacy.relations');
        }
        if ($userAlliancePermission->hasRights(AllianceRights::POLLS)) {
            $adminLinks["Umfragen verwalten"] = $this->router->generate('game.alliance.polls.overview');
        }
        if ($userAlliancePermission->hasRights(AllianceRights::MASS_MAIL)) {
            $adminLinks["Rundmail"] = $this->router->generate('game.alliance.massmail');
        }
        if ($userAlliancePermission->hasRights(AllianceRights::EDIT_MEMBERS)) {
            $adminLinks["Mitglieder verwalten"] = $this->router->generate('game.alliance.editmembers');
        }
        if ($userAlliancePermission->hasRights(AllianceRights::RANKS)) {
            $adminLinks["Ränge"] = $this->router->generate('game.alliance.ranks');
        }
        if ($userAlliancePermission->hasRights(AllianceRights::EDIT_DATA)) {
            $adminLinks["Allianz-Daten bearbeiten"] = $this->router->generate('game.alliance.edit');
        }
        if ($userAlliancePermission->hasRights(AllianceRights::APPLICATION_TEMPLATE)) {
            $adminLinks["Bewerbungsvorlage"] = $this->router->generate('game.alliance.applicationtemplate');
        }
        if ($isFounder && !$this->allianceDiplomacyRepository->isAtWar($alliance)) {
            $adminLinks["Allianz auflösen"] = $this->router->generate('game.alliance.disband');
        }
        if (!$isFounder && !$this->allianceDiplomacyRepository->isAtWar($alliance)) {
            $adminLinks["Allianz verlassen"] = $this->router->generate('game.alliance.leave');
        }

        return [
            'alliance' => $alliance,
            'permission' => $userAlliancePermission,
            'isFounder' => $isFounder,
            'latestPost' => $latestPost,
            'polls' => $this->alliancePollRepository->getPolls($alliance, 2),
            'applicationsCount' => $userAlliancePermission->hasRights(AllianceRights::APPLICATIONS)
                ? $this->allianceApplicationRepository->countApplications($alliance->getId())
                : 0,
            'hasWingRequest' => $allowWings
                && $userAlliancePermission->hasRights(AllianceRights::WINGS)
                && $alliance->getMotherRequest() !== null,
            'hasPendingBndRequests' => $userAlliancePermission->hasRights(AllianceRights::RELATIONS)
                && $this->allianceDiplomacyRepository->hasPendingBndRequests($alliance->getId()),
            'warDeclaredRecently' => $this->allianceDiplomacyRepository->wasWarDeclaredAgainstSince($alliance->getId(), time() - 192600),
            'adminLinks' => $adminLinks,
            'historyEntries' => $userAlliancePermission->hasRights(AllianceRights::HISTORY)
                ? $this->allianceHistoryRepository->findForAlliance($alliance, 5)
                : [],
            'wars' => $this->resolveDiplomacies($alliance, AllianceDiplomacyLevel::WAR),
            'peace' => $this->resolveDiplomacies($alliance, AllianceDiplomacyLevel::PEACE),
            'bnds' => $this->resolveDiplomacies($alliance, AllianceDiplomacyLevel::BND_CONFIRMED),
            'warDuration' => $warDuration,
            'allowWings' => $allowWings,
            'wings' => $allowWings ? $this->repository->searchAlliances(AllianceSearch::create()->motherId($alliance->getId())) : [],
            'memberCount' => $this->userRepository->count(['alliance' => $alliance->getId()]),
            'averagePoints' => $this->getAveragePoints($alliance),
        ];
    }

    /**
     * @return array<int, array{diplomacy: \EtoA\Entity\AllianceDiplomacy, opponent: AllianceWithMemberCount}>
     */
    private function resolveDiplomacies(Alliance $alliance, int $level): array
    {
        $result = [];
        foreach ($this->allianceDiplomacyRepository->getDiplomacies($alliance, $level) as $diplomacy) {
            $opponentId = $diplomacy->getAlliance1()->getId() === $alliance->getId()
                ? $diplomacy->getAlliance2()->getId()
                : $diplomacy->getAlliance1()->getId();

            $result[] = [
                'diplomacy' => $diplomacy,
                'opponent' => $this->repository->getAlliance($opponentId),
            ];
        }

        return $result;
    }

    public function calculateCosts(AllianceBuilding|AllianceTechnology $entity, int $level, int $members, float $memberCostsFactor): BaseResources
    {
        $level = max(1, $level);
        $members = max(1, $members);

        $factor = $entity->getBuildFactor() ** ($level - 1);
        $memberLevelFactor = $factor * (1 + ($members - 1) * $memberCostsFactor);

        $costs = new BaseResources();
        $costs->metal = (int) ceil($entity->getCostsMetal() * $memberLevelFactor);
        $costs->crystal = (int) ceil($entity->getCostsCrystal() * $memberLevelFactor);
        $costs->plastic = (int) ceil($entity->getCostsPlastic() * $memberLevelFactor);
        $costs->fuel = (int) ceil($entity->getCostsFuel() * $memberLevelFactor);
        $costs->food = (int) ceil($entity->getCostsFood() * $memberLevelFactor);

        return $costs;
    }

    public function isFounder():bool
    {
        return $this->security->getUser()->getData() === $this->security->getUser()->getData()->getAlliance()->getFounder();
    }
}
