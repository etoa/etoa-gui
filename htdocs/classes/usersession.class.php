<?php

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionManager;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSittingRepository;

/**
 * Provides session and authentication management
 * for player area.
 */
class UserSession extends Session
{
    function login($data)
    {
        // TODO
        global $app;

        /** @var UserSessionManager $sessionManager */
        $sessionManager = $app[UserSessionManager::class];

        /** @var UserSittingRepository $userSittingRepository */
        $userSittingRepository = $app[UserSittingRepository::class];

        /** @var UserLoginFailureRepository $userLoginFailureRepository */
        $userLoginFailureRepository = $app[UserLoginFailureRepository::class];

        /** @var UserRepository $userRepository */
        $userRepository = $app[UserRepository::class];
        /** @var LogRepository $logRepository */
        $logRepository = $app[LogRepository::class];

        $sessionManager->cleanup();

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
                                $user = $userRepository->getUserByNick($loginNick);
                                if ($user !== null) {
                                    $t = time();

                                    // check sitter
                                    $this->sittingActive = false;
                                    $this->falseSitter = false;
                                    $sittingEntry = $userSittingRepository->getActiveUserEntry($user->id);
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
                                            $user->password = $userRepository->updatePassword($user->id, $pw);
                                        }
                                    }

                                    if (
                                        validatePasswort($loginPassword, $user->password)
                                        || $this->sittingActive
                                        || ($user->passwordTemp != "" && $user->passwordTemp == $loginPassword)
                                    ) {
                                        session_regenerate_id(true);

                                        $this->user_id = $user->id;
                                        $this->user_nick = $user->nick;
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
                                        $userLoginFailureRepository->add($user->id, $t, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
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
                        $this->lastError = "Login-Timeout (" . tf(abs($realtime - $t)) . ")!";
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
            $logRepository->add(LogFacility::ILLEGALACTION, LogSeverity::WARNING, $text);
        }

        return false;
    }

    /**
     * Checks if the current session is valid
     *
     * @return bool, True if session is valid
     */
    function validate($destroy = 1)
    {
        // TODO
        global $app;

        /** @var UserSessionRepository $userSessionRepository */
        $userSessionRepository = $app[UserSessionRepository::class];

        /** @var UserSittingRepository $userSittingRepository */
        $userSittingRepository = $app[UserSittingRepository::class];

        /** @var UserSessionManager $sessionManager */
        $sessionManager = $app[UserSessionManager::class];

        if (isset($this->time_login)) {
            $userSession = $userSessionRepository->findByParameters(session_id(), $this->user_id, $_SERVER['HTTP_USER_AGENT'], $this->time_login);
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
                            $activeSitting = $userSittingRepository->getActiveUserEntry($this->user_id);
                            if ($activeSitting !== null) {
                                $allows = true;
                            }
                        }
                    } else
                        $allows = true;
                    if ($allows) {
                        if (!$bot) {
                            $userSessionRepository->update(session_id(), $t, $this->bot_count, $this->last_span, $_SERVER['REMOTE_ADDR']);

                            $this->time_action = $t;
                            return true;
                        } else {
                            $this->lastError = "Die Verwendung von Bots ist nichtgestattet!";
                        }
                    } else {
                        $this->lastError = "Sitting abgelaufen!";
                    }
                } else {
                    $this->lastError = "Das Timeout von " . tf($this->config->getInt('user_timeout')) . " wurde überschritten!";
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
            $sessionManager->unregisterSession();
        }
        return false;
    }

    /**
     * only for session validation in chat, do not use
     * for real validation
     */
    function chatValidate()
    {
        if (
            $this->cLogin == true &&
            $this->cRemoteAddr == $_SERVER['REMOTE_ADDR'] &&
            $this->cUserAgent == $_SERVER['HTTP_USER_AGENT'] &&
            isset($_SESSION['user_id']) &&
            $this->user_id > 0 &&
            $_SESSION['user_id'] == $this->user_id
        ) {
            return true;
        }
        return false;
    }

    function registerSession()
    {
        // TODO
        global $app;

        /** @var UserSessionRepository $userSessionRepository */
        $userSessionRepository = $app[UserSessionRepository::class];

        $userSessionRepository->remove(session_id());
        $userSessionRepository->removeForUser($this->user_id);

        $userSessionRepository->add(session_id(), $this->user_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $this->time_login);
    }

    function logout()
    {
        // TODO
        global $app;

        /** @var UserSessionManager $sessionManager */
        $sessionManager = $app[UserSessionManager::class];

        // chat logout
        $this->cLogin = false;

        // destroy session
        $sessionManager->unregisterSession();
    }
}
