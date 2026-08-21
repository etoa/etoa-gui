<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Admin\AllianceBoardAvatar;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Race;
use EtoA\Entity\User;
use EtoA\Exceptions\RecordNotFoundException;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Support\ValidationUtils;
use EtoA\Universe\Planet\PlanetService;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use EtoA\Support\GameUtils;
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
        private readonly MailSenderService             $mailSenderService,
        private readonly PlanetService                 $planetService,
        private readonly AllianceRepository            $allianceRepository,
        private readonly FleetRepository               $fleetRepository,
        private readonly UserLogRepository             $userLogRepository,
        private readonly UserToXml                     $userToXml,
        private readonly LogRepository                 $logRepository,
        private readonly Environment                   $twig,
        private readonly AllianceDiplomacyRepository   $allianceDiplomacyRepository,
        private readonly Security                      $security,
        private readonly UserPasswordHasherInterface   $passwordHasher,
    )
    {
    }

    /**
     * The password arrives already hashed, both forms use the "hash_property_path"
     * option of PasswordType. Length and blank rules therefore belong to the form.
     */
    public function register(
        string $name,
        string $email,
        string $nick,
        string $hashedPassword,
        ?Race   $race = null,
        bool   $ghost = false,
        bool   $forceVerified = false
    ): User
    {
        // Validate required data is not empty
        if (ValidationUtils::blank($name) || ValidationUtils::blank($email) || ValidationUtils::blank($nick) || ValidationUtils::blank($hashedPassword)) {
            throw new Exception("Nicht alle Felder sind ausgefüllt!");
        }

        // Validate email
        if (!ValidationUtils::checkEmail($email)) {
            throw new Exception("Diese E-Mail-Adresse scheint ungültig zu sein. Prüfe nach, ob dein E-Mail-Server online ist und die Adresse im korrekten Format vorliegt!");
        }

        // Validate name
        if (!ValidationUtils::checkValidName($name)) {
            throw new Exception("Du hast ein unerlaubtes Zeichen im vollständigen Namen!");
        }

        // Validate nickname
        $nick = trim($nick);
        if (!ValidationUtils::checkValidNick($nick)) {
            throw new Exception("Du hast ein unerlaubtes Zeichen im Benutzernamen!");
        }
        if ($nick == '') {
            throw new Exception("Dein Nickname darf nicht nur aus Leerzeichen bestehen!");
        }
        $nick_length = strlen(utf8_decode($nick));
        if ($nick_length < $this->config->param1Int('nick_length') || $nick_length > $this->config->param2Int('nick_length')) {
            throw new Exception("Dein Nickname muss mindestens " . $this->config->param1Int('nick_length') . " Zeichen und maximum " . $this->config->param2Int('nick_length') . " Zeichen haben!");
        }

        // Check existing user
        if ($this->userRepository->exists(UserSearch::create()->nick($nick)->emailFix($email))) {
            throw new Exception("Der Benutzer mit diesem Nicknamen oder dieser E-Mail-Adresse existiert bereits!");
        }

        // Add new record
        $user = $this->userRepository->create($nick, $name, $email, $hashedPassword, $race, $ghost);

        $this->userRepository->setSittingDays($user, $this->config->getInt('user_sitting_days'));
        $this->userRatingRepository->addBlank($user);
        $this->userPropertiesRepository->addBlank($user);

        $verificationRequired = $this->config->getBoolean('email_verification_required');
        $this->userRepository->setVerified($user, !$verificationRequired || $forceVerified);

        return $user;
    }

    public function delete(User $user, bool $self = false, string $from = ""): void
    {
        try {
            $xmlfile = $this->userToXml->toCacheFile($user);
        } catch (Exception $ex) {
            throw new Exception("Konnte UserXML für " . $user->getId() . " nicht exportieren, User nicht gelöscht!", 0, $ex);
        }

        $userPlanets = $user->getPlanets();
        foreach ($userPlanets as $planet) {

            // Delete market fleets to planet
            $marketResFleets = $this->fleetRepository->findByParameters((new FleetSearchParameters())
                ->entityTo($planet->getEntity()->getId())
                ->action($this->config->get('market_ship_action_ress')));
            $marketShipFleets = $this->fleetRepository->findByParameters((new FleetSearchParameters())
                ->entityTo($planet->getEntity()->getId())
                ->action($this->config->get('market_ship_action_ship')));
            foreach (array_merge($marketResFleets, $marketShipFleets) as $fleet) {
                $this->fleetRepository->remove($fleet);
                $this->fleetRepository->save();
            }
            $this->planetService->reset($planet);
        }

        //
        // Allianz löschen (falls alleine) oder einen Nachfolger bestimmen
        //
        if ($user->getAlliance()) {
            $alliance = $user->getAlliance();
            $members = $alliance->getMembers();
            if (count($members) === 1) {
                $this->allianceRepository->remove($alliance);
            } elseif ($alliance->getFounder() === $user) {
                foreach ($members as $member) {
                    if ($member !== $alliance->getFounder()) {
                        $this->allianceRepository->setFounder($alliance, $member);

                        break;
                    }
                }
            }
        }

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

        $this->userRepository->remove($user);
        $this->userRepository->save();
    }

    public function deleteRequest(int $userId, string $password): bool
    {
        $user = $this->userRepository->getUser($userId);
        if ($user !== null && $this->passwordHasher->isPasswordValid($user, $password)) {
            $timestamp = time() + ($this->config->getInt('user_delete_days') * 3600 * 24);
            $this->userRepository->markDeleted($userId, $timestamp);

            return true;
        }

        return false;
    }

    public function updateDelete(User $user, int $timestamp): void
    {
        $this->userRepository->markDeleted($user,$timestamp);
    }

    public function removeInactive(bool $manual = false): int
    {
        /** @var int $registerTime Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat */
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        /** @var int $onlineTime Zeit nach der ein User normalerweise gelöscht wird */
        $onlineTime = time() - (24 * 3600 * $this->config->param1Int('user_inactive_days'));

        $inactiveUsers = $this->userRepository->findInactive($registerTime, $onlineTime);
        foreach ($inactiveUsers as $user) {
            $this->delete($user);
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
            $this->delete($user);
        }

        $this->logRepository->add(
            LogFacility::SYSTEM,
            LogSeverity::INFO,
            count($deletedUsers) . ' als gelöscht markierte User wurden ' . ($manual ? 'manuell' : '') . ' gelöscht!'
        );

        return count($deletedUsers);
    }

    public function addToUserLog(User $user, string $zone, string $message, bool $public = true): void
    {
        $search = array("{user}", "{nick}");
        $replace = array($user->getNick(), $user->getNick());
        $message = str_replace($search, $replace, $message);

        $this->userLogRepository->add($user, $zone, $message, gethostbyname($_SERVER['REMOTE_ADDR']), $public);
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

        $pw = GameUtils::generatePasswort();

        $emailText = $this->twig->render('email/new-password.txt.twig', [
            'user' => $user,
            'roundName' => $this->config->get('roundname'),
            'password' => $pw,
        ]);
        $this->mailSenderService->send("Passwort-Anforderung", $emailText, $emailFixed);

        $this->userRepository->updatePassword($user, $this->passwordHasher->hashPassword($user, $pw));

        $this->logRepository->add(
            LogFacility::USER,
            LogSeverity::INFO,
            'Der Benutzer ' . $user->getNick() . ' hat ein neues Passwort per E-Mail angefordert!'
        );
    }

    public function buildProfileImageUrl($profileImage): ?string
    {
        if ($profileImage == '') {
            return null;
        }

        return ProfileImage::IMAGE_PATH . $profileImage;
    }

    public function buildAvatarUrl($avatar): ?string
    {
        if ($avatar == '') {
            return null;
        }

        return AllianceBoardAvatar::IMAGE_PATH . $avatar;
    }

    public function canAttackUser(User $u):bool
    {
        $cu = $this->security->getUser()->getData();
        // att allowed if war is active
        // or att allowed if target user is not noob protected
        // or att allowed if target user is inactive
        // or att allowed if target user is locked
        if ($cu->getAlliance() && $u->getAlliance()) {

            return $this->allianceDiplomacyRepository->isAtWar($cu->getAlliance(), $u->getAlliance())
                || !$this->isUserNoobProtected($u)
                || $this->isInactiv($u)
                || ($u->getBlockedFrom() < time() && $u->getBlockedTo() > time())
                || $u->isNpc();
        } else {
            return !$this->isUserNoobProtected($u)
                || $this->isInactiv($u)
                || ($u->getBlockedFrom() < time() && $u->getBlockedTo() > time())
                || $u->isNpc();
        }
    }

    public function isUserNoobProtected(User $u):bool
    {
        $cu = $this->security->getUser()->getData();
        // check whether user points are outside limits
        // or this user or opponent is below minimum attack threshold
        return ($u->getPoints() * $this->config->getFloat('user_attack_percentage') > $cu->getPoints() || $u->getPoints() / $this->config->getFloat('user_attack_percentage') < $cu->getPoints())
            || ($cu->getPoints() <= $this->config->getInt('user_attack_min_points'))
            || ($u->getPoints() <= $this->config->getInt('user_attack_min_points'));
    }

    public function isInactiv(User $u):bool
    {
        if (!$u->getAdmin()) {
            if (!$u->getHmodFrom() != 0 && $u->getHmodTo() != 0) {
                if ($u->getLastOnline() < time() - USER_INACTIVE_SHOW * 86400) {
                    return true;
                }
            }
        }
        return false;
    }
}
