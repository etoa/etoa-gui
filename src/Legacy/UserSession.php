<?php

namespace EtoA\Legacy;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\StringUtils;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionManager;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSittingRepository;

/**
 * Provides session and authentication management
 * for player area.
 */
class UserSession
{
    /**
     * @var string Message of the last error
     */
    private string $lastError;

    /**
     * @var string Short string (one word/abreviation) that characterizes the last error
     */
    private string $lastErrorCode;

    /**
     * @var bool True if this is the first run (page) of the session, that means if the user hast just logged in
     */
    private bool $firstView = false;

    private int $bot_count;

    private int $last_span;

    private int $time_action;

    private int $time_login;

    private string $passwordField;

    private bool $cLogin;

    private string $cRemoteAddr;

    private string $cUserAgent;

    private int $userId;

    private string $userNick;

    private bool $sittingActive;

    private bool $falseSitter;

    private int $sittingUntil;

    /**
     * The constructor defines the session hash function to be used
     * and names and initiates the session
     */
    public function __construct(
        private readonly ConfigurationService       $config,
        private readonly UserSessionManager         $sessionManager,
        private readonly UserSessionRepository      $userSessionRepository,
        private readonly UserSittingRepository      $userSittingRepository,
        private readonly UserLoginFailureRepository $userLoginFailureRepository,
        private readonly UserRepository             $userRepository,
        private readonly LogRepository              $logRepository,
    )
    {
        // Use SHA1 hash
        ini_set('session.hash_function', '1');

        // Set session name based on class and game round file system path.
        $name = md5(get_class($this) . __DIR__);
        @session_name($name);
        @session_start();    // Start the session
    }

    public function getLastError(): string
    {
        return ($this->lastError != null) ? $this->lastError : "";
    }

    public function getLastErrorCode(): string
    {
        return ($this->lastErrorCode != null) ? $this->lastErrorCode : "general";
    }

    public function isFirstView(): bool
    {
        return $this->firstView;
    }

    public function getId(): false|string
    {
        return session_id();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserNick(): string
    {
        return $this->userNick;
    }

    /**
     * Getter that returns session variables or some class properties
     *
     * @param string $field
     * @return mixed Requested variable or null if field was not found
     */
    function __get(string $field): mixed
    {
        if (isset($_SESSION[$field])) {
            return $_SESSION[$field];
        }
        return null;
    }

    /**
     * Checks if a session property exists
     *
     * @param string $field Property name
     * @return bool True if property exists
     */
    function __isset(string $field): bool
    {
        return isset($_SESSION[$field]);
    }

    /**
     * Sets a session property
     *
     * @param string $field Property name
     * @param mixed $value Property value
     * @return bool True if setting was successfull
     */
    function __set(string $field, mixed $value): void
    {
        $_SESSION[$field] = $value;
    }

    /**
     * Unsets a session property
     *
     * @param string $field Property name
     */
    function __unset(string $field): void
    {
        unset($_SESSION[$field]);
    }

    public function login(array $data): bool
    {
        $this->sessionManager->cleanup();

        $loginTimeDifferenceThreshold = 3600;

        if (isset($data['token'])) {
            $t = hexdec(substr($data['token'], 40));
            $logintoken = sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $t) . dechex($t);

            // Check token (except if user is form localhost = developer)
            // Disable this check until https login proglems solved
            if (true || $logintoken == $data['token'] || $_SERVER['REMOTE_ADDR'] == "127.0.0.1" || $_SERVER['REMOTE_ADDR'] == "::1") {
                if (!isset($_SESSION['used_login_tokens']))
                    $_SESSION['used_login_tokens'] = array();

                $logintoken = $data['token'];
                $nickField = sha1("nick" . $logintoken . $t);
                $passwordField = sha1("password" . $logintoken . $t);

                $this->passwordField = $passwordField;

                // Check if token has not already been used (multi logins with browser auto-refresher)
                if (!in_array($logintoken, $_SESSION['used_login_tokens'], true)) {
                    $_SESSION['used_login_tokens'][] = $logintoken;

                    // Check if login is withing given time bounds (+- one hour)
                    $realtime = time();
                    if ($t + $loginTimeDifferenceThreshold >= $realtime && $t - $loginTimeDifferenceThreshold <= $realtime) {
                        // Check if the user and password fields are set
                        if (isset($data[$nickField]) && isset($data[$passwordField])) {
                            $loginNick = trim($data[$nickField]);
                            $loginPassword = trim($data[$passwordField]);

                            if ($loginNick != "" && $loginPassword != "") // Add here regex check for nickname
                            {
                                $user = $this->userRepository->getUserByNick($loginNick);
                                if ($user !== null) {
                                    $t = time();

                                    // check sitter
                                    $this->sittingActive = false;
                                    $this->falseSitter = false;
                                    $sittingEntry = $this->userSittingRepository->getActiveUserEntry($user->id);
                                    if ($sittingEntry !== null) {
                                        if (validatePasswort($loginPassword, $sittingEntry->password)) {
                                            $this->sittingActive = true;
                                            $this->sittingUntil = $sittingEntry->dateTo;
                                        } elseif (validatePasswort($loginPassword, $user->password)) {
                                            $this->falseSitter = true;
                                            $this->sittingActive = true;
                                            $this->sittingUntil = $sittingEntry->dateTo;
                                        }
                                    }
                                    if (strlen($user->password) == 64) {
                                        $pw = $loginPassword;
                                        $seed = $user->registered;
                                        $salt = "yheaP;BXf;UokIAJ4dhaOL"; // Round 9
                                        if ($user->password == md5($pw . $seed . $salt) . md5($salt . $seed . $pw)) {
                                            $user->password = $this->userRepository->updatePassword($user->id, $pw);
                                        }
                                    }

                                    if (
                                        validatePasswort($loginPassword, $user->password)
                                        || $this->sittingActive
                                        || ($user->passwordTemp != "" && $user->passwordTemp == $loginPassword)
                                    ) {
                                        session_regenerate_id(true);

                                        $this->userId = $user->id;
                                        $this->userNick = $user->nick;
                                        $this->time_login = $t;
                                        $this->time_action = $t;
                                        $this->registerSession();
                                        $this->bot_count = 0;
                                        $this->firstView = true;

                                        // do not use this values for real verification
                                        // intended only for chat session pseudo-validation
                                        $this->cRemoteAddr = $_SERVER['REMOTE_ADDR'];
                                        $this->cUserAgent = $_SERVER['HTTP_USER_AGENT'];
                                        // does not guarantee valid login, see above.
                                        // this isn't set to false on session timeout
                                        $this->cLogin = true;

                                        return true;
                                    } else {
                                        $this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
                                        $this->userLoginFailureRepository->add($user->id, $t, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                                        $this->lastErrorCode = "pass";
                                    }
                                } else {
                                    $this->lastError = "Der Benutzername ist in dieser Runde nicht registriert!";
                                    $this->lastErrorCode = "pass";
                                }
                            } else {
                                $this->lastError = "Kein Benutzername oder Passwort eingegeben!";
                                $this->lastErrorCode = "name";
                            }
                        } else {
                            $this->lastError = "Kein Benutzername oder Passwort eingegeben!";
                            $this->lastErrorCode = "name";
                        }
                    } else {
                        $this->lastError = "Login-Timeout (" . StringUtils::formatTimespan(abs($realtime - $t)) . ")!";
                        $this->lastErrorCode = "logintimeout";
                        $tokenlog = true;
                    }
                } else {
                    $this->lastError = "Login ungültig, Token bereits verwendet!";
                    $this->lastErrorCode = "sameloginkey";
                    $tokenlog = true;
                }
            } else {
                $this->lastError = "Login ungültig, falsches Token!";
                $this->lastErrorCode = "wrongloginkey";
                $tokenlog = true;
            }
        } else {
            $this->lastError = "Login ungültig, kein Token!";
            $this->lastErrorCode = "nologinkey";
            $tokenlog = true;
        }

        if (isset($tokenlog)) {
            $tokenlog = true;
            $text = $this->lastError . "\n";

            if (isset($passwordField) && isset($data[$passwordField]))
                $data[$passwordField] = "*****";
            $text .= "POST: " . var_export($data, true) . "\n";
            if (count($_GET) > 0)
                $text .= "GET: " . var_export($_GET, true) . "\n";
            $text .= "Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
            $text .= "Referer: " . $_SERVER['HTTP_REFERER'] . "\n";
            $this->logRepository->add(LogFacility::ILLEGALACTION, LogSeverity::WARNING, $text);
        }

        return false;
    }

    /**
     * Checks if the current session is valid
     *
     * @return bool, True if session is valid
     */
    function validate(bool $destroy = true): bool
    {
        if (isset($this->time_login)) {
            $userSession = $this->userSessionRepository->findByParameters(session_id(), $this->userId, $_SERVER['HTTP_USER_AGENT'], $this->time_login);
            if ($userSession !== null) {
                $t = time();

                if ($this->time_action + $this->config->getInt('user_timeout') > $t) {
                    $allows = false;
                    $bot = false;
                    if (($t - $this->time_action) >= 5 && ($this->last_span >= $t - $this->time_action - 1 && $this->last_span <= $t - $this->time_action + 1)) {
                        $this->bot_count++;
                        $bot = $this->bot_count > $this->config->getInt('bot_max_count');
                    } else {
                        $this->last_span = $t - $this->time_action;
                        $this->bot_count = 0;
                    }

                    if ($this->sittingActive) {
                        if (time() < $this->sittingUntil) {
                            $activeSitting = $this->userSittingRepository->getActiveUserEntry($this->userId);
                            if ($activeSitting !== null) {
                                $allows = true;
                            }
                        }
                    } else
                        $allows = true;
                    if ($allows) {
                        if (!$bot) {
                            $this->userSessionRepository->update(session_id(), $t, $this->bot_count, $this->last_span, $_SERVER['REMOTE_ADDR']);

                            $this->time_action = $t;
                            return true;
                        } else {
                            $this->lastError = "Die Verwendung von Bots ist nichtgestattet!";
                        }
                    } else {
                        $this->lastError = "Sitting abgelaufen!";
                    }
                } else {
                    $this->lastError = "Das Timeout von " . StringUtils::formatTimespan($this->config->getInt('user_timeout')) . " wurde überschritten!";
                }
            } else {
                $this->lastError = "Session nicht mehr vorhanden!";
            }
        } else {
            $this->lastError = "";
        }
        if ($destroy == 1) {
            // chat logout
            $this->cLogin = false;

            // destroy user session
            $this->sessionManager->unregisterSession();
        }
        return false;
    }

    /**
     * only for session validation in chat, do not use
     * for real validation
     */
    function chatValidate(): bool
    {
        if (
            $this->cLogin &&
            $this->cRemoteAddr == $_SERVER['REMOTE_ADDR'] &&
            $this->cUserAgent == $_SERVER['HTTP_USER_AGENT'] &&
            isset($_SESSION['user_id']) &&
            $this->userId > 0 &&
            $_SESSION['user_id'] == $this->userId
        ) {
            return true;
        }
        return false;
    }

    function registerSession(): void
    {
        $this->userSessionRepository->remove(session_id());
        $this->userSessionRepository->removeForUser($this->userId);
        $this->userSessionRepository->add(session_id(), $this->userId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $this->time_login);
    }

    function logout(): void
    {
        // chat logout
        $this->cLogin = false;

        // destroy session
        $this->sessionManager->unregisterSession();
    }
}
