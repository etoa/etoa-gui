<?php

use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUserRepository;

/**
 * Provides session and authentication management
 * for admin area.
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class AdminSession extends Session
{
	protected $namePrefix = "admin";

	protected AdminSessionRepository $repository;
	protected AdminUserRepository $userRepository;

	protected function __construct()
	{
		parent::__construct();

		global $app;
		$this->repository = $app['etoa.admin.session.repository'];
		$this->userRepository = $app['etoa.admin.user.repository'];
	}

	/**
	 * Returns the single instance of this class
	 *
	 * @return AdminSession Instance of this class
	 */
	public static function getInstance($className = null)
	{
		return parent::getInstance(__CLASS__);
	}

	function login($data)
	{
		self::cleanup();

		// If user login data has been temporary stored (two factor authentication challenge), restore it
		if (empty($data['login_nick']) && !empty($this->tfa_login_nick)) {
			$data['login_nick'] = $this->tfa_login_nick;
		}
		if (empty($data['login_pw']) && !empty($this->tfa_login_pw)) {
			$data['login_pw'] = $this->tfa_login_pw;
		}

		if (!empty($data['login_nick']) && !empty($data['login_pw'])) {
			$user = $this->userRepository->findOneByNick($data['login_nick']);
			if ($user != null) {
				if (validatePasswort($data['login_pw'], $user->passwordString)) {
					// Check if two factor authentication is enabled for this user
					if (!empty($uarr['tfa_secret'])) {
						// Check if user supplied challenge
						if (!empty($data['login_challenge'])) {
							$tfa = new RobThree\Auth\TwoFactorAuth(APP_NAME);
							// Validate challenge. If false, return to challenge input
							if (!$tfa->verifyCode($uarr['tfa_secret'], $data['login_challenge'])) {
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

					$this->user_id = $user->id;
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
	 * @return True if session is valid
	 */
	function validate()
	{
		if (isset($this->time_login)) {
			$exists = $this->repository->exists(
				session_id(),
				intval($this->user_id),
				$_SERVER['HTTP_USER_AGENT'],
				intval($this->time_login),
			);
			if ($exists) {
				$t = time();
				$cfg = Config::getInstance();
				if ($this->time_action + $cfg->admin_timeout->v > $t) {
					$this->repository->update(session_id(), $t, $_SERVER['REMOTE_ADDR']);
					$this->time_action = $t;
					return true;
				} else {
					$this->lastError = "Das Timeout von " . tf($cfg->admin_timeout->v) . " wurde überschritten!";
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
		self::unregisterSession();
		return false;
	}

	function registerSession()
	{
		$this->repository->removeByUserOrId(session_id(), intval($this->user_id));
		$this->repository->create(
			session_id(),
			intval($this->user_id),
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT'],
			intval($this->time_login),
		);
	}

	function logout()
	{
		self::unregisterSession();
	}

	/**
	 * Unregisters a session and save session to session-log
	 *
	 * @param string $sid Session-ID. If null, the current user's session id will be taken
	 * @param bool $logoutPressed True if it was manual logout
	 */
	static function unregisterSession($sid = null, $logoutPressed = 1)
	{
		if ($sid == null) {
			$sid = self::getInstance()->id;
		}

		$adminSession = self::getInstance()->repository->find($sid);
		if ($adminSession != null) {
			self::getInstance()->repository->addSessionLog($adminSession, $logoutPressed == 1 ? time() : 0);
			self::getInstance()->repository->remove($sid);
		}
		if ($logoutPressed == 1) {
			session_regenerate_id(true);
			session_destroy();
		}
	}

	/**
	 * Cleans up sessions with have a timeout. Should be called at login or by cronjob regularly
	 */
	static function cleanup()
	{
		$cfg = Config::getInstance();

		$sessions = self::getInstance()->repository->findByTimeout($cfg->admin_timeout->v);
		foreach ($sessions as $sessions) {
			self::unregisterSession($sessions['id'], 0);
		}
	}

	/**
	 * Removes old session logs from the database
	 * @param int $threshold Time difference in seconds
	 */
	static function cleanupLogs($threshold = 0)
	{
		$cfg = Config::getInstance();

		$timestamp = $threshold > 0
			? time() - $threshold
			: time() - (24 * 3600 * $cfg->sessionlog_store_days->p2);

		$count = self::getInstance()->repository->removeSessionLogs($timestamp);
		Log::add(Log::F_SYSTEM, Log::INFO, "$count Admin-Session-Logs die älter als " . date("d.m.Y, H:i", $timestamp) . " sind wurden gelöscht.");
		return $count;
	}

	/**
	 * Kicks the user with the given session id
	 * @param string $sid Session id
	 */
	static function kick($sid)
	{
		self::unregisterSession($sid, 0);
	}
}
