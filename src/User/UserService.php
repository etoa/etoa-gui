<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Backend\BackendMessageService;
use EtoA\Bookmark\BookmarkRepository;
use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\BuddyList\BuddyListRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogSeverity;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Notepad\NotepadRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use Exception;
use Log;

class UserService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;
    private UserRatingRepository $userRatingRepository;
    private UserPropertiesRepository $userPropertiesRepository;
    private PlanetRepository $planetRepository;
    private BuildingRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipQueueRepository $shipQueueRepository;
    private DefenseQueueRepository $defenseQueueRepository;
    private MailSenderService $mailSenderService;
    private PlanetService $planetService;
    private UserSittingRepository $userSittingRepository;
    private UserWarningRepository $userWarningRepository;
    private UserMultiRepository $userMultiRepository;
    private AllianceRepository $allianceRepository;
    private AllianceApplicationRepository $allianceApplicationRepository;
    private MarketAuctionRepository $marketAuctionRepository;
    private MarketResourceRepository $marketResourceRepository;
    private MarketShipRepository $marketShipRepository;
    private NotepadRepository $notepadRepository;
    private FleetRepository $fleetRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;
    private MissileRepository $missileRepository;
    private BuddyListRepository $buddyListRepository;
    private TicketRepository $ticketRepository;
    private BookmarkRepository $bookmarkRepository;
    private FleetBookmarkRepository $fleetBookmarkRepository;
    private UserPointsRepository $userPointsRepository;
    private UserCommentRepository $userCommentRepository;
    private UserSurveillanceRepository $userSurveillanceRepository;
    private BackendMessageService $backendMessageService;
    private UserLogRepository $userLogRepository;
    private UserToXml $userToXml;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepository,
        UserRatingRepository $userRatingRepository,
        UserPropertiesRepository $userPropertiesRepository,
        PlanetRepository $planetRepository,
        BuildingRepository $buildingRepository,
        TechnologyRepository $technologyRepository,
        ShipQueueRepository $shipQueueRepository,
        DefenseQueueRepository $defenseQueueRepository,
        MailSenderService $mailSenderService,
        PlanetService $planetService,
        UserSittingRepository $userSittingRepository,
        UserWarningRepository $userWarningRepository,
        UserMultiRepository $userMultiRepository,
        AllianceRepository $allianceRepository,
        AllianceApplicationRepository $allianceApplicationRepository,
        MarketAuctionRepository $marketAuctionRepository,
        MarketResourceRepository $marketResourceRepository,
        MarketShipRepository $marketShipRepository,
        NotepadRepository $notepadRepository,
        FleetRepository $fleetRepository,
        ShipRepository $shipRepository,
        DefenseRepository $defenseRepository,
        MissileRepository $missileRepository,
        BuddyListRepository $buddyListRepository,
        TicketRepository $ticketRepository,
        BookmarkRepository $bookmarkRepository,
        FleetBookmarkRepository $fleetBookmarkRepository,
        UserPointsRepository $userPointsRepository,
        UserCommentRepository $userCommentRepository,
        UserSurveillanceRepository $userSurveillanceRepository,
        BackendMessageService $backendMessageService,
        UserLogRepository $userLogRepository,
        UserToXml $userToXml
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
        $this->userRatingRepository = $userRatingRepository;
        $this->userPropertiesRepository = $userPropertiesRepository;
        $this->planetRepository = $planetRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipQueueRepository = $shipQueueRepository;
        $this->defenseQueueRepository = $defenseQueueRepository;
        $this->mailSenderService = $mailSenderService;
        $this->planetService = $planetService;
        $this->userSittingRepository = $userSittingRepository;
        $this->userWarningRepository = $userWarningRepository;
        $this->userMultiRepository = $userMultiRepository;
        $this->allianceRepository = $allianceRepository;
        $this->allianceApplicationRepository = $allianceApplicationRepository;
        $this->marketAuctionRepository = $marketAuctionRepository;
        $this->marketResourceRepository = $marketResourceRepository;
        $this->marketShipRepository = $marketShipRepository;
        $this->notepadRepository = $notepadRepository;
        $this->fleetRepository = $fleetRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
        $this->missileRepository = $missileRepository;
        $this->buddyListRepository = $buddyListRepository;
        $this->ticketRepository = $ticketRepository;
        $this->bookmarkRepository = $bookmarkRepository;
        $this->fleetBookmarkRepository = $fleetBookmarkRepository;
        $this->userPointsRepository = $userPointsRepository;
        $this->userCommentRepository = $userCommentRepository;
        $this->userSurveillanceRepository = $userSurveillanceRepository;
        $this->backendMessageService = $backendMessageService;
        $this->userLogRepository = $userLogRepository;
        $this->userToXml = $userToXml;
    }

    public function register(
        string $name,
        string $email,
        string $nick,
        string $password,
        int $race = 0,
        bool $ghost = false,
        bool $forceVerified = false
    ): User {
        // Validate required data is not empty
        if (!filled($name) || !filled($email) || !filled($nick) || !filled($password)) {
            throw new Exception("Nicht alle Felder sind ausgefüllt!");
        }

        // Validate email
        if (!checkEmail($email)) {
            throw new Exception("Diese E-Mail-Adresse scheint ungültig zu sein. Prüfe nach, ob dein E-Mail-Server online ist und die Adresse im korrekten Format vorliegt!");
        }

        // Validate name
        if (!checkValidName($name)) {
            throw new Exception("Du hast ein unerlaubtes Zeichen im vollständigen Namen!");
        }

        // Validate nickname
        $nick = trim($nick);
        if (!checkValidNick($nick)) {
            throw new Exception("Du hast ein unerlaubtes Zeichen im Benutzernamen!");
        }
        if ($nick == '') {
            throw new Exception("Dein Nickname darf nicht nur aus Leerzeichen bestehen!");
        }
        $nick_length = strlen(utf8_decode($nick));
        if ($nick_length < $this->config->param1Int('nick_length') || $nick_length > $this->config->param2Int('nick_length')) {
            throw new Exception("Dein Nickname muss mindestens " . $this->config->param1Int('nick_length') . " Zeichen und maximum " . $this->config->param2Int('nick_length') . " Zeichen haben!");
        }

        // Validate password
        if (strlen($password) < $this->config->getInt('password_minlength')) {
            throw new Exception("Das Passwort ist noch zu kurz (mind. " . $this->config->getInt('password_minlength') . " Zeichen sind nötig)!");
        }

        // Check existing user
        if ($this->userRepository->exists(UserSearch::create()->nick($nick)->emailFix($email))) {
            throw new Exception("Der Benutzer mit diesem Nicknamen oder dieser E-Mail-Adresse existiert bereits!");
        }

        // Add new record
        $userId = $this->userRepository->create($nick, $name, $email, $password, $race, $ghost);
        $this->userRepository->setSittingDays($userId, $this->config->getInt('user_sitting_days'));
        $this->userRatingRepository->addBlank($userId);
        $this->userPropertiesRepository->addBlank($userId);

        $verificationRequired = $this->config->getBoolean('email_verification_required');
        $this->userRepository->setVerified($userId, !$verificationRequired || $forceVerified);

        return $this->userRepository->getUser($userId);
    }

    public function delete(int $userId, bool $self = false, string $from = ""): void
    {
        $user = $this->userRepository->getUser($userId);
        if ($user === null) {
            throw new Exception('Benutzer existiert nicht!');
        }

        try {
            $xmlfile = $this->userToXml->toCacheFile($userId);
        } catch (Exception $ex) {
            throw new Exception("Konnte UserXML für " . $userId . " nicht exportieren, User nicht gelöscht!", 0, $ex);
        }

        // Delete fleets of user
        $userFleets = $this->fleetRepository->findByParameters((new FleetSearchParameters())
            ->userId($userId));
        foreach ($userFleets as $fleet) {
            $this->fleetRepository->removeAllShipsFromFleet($fleet->id);
            $this->fleetRepository->remove($fleet->id);
        }

        $userPlanets = $this->planetRepository->getUserPlanets($userId);
        foreach ($userPlanets as $planet) {

            // Delete market fleets to planet
            $marketResFleets = $this->fleetRepository->findByParameters((new FleetSearchParameters())
                ->entityTo($planet->id)
                ->action($this->config->get('market_ship_action_ress')));
            $marketShipFleets = $this->fleetRepository->findByParameters((new FleetSearchParameters())
                ->entityTo($planet->id)
                ->action($this->config->get('market_ship_action_ship')));
            foreach (array_merge($marketResFleets, $marketShipFleets) as $fleet) {
                $this->fleetRepository->removeAllShipsFromFleet($fleet->id);
                $this->fleetRepository->remove($fleet->id);
            }

            $this->planetService->reset($planet->id);
        }

        //
        // Allianz löschen (falls alleine) oder einen Nachfolger bestimmen
        //
        if ($user->allianceId > 0) {
            $alliance = $this->allianceRepository->getAlliance($user->allianceId);
            if ($alliance !== null) {
                $members = $this->allianceRepository->findUsers($alliance->id);
                if (count($members) == 1) {
                    $this->allianceRepository->remove($alliance->id);
                } elseif ($alliance->founderId == $user->id) {
                    foreach ($members as $member) {
                        if ($member['user_id'] != $alliance->founderId) {
                            $this->allianceRepository->setFounderId($alliance->id, (int) $member['user_id']);

                            break;
                        }
                    }
                }
            }
        }

        $this->allianceApplicationRepository->deleteUserApplication($userId);
        $this->shipRepository->removeForUser($userId);
        $this->defenseRepository->removeForUser($userId);
        $this->technologyRepository->removeForUser($userId);
        $this->buildingRepository->removeForUser($userId);
        $this->missileRepository->removeForUser($userId);
        $this->buddyListRepository->removeForUser($userId);
        $this->marketResourceRepository->deleteUserOffers($userId);
        $this->marketShipRepository->deleteUserOffers($userId);
        $this->marketAuctionRepository->deleteUserAuctions($userId);
        $this->notepadRepository->deleteAll($userId);
        $this->bookmarkRepository->removeForUser($userId);
        $this->fleetBookmarkRepository->removeForUser($userId);
        $this->userMultiRepository->deleteUserEntries($userId);
        $this->userPointsRepository->removeForUser($userId);
        $this->userWarningRepository->deleteAllUserEntries($userId);
        $this->userSittingRepository->deleteAllUserEntries($userId);
        $this->userPropertiesRepository->removeForUser($userId);
        $this->userSurveillanceRepository->removeForUser($userId);
        $this->userCommentRepository->removeForUser($userId);
        $this->userRatingRepository->removeForUser($userId);
        $this->ticketRepository->removeForUser($userId);

        $this->userRepository->remove($userId);

        //Log schreiben
        if ($self) {
            Log::add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->nick . " hat sich selbst gelöscht!\nDie Daten des Benutzers wurden nach " . $xmlfile . " exportiert.");
        } elseif ($from != "") {
            Log::add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->nick . " wurde von " . $from . " gelöscht!\nDie Daten des Benutzers wurden nach " . $xmlfile . " exportiert.");
        } else {
            Log::add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->nick . " wurde gelöscht!\nDie Daten des Benutzers wurden nach " . $xmlfile . " exportiert.");
        }

        $text = "Hallo " . $user->nick . "

Dein Account bei Escape to Andromeda (" . $this->config->get('roundname') . ") wurde auf Grund von Inaktivität
oder auf eigenem Wunsch hin gelöscht.

Mit freundlichen Grüssen,
die Spielleitung";

        $this->mailSenderService->send("Accountlöschung", $text, $user->email);
    }

    public function deleteRequest(int $userId, string $password): bool
    {
        $user = $this->userRepository->getUser($userId);
        if ($user !== null && validatePasswort($password, $user->password)) {
            $timestamp = time() + ($this->config->getInt('user_delete_days') * 3600 * 24);
            $this->userRepository->markDeleted($userId, $timestamp);

            return true;
        }

        return false;
    }

    public function revokeDelete(int $userId): void
    {
        $this->userRepository->markDeleted($userId, 0);
    }

    public function removeInactive(bool $manual = false): int
    {
        /** @var int $registerTime Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat */
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        /** @var int $onlineTime Zeit nach der ein User normalerweise gelöscht wird */
        $onlineTime = time() - (24 * 3600 * $this->config->param1Int('user_inactive_days'));

        $inactiveUsers = $this->userRepository->findInactive($registerTime, $onlineTime);
        foreach ($inactiveUsers as $user) {
            $this->delete($user->id);
        }

        Log::add(
            LogFacility::SYSTEM,
            LogSeverity::INFO,
            count($inactiveUsers) . " inaktive User die seit " . date("d.m.Y H:i", $onlineTime) . " nicht mehr online waren oder seit " . date("d.m.Y H:i", $registerTime) . " keine Punkte haben wurden " . ($manual ? 'manuell' : '') . " gelöscht!"
        );

        return count($inactiveUsers);
    }

    public function informLongInactive(): void
    {
        $userInactiveTimeLong = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));
        $inactiveTime = time() - (24 * 3600 * $userInactiveTimeLong);

        $longInactive = $this->userRepository->findLongInactive($inactiveTime - (3600 * 24), $inactiveTime);
        foreach ($longInactive as $user) {
            $text = "Hallo " . $user->nick . "

Du hast dich seit mehr als " . $this->config->param2Int('user_inactive_days') . " Tage nicht mehr bei Escape to Andromeda (" . $this->config->get('roundname') . ") eingeloggt und
dein Account wurde deshalb als inaktiv markiert. Solltest du dich innerhalb von " . $this->config->getInt('user_inactive_days') . " Tage
nicht mehr einloggen wird der Account gelöscht.

Mit freundlichen Grüssen,
die Spielleitung";

            $this->mailSenderService->send('Inaktivität', $text, $user->email);
        }
    }

    public function getNumInactive(): int
    {
        /** @var int $registerTime Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat */
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        /** @var int $onlineTime Zeit nach der ein User normalerweise gelöscht wird */
        $onlineTime = time() - (24 * 3600 * $this->config->param1Int('user_inactive_days'));

        $inactiveUsers = $this->userRepository->findInactive($registerTime, $onlineTime);

        return count($inactiveUsers);
    }

    public function removeDeleted(bool $manual = false): int
    {
        $deletedUsers = $this->userRepository->findDeleted();
        foreach ($deletedUsers as $user) {
            $this->delete($user->id);
        }

        Log::add(
            LogFacility::SYSTEM,
            LogSeverity::INFO,
            count($deletedUsers) . ' als gelöscht markierte User wurden ' . ($manual ? 'manuell' : '') . ' gelöscht!'
        );

        return count($deletedUsers);
    }

    public function addToUserLog(int $userId, string $zone, string $message, bool $public = true): void
    {
        $user = $this->userRepository->getUser($userId);

        $search = array("{user}", "{nick}");
        $replace = array($user->nick, $user->nick);
        $message = str_replace($search, $replace, $message);

        $this->userLogRepository->add($userId, $zone, $message, gethostbyname($_SERVER['REMOTE_ADDR']), $public);
    }

    public function setPassword(int $userId, string $oldPassword, string $newPassword1, string $newPassword2): void
    {
        $user = $this->userRepository->getUser($userId);

        if (!validatePasswort($oldPassword, $user->password)) {
            throw new Exception("Dein altes Passwort stimmt nicht mit dem gespeicherten Passwort &uuml;berein!");
        }

        if ($this->userSittingRepository->existsEntry($userId, md5($newPassword1))) {
            throw new Exception("Das Passwort darf nicht identisch mit dem Sitterpasswort sein!");
        }

        if ($newPassword1 != $newPassword2) {
            throw new Exception("Die Eingaben m&uuml;ssen identisch sein!");
        }

        if (strlen($newPassword1) < $this->config->getInt('password_minlength')) {
            throw new Exception("Das Passwort muss mindestens " . $this->config->getInt('password_minlength') . " Zeichen lang sein!");
        }

        $this->userRepository->updatePassword($userId, $newPassword1);

        Log::add(LogFacility::USER, LogSeverity::INFO, "Der Spieler [b]" . $user->nick . "[/b] &auml;ndert sein Passwort!");

        $this->mailSenderService->send(
            "Passwortänderung",
            "Hallo " . $user->nick . "\n\nDies ist eine Bestätigung, dass du dein Passwort für deinen Account erfolgreich geändert hast!\n\nSolltest du dein Passwort nicht selbst geändert haben, so nimm bitte sobald wie möglich Kontakt mit einem Game-Administrator auf: http://www.etoa.ch/kontakt",
            $user->email
        );

        $this->addToUserLog($userId, "settings", "{nick} ändert sein Passwort.", false);
    }

    public function resetPassword(string $nick, string $emailFixed): void
    {
        $user = $this->userRepository->getUserByNickAndEmail($nick, $emailFixed);
        if ($user === null) {
            throw new Exception('Es wurde kein entsprechender Datensatz gefunden!');
        }

        $pw = generatePasswort();

        $email_text = 'Hallo ' . $_POST['user_nick'] . "\n\nDu hast ein neues Passwort angefordert.\nHier sind die neuen Daten:\n\nUniversum: " . $this->config->get('roundname') . "\n\nNick: " . $nick . "\nPasswort: " . $pw . "\n\nWeiterhin viel Spass...\nDas EtoA-Team";
        $this->mailSenderService->send("Passwort-Anforderung", $email_text, $emailFixed);

        $this->userRepository->updatePassword($user->id, $pw);

        Log::add(LogFacility::USER, LogSeverity::INFO, 'Der Benutzer ' . $_POST['user_nick'] . ' hat ein neues Passwort per E-Mail angefordert!');
    }
}
