<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Alliance\Board\AllianceBoardCategoryRepository;
use EtoA\Alliance\Board\AllianceBoardPostRepository;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Alliance;
use EtoA\Entity\User;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AllianceService
{
    private AllianceRepository $repository;
    private UserRepository $userRepository;
    private AllianceHistoryRepository $allianceHistoryRepository;
    private UserService $userService;
    private AllianceDiplomacyRepository $allianceDiplomacyRepository;
    private AllianceBoardCategoryRepository $allianceBoardCategoryRepository;
    private AllianceBoardPostRepository $allianceBoardPostRepository;
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
    private Security $security;
    private UrlGeneratorInterface $router;
    public function __construct(
        AllianceRepository $repository,
        UserRepository $userRepository,
        AllianceHistoryRepository $allianceHistoryRepository,
        UserService $userService,
        AllianceDiplomacyRepository $allianceDiplomacyRepository,
        AllianceBoardCategoryRepository $allianceBoardCategoryRepository,
        AllianceApplicationRepository $allianceApplicationRepository,
        AllianceBoardTopicRepository $allianceBoardTopicRepository,
        AllianceBoardPostRepository $allianceBoardPostRepository,
        AllianceBuildingRepository $allianceBuildingRepository,
        AlliancePointsRepository $alliancePointsRepository,
        AllianceNewsRepository $allianceNewsRepository,
        AlliancePollRepository $alliancePollRepository,
        AllianceRankRepository $allianceRankRepository,
        AllianceSpendRepository $allianceSpendRepository,
        AllianceTechnologyRepository $allianceTechnologyRepository,
        LogRepository $logRepository,
        MessageRepository $messageRepository,
        ConfigurationService $config,
        AllianceMemberCosts $allianceMemberCosts,
        FleetRepository $fleetRepository,
        AllianceRightRepository $allianceRightRepository,
        Security $security,
        UrlGeneratorInterface $router,
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->allianceHistoryRepository = $allianceHistoryRepository;
        $this->userService = $userService;
        $this->allianceDiplomacyRepository = $allianceDiplomacyRepository;
        $this->allianceBoardCategoryRepository = $allianceBoardCategoryRepository;
        $this->allianceBoardPostRepository = $allianceBoardPostRepository;
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
        $this->security =$security;
        $this->router = $router;
    }

    public function create(string $tag, string $name, ?int $founderId): AllianceWithMemberCount
    {
        if (!filled($name) || !filled($tag)) {
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
        $this->allianceHistoryRepository->addEntry($id, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde von [b]" . $founder->getNick() . "[/b] gegründet!");

        return $alliance;
    }

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

    public function kickMember(AllianceWithMemberCount $alliance, User $user, bool $kick = true): bool
    {
        if ($alliance->getId() !== $user->getAlliance()->getId()) {
            return false;
        }

        if ($this->allianceDiplomacyRepository->isAtWar($alliance->getId())) {
            return false;
        }

        if ($this->fleetRepository->exists(FleetSearch::create()->user($user->getId())->actionIn([FleetAction::ALLIANCE, FleetAction::SUPPORT]))) {
            return false;
        }

        if ($kick) {
            $this->messageRepository->createSystemMessage($user->getId(), MessageCategoryId::ALLIANCE, "Allianzausschluss", "Du wurdest aus der Allianz [b]" . $alliance->toString() . "[/b] ausgeschlossen!");
        } else {
            $this->messageRepository->createSystemMessage($alliance->getFounderId(), MessageCategoryId::ALLIANCE, "Allianzaustritt", "Der Spieler " . $user->getNick() . " trat aus der Allianz aus!");
        }

        $this->allianceHistoryRepository->addEntry($alliance->getId(), "[b]" . $user->getNick() . "[/b] ist nun kein Mitglied mehr von uns.");
        $this->userRepository->setAllianceId($user->getId(), $alliance->getId(), 0, time());
        $this->userService->addToUserLog($user->getId(), "alliance", "{nick} ist nun kein Mitglied mehr der Allianz " . $alliance->toString() . ".");

        $alliance->memberCount--;

        return true;
    }

    public function changeFounder(Alliance $alliance, User $founder): bool
    {
        if ($alliance->getId() !== $founder->getAlliance()->getId()) {
            return false;
        }

        $this->allianceHistoryRepository->addEntry($alliance->getId(), "Der Spieler [b]" . $founder->getNick() . "[/b] wird zum Gründer befördert.");
        $this->messageRepository->createSystemMessage($founder->getId(), MessageCategoryId::ALLIANCE, "Gründer", "Du hast nun die Gründerrechte deiner Allianz!");
        $this->userService->addToUserLog($founder->getId(), "alliance", "{nick} ist nun Gründer der Allianz " . $alliance->toString());

        return true;
    }

    public function delete(Alliance $alliance, User $user = null): bool
    {
        if (!$this->allianceDiplomacyRepository->isAtWar($alliance->getId())) {
            $this->allianceBoardCategoryRepository->deleteAllCategories($alliance->getId());
            $this->allianceApplicationRepository->deleteAllianceApplication($alliance->getId());
            $diplomacies = $this->allianceDiplomacyRepository->getDiplomacies($alliance->getId());
            foreach ($diplomacies as $diplomacy) {
                $this->allianceBoardTopicRepository->deleteBndTopic($diplomacy->getId());
            }

            $this->allianceDiplomacyRepository->deleteAllianceDiplomacies($alliance->getId());

            $this->allianceBuildingRepository->removeForAlliance($alliance->getId());
            $this->allianceHistoryRepository->removeForAlliance($alliance->getId());
            $this->alliancePointsRepository->removeForAlliance($alliance->getId());
            $this->allianceNewsRepository->deleteAllianceEntries($alliance->getId());
            $this->alliancePollRepository->deleteAllianceEntries($alliance->getId());
            $this->allianceRankRepository->deleteAllianceRanks($alliance->getId());
            $this->allianceSpendRepository->deleteAllianceEntries($alliance->getId());
            $this->allianceTechnologyRepository->removeForAlliance($alliance->getId());

            $this->repository->resetMother($alliance->getId());

            // Set user alliance link to null
            $this->userRepository->resetAllianceId($alliance->getId());

            // Daten löschen
            $this->repository->remove($alliance->getId());

            //Log schreiben
            if ($user !== null) {
                $this->userService->addToUserLog($user->getId(), "alliance", "{nick} löst die Allianz [b]" . $alliance->toString() . "[/b] auf.");
                $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde von " . $user->getNick() . " aufgelöst!");
            } else {
                $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Die Allianz [b]" . $alliance->toString() . "[/b] wurde gelöscht!");
            }

            return true;
        }

        return false;
    }

    public function getUserAlliancePermissions(Alliance $alliance, User $user): UserAlliancePermission
    {
        if ($alliance->getFounderId() === $user->getId()) {
            return new UserAlliancePermission(true, []);
        }

        $userRights = [];
        $allianceRights = $this->allianceRightRepository->findBy([],['id'=>'ASC']);
        if (count($allianceRights) > 0) {
            $rightIds = $this->allianceRankRepository->getAvailableRightIds($alliance->getId(), $user->getAllianceRankId());

            foreach ($allianceRights as $right) {
                $userRights[$right->getKey()] = in_array($right->getId(), $rightIds, true);
            }
        }

        return new UserAlliancePermission(false, array_keys(array_unique($userRights)));
    }

    public function getAveragePoints(Alliance $alliance):float
    {
        $memberCount = $this->userRepository->count(['alliance'=>$alliance->getId()]);
        if ($memberCount > 0) {
            return floor($alliance->getPoints() / $memberCount);
        }

        return 0;
    }

    //TODO: migrate to twig
    public function renderOverview(Alliance $alliance):string
    {
        ob_start();

        $cu = $this->security->getUser()->getData();
        $userAlliancePermission = $this->getUserAlliancePermissions($alliance, $cu);
        $myRankId = $cu->getAllianceRankId();
        $page = '';
        $isFounder = $alliance->getFounderId() == $cu->getId();

        echo '<table class="tb"><caption>'.$alliance->toString().'</caption>';
        if ($alliance->getImage() != "") {
            $im = $alliance->getImageUrl();
            if (file_exists($im)) {
                $ims = getimagesize($im);
                echo "<tr><td class=\"tblblack\" colspan=\"3\" style=\"text-align:center;background:#000\">
                    <img src=\"" . $im . "\" alt=\"Allianz-Logo\" style=\"width:" . $ims[0] . "px;height:" . $ims[1] . "\" /></td></tr>";
            }
        }

        // Internes Forum verlinken
        if ($userAlliancePermission->hasRights(AllianceRights::ALLIANCE_BOARD)) {
            $post = $this->allianceBoardPostRepository->getLatestAlliancePost($alliance->getId());
        } else {
            $post = $this->allianceBoardPostRepository->getLatestAlliancePost($alliance->getId(), $myRankId);
        }

        if ($post) {
            $ps = "Neuster Post: <a href=".$this->router->generate('game.alliance.allianceboard.showposts',['id'=>$post->getTopic()->getId(),'_fragment' =>$post->getId()])."><b>" . $post->getTopic()->getSubject() . "</b>, geschrieben von: <b>" . $post->getUserNick() . "</b>, <b>" . StringUtils::formatDate($post->getTopic()->getTimestamp()) . "</b></a>";
        } else
            $ps = "<i>Noch keine Beitr&auml;ge vorhanden";
        echo "<tr><th>Internes Forum</th><td colspan=\"2\"><b><a href=".$this->router->generate('game.alliance.allianceboard.overview').">Forum&uuml;bersicht</a></b> &nbsp; $ps</td></tr>";

        // Umfrage verlinken
        $polls = $this->alliancePollRepository->getPolls($alliance->getId(), 2);
        $pcnt = count($polls);
        if ($pcnt > 0) {
            echo "<tr><th>Umfrage:</th>
                <td colspan=\"2\"><a href=\"?page=$page&amp;action=viewpoll\"><b>" . stripslashes($polls[0]->getTitle()) . ":</b> " . stripslashes($polls[0]->getQuestion()) . "</a>";
            if ($pcnt > 1)
                echo " &nbsp; (<a href=\"?page=$page&amp;action=viewpoll\">mehr Umfragen</a>)";
            echo "</td></tr>";
        }

        // Bewerbungen anzeigen
        if ($userAlliancePermission->hasRights(AllianceRights::APPLICATIONS)) {
            $applications = $this->allianceApplicationRepository->countApplications($cu->getAlliance()->getId());
            if ($applications > 0) {
                echo "<tr><th colspan=\"3\">
                    <div><b><a href=\"?page=$page&action=applications\">Es sind Bewerbungen vorhanden!</a></b></div>
                    </th></tr>";
            }
        }

        // Wing-Anfrage
        if ($this->config->getBoolean('allow_wings') && ($userAlliancePermission->hasRights(AllianceRights::WINGS)) && $alliance->getMotherRequest() > 0) {
            echo "<tr><th colspan=\"3\">
                <div><b><a href=\"?page=$page&action=wings\">Es ist eine Wing-Anfrage vorhanden!</a></b></div>
                </th></tr>";
        }

        if ($this->config->getBoolean('allow_wings') && $alliance->getMotherId() !== 0) {
            $motherAlliance = $this->repository->getAlliance($alliance->getMotherId());
            echo "<tr>
                                <th colspan=\"3\" style=\"text-align:center;\">
                                    Diese Allianz ist ein Wing von <b><a href=\"?page=$page&amp;action=info&amp;id=" . $alliance->getMotherId() . "\">" . $motherAlliance->toString() . "</a></b>
                                </th>
                            </tr>";
        }


        // Bündnissanfragen anzeigen
        if ($userAlliancePermission->hasRights(AllianceRights::RELATIONS)) {
            if ($this->allianceDiplomacyRepository->hasPendingBndRequests($cu->getAlliance()->getId()))
                echo "<tr>
                        <th colspan=\"3\" style=\"text-align:center;color:#0f0\">
                            <a  style=\"color:#0f0\" href=\"?page=$page&action=relations\">Es sind B&uuml;ndnisanfragen vorhanden!</a>
                    </th></tr>";
        }

        // Kriegserklärung anzeigen
        $time = time() - 192600;
        if ($this->allianceDiplomacyRepository->wasWarDeclaredAgainstSince($cu->getAlliance()->getId(), $time)) {
            if ($userAlliancePermission->hasRights(AllianceRights::RELATIONS))
                echo "<tr>
                    <th colspan=\"3\"><b>
                        <div><a href=\"?page=$page&action=relations\">Deiner Allianz wurde in den letzten 36h der Krieg erkl&auml;rt!</a></div></b></th></tr>";
            else
                echo "<tr><th colspan=\"3\"><div><b>Deiner Allianz wurde in den letzten 36h der Krieg erkl&auml;rt!</b></div></th></tr>";
        }

        // Verwaltung
        $adminBox = array();

        if ($userAlliancePermission->hasRights(AllianceRights::VIEW_MEMBERS)) {
            $adminBox["Mitglieder anzeigen"] = "?page=$page&amp;action=viewmembers";
        }
        $adminBox["Allianzbasis"] = "?page=$page&action=base";
        if ($this->config->getBoolean('allow_wings') && $userAlliancePermission->hasRights(AllianceRights::WINGS)) {
            $adminBox["Wings verwalten"] = "?page=$page&action=wings";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::HISTORY)) {
            $adminBox["Geschichte"] = "?page=$page&action=history";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::ALLIANCE_NEWS)) {
            $adminBox["Allianznews (Rathaus)"] = "?page=$page&action=alliancenews";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::RELATIONS)) {
            $adminBox["Diplomatie"] = "?page=$page&action=relations";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::POLLS)) {
            $adminBox["Umfragen verwalten"] = "?page=$page&action=polls";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::MASS_MAIL)) {
            $adminBox["Rundmail"] = "?page=$page&action=massmail";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::EDIT_MEMBERS)) {
            $adminBox["Mitglieder verwalten"] = "?page=$page&action=editmembers";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::RANKS)) {
            $adminBox["Ränge"] = "?page=$page&action=ranks";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::EDIT_DATA)) {
            $adminBox["Allianz-Daten bearbeiten"] = "?page=$page&amp;action=editdata";
        }
        if ($userAlliancePermission->hasRights(AllianceRights::APPLICATION_TEMPLATE)) {
            $adminBox["Bewerbungsvorlage"] = "?page=$page&action=applicationtemplate";
        }
        if ($isFounder && !$this->allianceDiplomacyRepository->isAtWar($cu->getAlliance()->getId())) {
            $adminBox["Allianz aufl&ouml;sen"] = "?page=$page&action=liquidate";
            $adminBox["Allianz verlassen"] = "?page=$page&action=leave";
            //array_push($adminBox,"<a href=\"\" onclick=\"return confirm('Allianz wirklich verlassen?');\"></a>");
        }

        echo "<tr><th>Verwaltung:</th>";
        echo "<td colspan=\"2\">";
        echo "<div class=\"threeColumnList allianceManagementLinks\">";
        foreach ($adminBox as $k => $v) {
            echo "<a href=\"$v\">$k</a><br/>";
        }
        echo "</div>";
        echo "</td></tr>";


        // Letzte Ereignisse anzeigen
        if ($userAlliancePermission->hasRights(AllianceRights::HISTORY)) {
            echo "<tr>
                    <th>Letzte Ereignisse:</th>
                    <td colspan=\"2\">";

            $entries = $this->allianceHistoryRepository->findForAlliance($cu->getAlliance()->getId(), 5);
            if (count($entries) > 0) {
                foreach ($entries as $entry) {
                    echo "<div class=\"infoLog\">" . BBCodeUtils::toHTML($entry->getText()) . " <span>" . StringUtils::formatDate($entry->getTimestamp(), false) . "</span></div>";
                }
            }
            echo "</td></tr>";
        }

        // Text anzeigen
        if ($alliance->getText() != "") {
            echo "<tr><td colspan=\"3\" style=\"text-align:center\">" . BBCodeUtils::toHTML($alliance->getText()) . "</td></tr>\n";
        }

        // Kriege
        $wars = $this->allianceDiplomacyRepository->getDiplomacies($alliance->getId(), AllianceDiplomacyLevel::WAR);
        if (count($wars) > 0) {
            echo "<tr>
                                <th>Kriege:</th>
                                <td>
                                    <table class=\"tbl\">
                                        <tr>
                                            <th>Allianz</th>
                                            <th>Punkte</th>
                                            <th>Zeitraum</th>
                                        </tr>";
            foreach ($wars as $diplomacy) {
                $opAlliance = $this->repository->getAlliance($diplomacy->getAlliance2()->getId());
                echo "<tr>
                                            <td>
                                                <a href=\"?page=$page&amp;id=" . $diplomacy->getAlliance2()->getId() . "\">" . $opAlliance->toString() . "</a>
                                            </td>
                                            <td>" . StringUtils::formatNumber($opAlliance->getPoints()) . " / " . StringUtils::formatNumber($opAlliance->averagePoints) . "</td>
                                            <td>" . StringUtils::formatDate($diplomacy->getDate(), false) . " bis " . StringUtils::formatDate($diplomacy->getDate() + WAR_DURATION, false) . "</td>
                                        </tr>";
            }
            echo "</table>
                                </td>
                            </tr>";
        }


        // Friedensabkommen
        $peace = $this->allianceDiplomacyRepository->getDiplomacies($alliance->getId(), AllianceDiplomacyLevel::PEACE);
        if (count($peace) > 0) {
            echo "<tr>
                                <th>Friedensabkommen:</th>
                                <td>
                                    <table class=\"tbl\">
                                        <tr>
                                            <th>Allianz</th>
                                            <th>Punkte</th>
                                            <th>Zeitraum</th>
                                        </tr>";
            foreach ($peace as $diplomacy) {
                $opAlliance = $this->repository->getAlliance($diplomacy->getAlliance2()->getId());
                echo "<tr>
                                            <td>
                                                <a href=\"?page=$page&amp;id=" . $diplomacy->getAlliance2()->getId() . "\">" . $opAlliance->toString() . "</a>
                                            </td>
                                            <td>" . StringUtils::formatNumber($opAlliance->getPoints()) . " / " . StringUtils::formatNumber($opAlliance->averagePoints) . "</td>
                                            <td>" . StringUtils::formatDate($diplomacy->getDate(), false) . " bis " . StringUtils::formatDate($diplomacy->getDate() + PEACE_DURATION, false) . "</td>
                                        </tr>";
            }
            echo "</table>
                                </td>
                            </tr>";
        }

        // Bündnisse
        $bnds = $this->allianceDiplomacyRepository->getDiplomacies($alliance->getId(), AllianceDiplomacyLevel::BND_CONFIRMED);
        if (count($bnds) > 0) {
            echo "<tr>
                                <th>Bündnisse:</th>
                                <td>
                                    <table class=\"tbl\">
                                        <tr>
                                            <th>Bündnisname</th>
                                            <th>Allianz</th>
                                            <th>Punkte</th>
                                            <th>Seit</th>
                                        </tr>";

            foreach ($bnds as $diplomacy) {
                $opAlliance = $this->repository->getAlliance($diplomacy->getAlliance2()->getId());
                echo "<tr>
                                            <td>" . stripslashes($diplomacy->getName()) . "</td>
                                            <td><a href=\"?page=$page&amp;id=" . $diplomacy->getAlliance2()->getId() . "\">" . $opAlliance->toString() . "</a></td>
                                            <td>" . StringUtils::formatNumber($opAlliance->getPoints()) . " / " . StringUtils::formatNumber($opAlliance->averagePoints) . "</td>
                                            <td>" . StringUtils::formatDate($diplomacy->getDate()) . "</td>
                                        </tr>";
            }
            echo "</table>
                                </td>
                            </tr>";
        }

        // Besucher
        echo "<tr><th>Besucherzähler:</th>
            <td colspan=\"2\">" . StringUtils::formatNumber($alliance->getVisits()) . " intern / " . StringUtils::formatNumber($alliance->getVisitsExternal()) . " extern</td></tr>\n";

        // Wings
        if ($this->config->getBoolean('allow_wings')) {
            $wings = $this->repository->searchAlliances(AllianceSearch::create()->motherId($alliance->getId()));
            if (count($wings) > 0) {
                echo "<tr><th>Wings:</th><td colspan=\"2\">";
                echo "<table class=\"tb\">";
                echo "<tr>
                    <th>Name</th>
                    <th>Punkte</th>
                    <th>Mitglieder</th>
                    <th>Punkteschnitt</th>
                </tr>";
                foreach ($wings as $wing) {
                    echo "<tr>
                    <td><a href=\"?page=alliance&amp;id=" . $wing->getId() . "\">" . $wing->toString() . "</a></td>
                    <td>" . StringUtils::formatNumber($wing->getPoints()) . "</td>
                    <td>" . $wing->memberCount . "</td>
                    <td>" . StringUtils::formatNumber($wing->averagePoints) . "</td>
                    </tr>";
                }
                echo "</td></tr>";
                echo "</table>";
                echo "</td></tr>";
            }
        }


        // Website
        if ($alliance->getUrl() != "") {
            echo "<tr><th>Website/Forum:</th><td colspan=\"2\"><b>" .
                StringUtils::formatLink($alliance->getUrl()) . "</a></b></td></tr>\n";
        }

        $founderNick = $this->userRepository->findOneBy(['id'=>$alliance->getFounderId()])->getNick();

        // Diverses
        echo "<tr><th>Mitglieder:</th>
            <td colspan=\"2\">" . $this->userRepository->count(['alliance'=>$alliance->getId()]) . "</td></tr>\n";
        // Punkte
        echo "<tr>
                            <th>Punkte / Schnitt:</th>
                            <td colspan=\"2\">";
        echo StringUtils::formatNumber($alliance->getPoints()) . " / " . StringUtils::formatNumber($this->getAveragePoints($alliance)) . "";
        echo "</td>
                        </tr>";
        echo "<tr><th>Gr&uuml;nder:</th>
            <td colspan=\"2\">
                <a href=\"?page=userinfo&amp;id=" . $alliance->getFounderId() . "\">" . $founderNick . "</a></td></tr>";
        // Gründung
        echo "<tr>
                            <th>Gründungsdatum:</th>
                            <td colspan=\"2\">
                                " . StringUtils::formatDate($alliance->getFoundationTimestamp()) . " (vor " . StringUtils::formatTimespan(time() - $alliance->getFoundationTimestamp()) . ")
                            </td>
                        </tr>";
        echo "\n</table><br/>";

        return ob_get_clean();
    }
}
