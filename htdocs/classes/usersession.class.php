<?php
/**
 * Provides session and authentication management
 * for player area.
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 *
 * @property int $user_id
 * @property string $user_nick
 */
class UserSession extends Session
{
	const tableUser = "users";
	const tableSession = "user_sessions";
	const tableLog = "user_sessionlog";

	protected $namePrefix = "user";

    /**
	 * Returns the single instance of this class
	 *
	 * @return UserSession Instance of this class
	 */
	public static function getInstance($className = null)
	{
		return parent::getInstance(__CLASS__);
	}

	function login($data)
	{
		self::cleanup();

		$loginTimeDifferenceThreshold = 3600;

		if (isset($data['token']))
		{
			$t = hexdec(substr($data['token'],40));
			$logintoken = sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$t).dechex($t);

			// Check token (except if user is form localhost = developer)
			// Disable this check until https login proglems solved
			if (true || $logintoken == $data['token'] || $_SERVER['REMOTE_ADDR']=="127.0.0.1" || $_SERVER['REMOTE_ADDR']=="::1")
			{
				if (!isset($_SESSION['used_login_tokens']))
					$_SESSION['used_login_tokens'] = array();

				$logintoken = $data['token'];
				$nickField = sha1("nick".$logintoken.$t);
				$passwordField = sha1("password".$logintoken.$t);

				$this->passwordField = $passwordField;

				// Check if token has not already been used (multi logins with browser auto-refresher)
				if (!in_array($logintoken,$_SESSION['used_login_tokens']))
				{
					$_SESSION['used_login_tokens'][] = $logintoken;

					// Check if login is withing given time bounds (+- one hour)
					$realtime = time();
					if ($t + $loginTimeDifferenceThreshold >= $realtime && $t - $loginTimeDifferenceThreshold <= $realtime)
					{
						// Check if the user and password fields are set
						if (isset($data[$nickField]) && isset($data[$passwordField]))
						{
							$loginNick = trim($data[$nickField]);
							$loginPassword = trim($data[$passwordField]);

							if ($loginNick!="" && $loginPassword!="" ) // Add here regex check for nickname
							{
								$sql = "
								SELECT
									user_id,
									user_nick,
									user_registered,
									user_password,
									user_password_temp
								FROM
									".self::tableUser."
								WHERE
									LCASE(user_nick)=?
								LIMIT 1;
								;";
								$ures = dbQuerySave($sql, array(strtolower($loginNick)));
								if (mysql_num_rows($ures)>0)
								{
									$uarr = mysql_fetch_assoc($ures);
									$t = time();

									// check sitter
									$this->sittingActive = false;
									$this->falseSitter = false;
									$sres = dbquery("
									SELECT
										date_to,
										password
									FROM user_sitting
									WHERE
										user_id=".$uarr['user_id']."
										AND date_from<=$t
										AND $t<=date_to ;");
									if (mysql_num_rows($sres)>0)
									{
										$sarr = mysql_fetch_row($sres);
										if (validatePasswort($loginPassword, $sarr[1]))
										{
											$this->sittingActive = true;
											$this->sittingUntil = $sarr[0];
										}
										elseif (validatePasswort($loginPassword, $uarr['user_password']))
										{
											$this->falseSitter = true;
											$this->sittingActive = true;
											$this->sittingUntil = $sarr[0];
										}
									}
									if (strlen($uarr['user_password']) == 64) {
										$pw = $loginPassword;
										$seed = $uarr['user_registered'];
										$salt = "yheaP;BXf;UokIAJ4dhaOL"; // Round 9
										if ($uarr['user_password'] == md5($pw.$seed.$salt).md5($salt.$seed.$pw)) {
											$newPw = saltPasswort($pw);
											dbquery("UPDATE 
												users 
											SET 
												user_password='".$newPw."' 
											WHERE 
												user_id='".$uarr['user_id']."' 
											;");
											$uarr['user_password'] = $newPw;
										}
									}

									if (validatePasswort($loginPassword, $uarr['user_password'])
										|| $this->sittingActive
										|| ($uarr['user_password_temp']!="" && $uarr['user_password_temp']==$loginPassword))
									{
										session_regenerate_id(true);

										$this->user_id = $uarr['user_id'];
										$this->user_nick = $uarr['user_nick'];
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
									}
									else
									{
										$this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
										dbquery("
												INSERT INTO
													login_failures
												(
													failure_time,
													failure_ip,
													failure_user_id,
													failure_pw,
													failure_client
												)
												VALUES
												(
													'".$t."',
													'".$_SERVER['REMOTE_ADDR']."',
													'".$uarr['user_id']."',
													'".$pw."',
													'".$_SERVER['HTTP_USER_AGENT']."'
												);");
										$this->lastErrorCode = "pass";
									}
								}
								else
								{
									$this->lastError = "Der Benutzername ist in dieser Runde nicht registriert!";
									$this->lastErrorCode = "pass";
								}
							}
							else
							{
								$this->lastError = "Kein Benutzername oder Passwort eingegeben!";
								$this->lastErrorCode = "name";
							}
						}
						else
						{
							$this->lastError = "Kein Benutzername oder Passwort eingegeben!";
							$this->lastErrorCode = "name";
						}
					}
					else
					{
						$this->lastError = "Login-Timeout (".tf(abs($realtime-$t)).")!";
						$this->lastErrorCode = "logintimeout";
						$tokenlog = true;
					}
				}
				else
				{
					$this->lastError = "Login ungültig, Token bereits verwendet!";
					$this->lastErrorCode = "sameloginkey";
					$tokenlog = true;
				}
			}
			else
			{
				$this->lastError = "Login ungültig, falsches Token!";
				$this->lastErrorCode = "wrongloginkey";
				$tokenlog = true;
			}
		}
		else
		{
			$this->lastError = "Login ungültig, kein Token!";
			$this->lastErrorCode = "nologinkey";
			$tokenlog = true;
		}

		if (isset($tokenlog))
		{
			$tokenlog = true;
			$text = $this->lastError."\n";

			if (isset($passwordField) && isset($data[$passwordField]))
				$data[$passwordField] = "*****";
			$text.= "POST: ".var_export($data,true)."\n";
			if (count($_GET)>0)
				$text.= "GET: ".var_export($_GET,true)."\n";
			$text.= "Agent: ".$_SERVER['HTTP_USER_AGENT']."\n";
			$text.= "Referer: ".$_SERVER['HTTP_REFERER']."\n";
			Log::add(Log::F_ILLEGALACTION, Log::WARNING, $text);
		}

		return false;
	}

	/**
	 * Checks if the current session is valid
	 *
	 * @return bool, True if session is valid
	 */
	function validate($destroy=1)
	{
		if (isset($this->time_login))
		{
			$res = dbquery("
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
			;");
			if (mysql_num_rows($res)>0)
			{
				$t = time();
				$cfg = Config::getInstance();
				if ($this->time_action + $cfg->user_timeout->v > $t)
				{
					$allows = false;
					$bot = false;
					if (($t-$this->time_action)>=5 && ($this->last_span >= $t-$this->time_action-1 && $this->last_span <= $t-$this->time_action+1))
					{
						$this->bot_count++;
						if ($this->bot_count>$cfg->bot_max_count->v) $bot = true;
					}
					else
					{
						$this->last_span = $t-$this->time_action;
						$this->bot_count=0;
					}

					if ($this->sittingActive)
					{
						if (time() < $this->sittingUntil)
						{
							$t = time();
							$res = dbquery("SELECT
								id
							FROM user_sitting
							WHERE
								user_id=".$this->user_id."
								AND date_from<=$t
								AND $t<=date_to ;");
							if (mysql_num_rows($res)>0)
							{
								$allows = true;
							}
						}
					}
					else
						$allows = true;
					if ($allows)
					{
						if (!$bot)
						{
							dbquery("
							UPDATE
								`".self::tableSession."`
							SET
								time_action=".$t.",
								bot_count='".$this->bot_count."',
								last_span='".$this->last_span."',
								ip_addr='".$_SERVER['REMOTE_ADDR']."'
							WHERE
								id='".session_id()."'
							;");

							$this->time_action = $t;
							return true;
						}
						else
						{
							$this->lastError = "Die Verwendung von Bots ist nichtgestattet!";
						}
					}
					else
					{
						$this->lastError = "Sitting abgelaufen!";
					}
				}
				else
				{
					$this->lastError = "Das Timeout von ".tf($cfg->user_timeout->v)." wurde überschritten!";
				}
			}
			else
			{
				$this->lastError = "Session nicht mehr vorhanden!";
			}
		}
		else
		{
			$this->lastError = "";
		}
		if ($destroy==1)
		{
			// chat logout
			$this->cLogin = false;
			// destroy user session
			self::unregisterSession();
		}
		return false;
	}

	/**
	 * only for session validation in chat, do not use
	 * for real validation
	 */
	function chatValidate()
	{
		if($this->cLogin == true &&
		   $this->cRemoteAddr == $_SERVER['REMOTE_ADDR'] &&
		   $this->cUserAgent == $_SERVER['HTTP_USER_AGENT'] &&
		   isset($_SESSION['user_id']) &&
		   $this->user_id > 0 &&
		   $_SESSION['user_id'] == $this->user_id
		)
		{
			return true;
		}
		return false;
	}

	function registerSession()
	{
		dbquery("
		DELETE FROM
			`".self::tableSession."`
		WHERE
			user_id=".intval($this->user_id)."
			OR id='".session_id()."'
		;");
		dbquery("
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
		");
	}

	function logout()
	{
		// chat logout
		$this->cLogin = false;
		// destroy session
		self::unregisterSession();
	}

	/**
	 * Unregisters a session and save session to session-log
	 *
	 * @param string $sid Session-ID. If null, the current user's session id will be taken
	 * @param bool $logoutPressed True if it was manual logout
	 */
	static function unregisterSession($sid=null,$logoutPressed=1)
	{
		if ($sid == null)
			$sid = session_id();

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
				'".($logoutPressed==1 ? time() : 0)."'
			)
			");
			dbquery("
			DELETE FROM
				`".self::tableSession."`
			WHERE
				id='".$sid."'
			;");

			dbquery("
					UPDATE
						users
					SET
						user_logouttime='".time()."'
					WHERE
						user_id='".$arr['user_id']."'
					LIMIT 1;");
		}
        if ($logoutPressed==1)
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
			time_action+".($cfg->user_timeout->v)." < '".time()."'
		;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_row($res))
			{
				self::unregisterSession($arr[0],0);
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
			$tstamp = time() - (24*3600*$cfg->sessionlog_store_days->p1);
		dbquery("
		DELETE FROM
			`".self::tableLog."`
		WHERE
			time_action < ".$tstamp.";");
		$nr = mysql_affected_rows();
		Log::add(Log::F_SYSTEM, Log::INFO, "$nr Usersession-Logs die älter als ".date("d.m.Y, H:i",$tstamp)." sind wurden gelöscht.");
		return $nr;
	}

	/**
	 * Kicks the user with the given session id
	 * @param string $sid Session id
	 */
	static function kick($sid)
	{
		self::unregisterSession($sid,0);
	}
}
?>
