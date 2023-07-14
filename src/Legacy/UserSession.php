<?php

namespace EtoA\Legacy;

use EtoA\Core\Configuration\ConfigurationService;
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
     * @var bool True if this is the first run (page) of the session, that means if the user hast just logged in
     */
    private bool $firstView = false;

    private int $bot_count;

    private int $last_span;

    private int $time_action;

    private int $time_login;

    private int $userId;

    private string $userNick;

    private bool $sittingActive;

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

    public function login(string $loginNick, string $loginPassword): bool
    {
        $this->sessionManager->cleanup();

        if (!filled($loginNick) || !filled($loginPassword)) {
            $this->lastError = "Kein Benutzername oder Passwort eingegeben!";
            return false;
        }

        $user = $this->userRepository->getUserByNick($loginNick);
        if ($user === null) {
            $this->lastError = "Der Benutzername ist in dieser Runde nicht registriert!";
            return false;
        }

        $t = time();

        // check sitter
        $this->sittingActive = false;
        $sittingEntry = $this->userSittingRepository->getActiveUserEntry($user->id);
        if ($sittingEntry !== null) {
            if (validatePasswort($loginPassword, $sittingEntry->password)) {
                $this->sittingActive = true;
                $this->sittingUntil = $sittingEntry->dateTo;
            } elseif (validatePasswort($loginPassword, $user->password)) {
                // false sitter
                $this->sittingActive = true;
                $this->sittingUntil = $sittingEntry->dateTo;
            }
        }

        // Validate password
        $validPassword = validatePasswort($loginPassword, $user->password) || $this->sittingActive || ($user->passwordTemp != "" && $user->passwordTemp == $loginPassword);
        if (!$validPassword) {
            $this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
            $this->userLoginFailureRepository->add($user->id, $t, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
            return false;
        }

        session_regenerate_id(true);

        $this->userId = $user->id;
        $this->userNick = $user->nick;
        $this->time_login = $t;
        $this->time_action = $t;
        $this->registerSession();
        $this->bot_count = 0;
        $this->firstView = true;

        return true;
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
                    } else {
                        $allows = true;
                    }
                    if ($allows) {
                        if (!$bot) {
                            $this->userSessionRepository->update(session_id(), $t, $this->bot_count, $this->last_span, $_SERVER['REMOTE_ADDR']);

                            $this->time_action = $t;
                            return true;
                        } else {
                            $this->lastError = "Die Verwendung von Bots ist nicht gestattet!";
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
            // destroy user session
            $this->sessionManager->unregisterSession();
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
        // destroy session
        $this->sessionManager->unregisterSession();
    }
}
