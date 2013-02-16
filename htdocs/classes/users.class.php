<?PHP

	class Users
	{
		/**
		* Remove inactive users
		*/
		static function removeInactive($manual=false)
		{
			$cfg = Config::getInstance();
	
			$register_time = time()-(24*3600*$cfg->p2('user_inactive_days'));		// Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat
			$online_time = time()-(24*3600*$cfg->p1('user_inactive_days'));	// Zeit nach der ein User normalerweise gelöscht wird
			$inactive_time = time()-(24*3600*USER_INACTIVE_TIME_LONG);
	
			$res =	dbquery("
				SELECT
					user_id
				FROM
					users
				WHERE
					user_ghost='0'
					AND admin=0
					AND ((user_registered<'".$register_time."' AND user_points='0')
					OR (user_logouttime<'".$online_time."' AND user_logouttime>0 AND user_hmode_from='0'));
			");
			$nr = mysql_num_rows($res);
			if ($nr>0)
			{
				while ($arr=mysql_fetch_assoc($res))
				{
					$usr = new User($arr['user_id']);
					$usr->delete();
				}
			}
			if ($manual)
				add_log("4",mysql_num_rows($res)." inaktive User die seit ".date("d.m.Y H:i",$online_time)." nicht mehr online waren oder seit ".date("d.m.Y H:i",$register_time)." keine Punkte haben wurden manuell gelöscht!",time());
			else
				add_log("4",mysql_num_rows($res)." inaktive User die seit ".date("d.m.Y H:i",$online_time)." nicht mehr online waren oder seit ".date("d.m.Y H:i",$register_time)." keine Punkte haben wurden gelöscht!",time());
				
			// Nachricht an lange inaktive
			$res =	dbquery("
				SELECT
					user_id,
					user_nick,
					user_email
				FROM
					users
				WHERE
					user_ghost='0'
					AND admin=0
					AND user_logouttime<'".$inactive_time."' 
					AND user_logouttime>'".($inactive_time-3600*24)."' 
					AND user_hmode_from='0';
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_assoc($res))
				{
				$text ="Hallo ".$arr['user_nick']."
				
	Du hast dich seit mehr als ".USER_INACTIVE_LONG." Tage nicht mehr bei Escape to Andromeda (".Config::getInstance()->roundname->v.") eingeloggt und
	dein Account wurde deshalb als inaktiv markiert. Solltest du dich innerhalb von ".USER_INACTIVE_SHOW." Tage
	nicht mehr einloggen wird der Account gelöscht.
	
	Mit freundlichen Grüssen,
	die Spielleitung";
					$mail = new Mail('Inaktivität',$text);
					$mail->send($arr['user_email']);
					
				}
			}					
				
			return $nr;
		}

		/**
		* Delete user marked as delete
		*/
		static function removeDeleted($manual=false)
		{
			$res =	dbquery("
				SELECT
					user_id
				FROM
					users
				WHERE
					user_deleted>0 && user_deleted<".time()."
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_assoc($res))
				{
					$usr = new User($arr['user_id']);
					$usr->delete();
				}
			}
			if ($manual)
				add_log("4",mysql_num_rows($res)." als gelöscht markierte User wurden manuell gelöscht!",time());
			else
				add_log("4",mysql_num_rows($res)." als gelöscht markierte User wurden gelöscht!",time());
			return mysql_num_rows($res);
		}
		
		/**
		* Remove old point logs
		*/
		static function cleanUpPoints($threshold=0)
		{
			$cfg = Config::getInstance();
			if ($threshold>0)
				$tstamp = time() - $threshold;
			else
				$tstamp = time() - (24*3600*$cfg->get('log_threshold_days'));			
			dbquery("DELETE FROM user_points WHERE point_timestamp<".$tstamp.";");
			$nr = mysql_affected_rows();
			add_log("4","$nr Userpunkte-Logs die älter als ".date("d.m.Y H:i",$tstamp)." sind wurden gelöscht!");
			return $nr;
		}

		/**
		* Abgelaufene Sperren löschen
		*
		*/
		static function removeOldBanns()
		{
			dbquery("
				UPDATE
					users
				SET
					user_blocked_from='0',
					user_blocked_to='0',
					user_ban_reason='',
					user_ban_admin_id='0'
				WHERE
					user_blocked_to<'".time()."';
			");
		}
		
		/**
		* Spionageangriffscounter auf 0 setzen
		*
		*/
		static function resetSpyattacks()
		{
			dbquery("
					UPDATE
						users
					SET
						spyattack_counter='0';
			");
		}

		static function getArray()
		{
			$res = dbquery("SELECT user_id,user_nick FROM users;");
			$rtn = array();
			while ($arr=mysql_fetch_row($res))
			{
				$rtn[$arr[0]] = $arr[1];
			}
			return $rtn;
		}
		
		static function addSittingDays($days=0)
		{
			if ($days==0)
			{
				$cfg = Config::getInstance();
				$days = $cfg->p1("user_sitting_days");
			}
			
			dbquery("UPDATE `users` SET `user_sitting_days`=`user_sitting_days`+'".$days."';");
		}
		
		// check for $conf['hmode_days']['p2'] BEFORE calling this function
		static function setUmodToInactive()
		{
			$cfg = Config::getInstance();
			// set all users who are inactive 
			dbquery('UPDATE
						`users`
					SET
						`user_hmode_from`=0,
						`user_hmode_to`=0,
						`user_logouttime`="'.(time()-USER_INACTIVE_LONG*86400).'" 
					WHERE
						`user_ghost`="0"
					AND
						`admin`=0
					AND
						`user_hmode_from`<"'.(time()-$cfg->p1('hmode_days')*86400).'" 
					;');
			return mysql_affected_rows();
		}
		
	}

?>