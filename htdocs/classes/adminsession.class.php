<?php

use EtoA\Admin\AdminSessionManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUserRepository;

/**
 * Provides session and authentication management
 * for admin area.
 */
class AdminSession extends Session
{
    protected $namePrefix = "admin";

    function login($data)
    {
        // TODO
        global $app;

        /** @var AdminSessionManager */
        $sessionManager = $app['etoa.admin.session.manager'];

        /** @var AdminUserRepository */
        $userRepository = $app['etoa.admin.user.repository'];

        $sessionManager->cleanup();

        // If user login data has been temporary stored (two factor authentication challenge), restore it
        if (!isset($data['login_nick']) && $this->tfa_login_nick) {
            $data['login_nick'] = $this->tfa_login_nick;
        }
        if (!isset($data['login_pw']) && $this->tfa_login_pw) {
            $data['login_pw'] = $this->tfa_login_pw;
        }

        if ($data['login_nick'] && $data['login_pw']) {
            $user = $userRepository->findOneByNick($data['login_nick']);
            if ($user != null) {
                if (validatePasswort($data['login_pw'], $user->passwordString)) {
                    // Check if two factor authentication is enabled for this user
                    if ($user->tfaSecret != "") {
                        // Check if user supplied challenge
                        if (isset($data['login_challenge'])) {
                            $tfa = new RobThree\Auth\TwoFactorAuth(APP_NAME);
                            // Validate challenge. If false, return to challenge input
                            if (!$tfa->verifyCode($user->tfaSecret, $data['login_challenge'])) {
                                $this->lastError = "Ungültiger Code!";
                                $this->lastErrorCode = "tfa_challenge";
                                return false;
                            }
                        }
                        // User needs to supply challenge
                        else {
                            // Temporary store users login data
                            $this->tfa_login_nick = $data['login_nick'];
                            $this->tfa_login_pw = $data['login_pw'];
                            $this->lastErrorCode = "tfa_challenge";
                            return false;
                        }
                    }

                    // Unset temporary stored user login data
                    unset($this->tfa_login_nick);
                    unset($this->tfa_login_pw);

                    session_regenerate_id(true);

                    $this->user_id = (int) $user->id;
                    $this->user_nick = $user->nick;
                    $t = time();
                    $this->time_login = $t;
                    $this->time_action = $t;
                    $this->registerSession();

                    $this->firstView = true;
                    return true;
                } else {
                    $this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
                    $this->lastErrorCode = "pass";
                }
            } else {
                $this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
                $this->lastErrorCode = "pass";
            }
        } else {
            $this->lastError = "Kein Benutzername oder Passwort eingegeben oder ungültige Zeichen verwendet!";
            $this->lastErrorCode = "name";
        }
        // Unset temporary stored user login data
        unset($this->tfa_login_nick);
        unset($this->tfa_login_pw);
        return false;
    }

    /**
     * Checks if the current session is valid
     *
     * @return bool true if session is valid
     */
    function validate()
    {
        // TODO
        global $app;

        /** @var AdminSessionManager */
        $sessionManager = $app['etoa.admin.session.manager'];

        /** @var AdminSessionRepository */
        $repository = $app['etoa.admin.session.repository'];

        if (isset($this->time_login)) {
            $exists = $repository->exists(
                session_id(),
                intval($this->user_id),
                $_SERVER['HTTP_USER_AGENT'],
                intval($this->time_login),
            );
            if ($exists) {
                $t = time();
                if ($this->time_action + $this->config->getInt('admin_timeout') > $t) {
                    $repository->update(session_id(), $t, $_SERVER['REMOTE_ADDR']);
                    $this->time_action = $t;
                    return true;
                } else {
                    $this->lastError = "Das Timeout von " . tf($this->config->getInt('admin_timeout')) . " wurde überschritten!";
                    $this->lastErrorCode = "timeout";
                }
            } else {
                $this->lastError = "Session nicht mehr vorhanden!";
                $this->lastErrorCode = "nosession";
            }
        } else {
            $this->lastError = "Keine Session!";
            $this->lastErrorCode = "nologin";
        }
        $sessionManager->unregisterSession(session_id());
        return false;
    }

    function registerSession()
    {
        // TODO
        global $app;

        /** @var AdminSessionRepository */
        $repository = $app['etoa.admin.session.repository'];

        $repository->removeByUserOrId(session_id(), intval($this->user_id));
        $repository->create(
            session_id(),
            intval($this->user_id),
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'],
            intval($this->time_login),
        );
    }

    function logout()
    {
        // TODO
        global $app;

        /** @var AdminSessionManager */
        $sessionManager = $app['etoa.admin.session.manager'];

        $sessionManager->unregisterSession(session_id());
    }
}
