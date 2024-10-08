<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Bookmark\BookmarkRepository;
use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\BuddyList\BuddyListRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Exceptions\RecordNotFoundException;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Notepad\NotepadRepository;
use EtoA\Security\Player\CurrentPlayer;
use EtoA\Ship\ShipRepository;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserService
{
    public function __construct(
        private readonly ConfigurationService          $config,
        private readonly UserRepository                $userRepository,
        private readonly UserRatingRepository          $userRatingRepository,
        private readonly UserPropertiesRepository      $userPropertiesRepository,
        private readonly PlanetRepository              $planetRepository,
        private readonly BuildingRepository            $buildingRepository,
        private readonly TechnologyRepository          $technologyRepository,
        private readonly MailSenderService             $mailSenderService,
        private readonly PlanetService                 $planetService,
        private readonly UserSittingRepository         $userSittingRepository,
        private readonly UserWarningRepository         $userWarningRepository,
        private readonly UserMultiRepository           $userMultiRepository,
        private readonly AllianceRepository            $allianceRepository,
        private readonly AllianceApplicationRepository $allianceApplicationRepository,
        private readonly MarketAuctionRepository       $marketAuctionRepository,
        private readonly MarketResourceRepository      $marketResourceRepository,
        private readonly MarketShipRepository          $marketShipRepository,
        private readonly NotepadRepository             $notepadRepository,
        private readonly FleetRepository               $fleetRepository,
        private readonly ShipRepository                $shipRepository,
        private readonly DefenseRepository             $defenseRepository,
        private readonly MissileRepository             $missileRepository,
        private readonly BuddyListRepository           $buddyListRepository,
        private readonly TicketRepository              $ticketRepository,
        private readonly BookmarkRepository            $bookmarkRepository,
        private readonly FleetBookmarkRepository       $fleetBookmarkRepository,
        private readonly UserPointsRepository          $userPointsRepository,
        private readonly UserCommentRepository         $userCommentRepository,
        private readonly UserSurveillanceRepository    $userSurveillanceRepository,
        private readonly UserLogRepository             $userLogRepository,
        private readonly UserToXml                     $userToXml,
        private readonly LogRepository                 $logRepository,
        private readonly Environment                   $twig,
        private readonly UserPasswordHasherInterface   $passwordHasher,
    )
    {
    }

    public function register(
        string $name,
        string $email,
        string $nick,
        string $password,
        ?int   $race = 0,
        bool   $ghost = false,
        bool   $forceVerified = false
    ): User
    {
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
        $userId = $this->userRepository->create($nick, $name, $email, $password, (int)$race, $ghost);
        $user = $this->userRepository->getUser($userId);
        $hashedPassword = $this->passwordHasher->hashPassword(new CurrentPlayer($user), $password);
        $this->userRepository->updatePassword($userId, $hashedPassword, true);

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
        if ($user->getAllianceId() > 0) {
            $alliance = $this->allianceRepository->getAlliance($user->getAllianceId());
            if ($alliance !== null) {
                $members = $this->allianceRepository->findUsers($alliance->id);
                if (count($members) == 1) {
                    $this->allianceRepository->remove($alliance->id);
                } elseif ($alliance->founderId == $user->getId()) {
                    foreach ($members as $member) {
                        if ($member['user_id'] != $alliance->founderId) {
                            $this->allianceRepository->setFounderId($alliance->id, (int)$member['user_id']);

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
            $this->logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->getNick() . " hat sich selbst gelöscht!\nDie Daten des Benutzers wurden nach " . $xmlfile . " exportiert.");
        } elseif ($from != "") {
            $this->logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->getNick() . " wurde von " . $from . " gelöscht!\nDie Daten des Benutzers wurden nach " . $xmlfile . " exportiert.");
        } else {
            $this->logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->getNick() . " wurde gelöscht!\nDie Daten des Benutzers wurden nach " . $xmlfile . " exportiert.");
        }

        $text = "Hallo " . $user->getNick() . "

Dein Account bei Escape to Andromeda (" . $this->config->get('roundname') . ") wurde auf Grund von Inaktivität
oder auf eigenem Wunsch hin gelöscht.

Mit freundlichen Grüssen,
die Spielleitung";

        $this->mailSenderService->send("Accountlöschung", $text, $user->getEmail());
    }

    public function deleteRequest(int $userId, string $password): bool
    {
        $user = $this->userRepository->getUser($userId);
        if ($user !== null && validatePasswort($password, $user->getPassword())) {
            $timestamp = time() + ($this->config->getInt('user_delete_days') * 3600 * 24);
            $this->userRepository->markDeleted($userId, $timestamp);

            return true;
        }

        return false;
    }

    public function updateDelete(User $user, int $timestamp): void
    {
        $user->setDeleted($timestamp);
        $this->userRepository->save($user);
    }

    public function removeInactive(bool $manual = false): int
    {
        /** @var int $registerTime Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat */
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        /** @var int $onlineTime Zeit nach der ein User normalerweise gelöscht wird */
        $onlineTime = time() - (24 * 3600 * $this->config->param1Int('user_inactive_days'));

        $inactiveUsers = $this->userRepository->findInactive($registerTime, $onlineTime);
        foreach ($inactiveUsers as $user) {
            $this->delete($user->getId());
        }

        $this->logRepository->add(
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
            $text = "Hallo " . $user->getNick() . "

Du hast dich seit mehr als " . $this->config->param2Int('user_inactive_days') . " Tage nicht mehr bei Escape to Andromeda (" . $this->config->get('roundname') . ") eingeloggt und
dein Account wurde deshalb als inaktiv markiert. Solltest du dich innerhalb von " . $this->config->getInt('user_inactive_days') . " Tage
nicht mehr einloggen wird der Account gelöscht.

Mit freundlichen Grüssen,
die Spielleitung";

            $this->mailSenderService->send('Inaktivität', $text, $user->getEmail());
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
            $this->delete($user->getId());
        }

        $this->logRepository->add(
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
        $replace = array($user->getNick(), $user->getNick());
        $message = str_replace($search, $replace, $message);

        $this->userLogRepository->add($userId, $zone, $message, gethostbyname($_SERVER['REMOTE_ADDR']), $public);
    }

    /**

     * @deprecated let symfony/form handler deal with it

     */
    public function setPassword(int $userId, string $oldPassword, string $newPassword1, string $newPassword2): void
    {
        $user = $this->userRepository->getUser($userId);

        if (!validatePasswort($oldPassword, $user->getPassword())) {
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

        $this->logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Spieler [b]" . $user->getNick() . "[/b] &auml;ndert sein Passwort!");

        $this->mailSenderService->send(
            "Passwortänderung",
            "Hallo " . $user->getNick() . "\n\nDies ist eine Bestätigung, dass du dein Passwort für deinen Account erfolgreich geändert hast!\n\nSolltest du dein Passwort nicht selbst geändert haben, so nimm bitte sobald wie möglich Kontakt mit einem Game-Administrator auf: http://www.etoa.ch/kontakt",
            $user->getEmail()
        );

        $this->addToUserLog($userId, "settings", "{nick} ändert sein Passwort.", false);
    }

    /**
     * @throws SyntaxError if there is a syntax error in the email template
     * @throws RuntimeError
     * @throws RecordNotFoundException if the user record could not be found
     * @throws LoaderError if the email template could not be loaded
     */
    public function resetPassword(string $nick, string $emailFixed): void
    {
        $user = $this->userRepository->getUserByNickAndEmail($nick, $emailFixed);
        if ($user === null) {
            throw new RecordNotFoundException('Es wurde kein entsprechender Datensatz gefunden!');
        }

        $pw = generatePasswort();

        $emailText = $this->twig->render('email/new-password.txt.twig', [
            'user' => $user,
            'roundName' => $this->config->get('roundname'),
            'password' => $pw,
        ]);
        $this->mailSenderService->send("Passwort-Anforderung", $emailText, $emailFixed);

        $this->userRepository->updatePassword($user->getId(), $pw);

        $this->logRepository->add(
            LogFacility::USER,
            LogSeverity::INFO,
            'Der Benutzer ' . $user->getNick() . ' hat ein neues Passwort per E-Mail angefordert!'
        );
    }
}
