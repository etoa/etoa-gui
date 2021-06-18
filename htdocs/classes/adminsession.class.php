<?php
/**
 * Provides session and authentication management
 * for admin area.
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class AdminSession extends Session
{
	const tableUser = "admin_users";
	const tableSession = "admin_user_sessions";
	const tableLog = "admin_user_sessionlog";

	protected $namePrefix = "admin";

	function login($data)
	{
		self::cleanup();

		// If user login data has been temporary stored (two factor authentication challenge), restore it
		if (!isset($data['login_nick']) && $this->tfa_login_nick) {
			$data['login_nick'] = $this->tfa_login_nick;
		}
		if (!isset($data['login_pw']) && $this->tfa_login_pw) {
			$data['login_pw'] = $this->tfa_login_pw;
		}

		if ($data['login_nick'] && $data['login_pw'])
		{
			$sql = "
			SELECT
				user_id,
				user_nick,
				user_password,
				tfa_secret
			FROM
				".self::tableUser."
			WHERE
				LCASE(user_nick)=LCASE(?)
			LIMIT 1;
			;";
			$ures = dbQuerySave($sql,array($data['login_nick']));
			if (mysql_num_rows($ures)>0)
			{
				$uarr = mysql_fetch_assoc($ures);
				if (validatePasswort($data['login_pw'], $uarr['user_password']))
				{
					// Check if two factor authentication is enabled for this user
					if ($uarr['tfa_secret']) {
						// Check if user supplied challenge
						if (isset($data['login_challenge'])) {
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

					$this->user_id = (int) $uarr['user_id'];
					$this->user_nick = (string) $uarr['user_nick'];
					$t = time();
					$this->time_login = $t;
					$this->time_action = $t;
					$this->registerSession();

					$this->firstView = true;
					return true;
				}
				else
				{
					$this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
					$this->lastErrorCode = "pass";
				}
			}
			else
			{
				$this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
				$this->lastErrorCode = "pass";
			}
		}
		else
		{
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
		if (isset($this->time_login))
		{
			$sql = "
			SELECT
				id
			FROM
				`".self::tableSession."`
			WHERE
				id='".session_id()."'
				AND `user_id`=".intval($this->user_id)."
				AND `user_agent`='".$_SERVER['HTTP_USER_AGENT']."'
				AND `time_login`=".intval($this->time_login)."
			LIMIT 1
			;";
			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				$t = time();
				$cfg = Config::getInstance();
				if ($this->time_action + (int) $cfg->admin_timeout->v > $t)
				{
					dbquery("
					UPDATE
						`".self::tableSession."`
					SET
						time_action=".$t.",
						ip_addr='".$_SERVER['REMOTE_ADDR']."'
					WHERE
						id='".session_id()."'
					;");
					$this->time_action = $t;
					return true;
				}
				else
				{
					$this->lastError = "Das Timeout von ".tf($cfg->admin_timeout->v)." wurde überschritten!";
					$this->lastErrorCode = "timeout";
				}
			}
			else
			{
				$this->lastError = "Session nicht mehr vorhanden!";
				$this->lastErrorCode = "nosession";
			}
		}
		else
		{
			$this->lastError = "Keine Session!";
			$this->lastErrorCode = "nologin";
		}
		self::unregisterSession();
		return false;
	}

	function registerSession()
	{
		$sql = "
		DELETE FROM
			`".self::tableSession."`
		WHERE
			user_id=".intval($this->user_id)."
			OR id='".session_id()."'
		;";
		dbquery($sql);

		$sql = "
		INSERT INTO
			`".self::tableSession."`
		(
			`id` ,
			`user_id`,
			`ip_addr`,
			`user_agent`,
			`time_login`
		)
		VALUES
		(
			'".session_id()."',
			".intval($this->user_id).",
			'".$_SERVER['REMOTE_ADDR']."',
			'".$_SERVER['HTTP_USER_AGENT']."',
			".intval($this->time_login)."
		)
		";
		$res = dbquery($sql);
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
	static function unregisterSession($sid=null,$logoutPressed= true)
	{
		if ($sid == null)
			$sid = self::getInstance()->id;

		$res = dbquery("
		SELECT
			*
		FROM
			`".self::tableSession."`
		WHERE
			id='".$sid."'
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			dbquery("
			INSERT INTO
				`".self::tableLog."`
			(
				`session_id` ,
				`user_id`,
				`ip_addr`,
				`user_agent`,
				`time_login`,
				`time_action`,
				`time_logout`
			)
			VALUES
			(
				'".$arr['id']."',
				'".$arr['user_id']."',
				'".$arr['ip_addr']."',
				'".$arr['user_agent']."',
				'".$arr['time_login']."',
				'".$arr['time_action']."',
				'".($logoutPressed ? time() : 0)."'
			)
			");
			dbquery("
			DELETE FROM
				`".self::tableSession."`
			WHERE
				id='".$sid."'
			;");
		}
        if ($logoutPressed)
        {
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

		$res = dbquery("
		SELECT
			id
		FROM
			`".self::tableSession."`
		WHERE
			time_action+".($cfg->admin_timeout->v)." < '".time()."'
		;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_row($res))
			{
				self::unregisterSession($arr[0],false);
			}
		}
	}

	/**
	 * Removes old session logs from the database
	 * @param int $threshold Time difference in seconds
	 */
	static function cleanupLogs($threshold=0)
	{
		$cfg = Config::getInstance();
		if ($threshold>0)
			$tstamp = time() - $threshold;
		else
			$tstamp = time() - (24*3600*$cfg->sessionlog_store_days->p2);
		dbquery("
		DELETE FROM
			`".self::tableLog."`
		WHERE
			time_action < ".$tstamp.";");
		$nr = mysql_affected_rows();
		Log::add(Log::F_SYSTEM, Log::INFO, "$nr Adminsession-Logs die älter als ".date("d.m.Y, H:i",$tstamp)." sind wurden gelöscht.");
		return $nr;
	}

	/**
	 * Kicks the user with the given session id
	 * @param string $sid Session id
	 */
	static function kick($sid)
	{
		self::unregisterSession($sid,false);
	}
}
?>
