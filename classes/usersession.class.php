<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of usersession
 *
 * @author Nicolas
 */
class UserSession extends Session
{
	const tableUser = "users";
	const tableSession = "user_sessions";
	const tableLog = "user_sessionlog";

	protected $namePrefix = "user";

	public static function getInstance()
	{
		if (empty(self::$instance))
		{
			$className = __CLASS__;
			self::$instance = new $className(func_get_args());
		}
		return self::$instance;
	}

	function login($data)
	{
		$loginTimeDifferenceThreshold = 3600;
		
		if (isset($data['token']))
		{
			$t = hexdec(substr($data['token'],40));
			$logintoken = sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$t).dechex($t);

			// Check token (except if user is form localhost = developer)
			if ($logintoken == $data['token'] || $_SERVER['REMOTE_ADDR']=="127.0.0.1" || $_SERVER['REMOTE_ADDR']=="::1")
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
									LCASE(user_nick)='".strtolower(dbEscapeStr($loginNick))."'
								LIMIT 1;
								;";
								$ures = dbquery($sql);
								if (mysql_num_rows($ures)>0)
								{
									$uarr = mysql_fetch_assoc($ures);
									$pw = pw_salt($loginPassword,$uarr['user_registered']);
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
										if ($sarr[1] == $pw)
										{
											$this->sittingActive = true;
											$this->sittingUntil = $sarr[0];
										}
										elseif ($uarr['user_password'] == $pw)
										{
											$this->falseSitter = true;
											$this->sittingActive = true;
											$this->sittingUntil = $sarr[0];
										}
									}

									if ($uarr['user_password'] == $pw
										|| $this->sittingActive
										|| ($uarr['user_password_temp']!="" && $uarr['user_password_temp']==$loginPassword))
									{
										$this->user_id = $uarr['user_id'];
										$this->user_nick = $uarr['user_nick'];
										$this->time_login = $t;
										$this->time_action = $t;
										$this->registerSession();
										$this->bot_count = 0;
										$this->firstView = true;
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

									}
									else
									{
										$this->lastError = "Passwort oder Benutzername falsch!";
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
				AND `ip_addr`='".$_SERVER['REMOTE_ADDR']."'
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
								last_span='".$this->last_span."'
								
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
			self::unregisterSession();
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
		self::unregisterSession();
	}

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
            session_destroy();
            session_regenerate_id();
        }
	}

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

	static function kick($sid)
	{
		self::unregisterSession($sid,0);
	}

}
?>
