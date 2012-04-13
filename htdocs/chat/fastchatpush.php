<?PHP
	/* fastchatpush von river */

	define('RELATIVE_ROOT','../');
	include_once(RELATIVE_ROOT.'inc/bootstrap.inc.php');
	include_once(RELATIVE_ROOT.'inc/encoding.inc.php');

	$s = $_SESSION;
	function kickChatUser($uid,$msg = '') 
	{
		if($msg == '')
		{
			$msg = 'Kicked by Admin';
		}
		$res = dbquery('
		UPDATE
			chat_users
		SET
			kick="'.mysql_real_escape_string($msg).'"
		WHERE
			user_id="'.$uid.'"');
		if (mysql_affected_rows()>0)
		{
			return true;
		}
		return false;
	}
	
	if (isset($s['user_id']))
	{
		$admin = 0;
		$res = dbquery('
		SELECT
			user_chatadmin,admin
		FROM
			users
		WHERE
			user_id='.$s['user_id'].';');
		if (mysql_num_rows($res)>0) // Should always be true, otherwise the user does not exist
		{
			// chatadmins = 2, admins = 1, entwickler = 3
			$arr = mysql_fetch_assoc($res);
			if($arr['admin'] == 1)
				$admin = 1; // Admin
			elseif ($arr['user_chatadmin'] == 1)
				$admin = 2; // Chatadmin
			elseif ($arr['user_chatadmin'] == 2)
				$admin = 4; // Leiter Team Community
			elseif($arr['admin'] == 2)
				$admin = 3; // Entwickler
		}
		else
		{
			die('nu'); // no user
		}

		$ct = $_POST['ctext'];
		$isCommand = false;

		if ( $admin > 0 && $admin != 3) // Keine Kommandos für Entwickler
		{
			$m = array();
			if (preg_match('#^/kick (.[^\'\"\?\<\>\$\!\=\;\&\s]+)$#',$ct,$m)>0)
			{
				$isCommand = true;
				$uid = User::findIdByNick($m[1]);
				if ($uid>0)
				{
					if (kickChatUser($uid))
					{
						chatSystemMessage($m[1].' wurde gekickt!');
					}
					else
					{
						die('aa:User is not online in chat!');
					}
				}
				else
				{
					die('aa:A user with this nick does not exist!');
				}
			}
			elseif (preg_match('#^/kick (.[^\'\"\?\<\>\$\!\=\;\&\s]+)\s(.+)$#',$ct,$m)>0)
			{
				$isCommand = true;
				$text = '';
				if (isset($m[2]))
					$text = $m[2];
				$uid = User::findIdByNick($m[1]);
				if ($uid>0)
				{
					if (kickChatUser($uid,$text))
					{
						chatSystemMessage($m[1].' wurde gekickt! Grund: '.$text);
					}
					else
					{
						die('aa:User is not online in chat!');
					}
				}
				else
				{
					die('aa:A user with this nick does not exist!');
				}
			}
			elseif (preg_match('#^/ban (.[^\'\"\?\<\>\$\!\=\;\&\s]+)\s([A-Za-z0-9\s]+)$#',$ct,$m)>0)
			{
				$isCommand = true;
				$text = isset($m[2]) ? $m[2] : '';
				$uid = User::findIdByNick($m[1]);
				if ($uid>0)
				{
					dbquery('INSERT INTO
						chat_banns
							(user_id,reason,timestamp)
						VALUES ('.$uid.',"'.mysql_real_escape_string($text).'",'.time().')
						ON DUPLICATE KEY UPDATE
							timestamp='.time().',reason="'.mysql_real_escape_string($text).'"');
					kickChatUser($uid,$text);
					chatSystemMessage($m[1].' wurde gebannt! Grund: '.$text);
				}
				else
				{
					die('aa:A user with this nick does not exist!');
				}
			}
			elseif (preg_match('#^/unban (.[^\'\"\?\<\>\$\!\=\;\&\s]+)$#',$ct,$m)>0)
			{
				$isCommand = true;
				$uid = User::findIdByNick($m[1]);
				if ($uid>0)
				{
					dbquery('DELETE FROM
						chat_banns
					WHERE
						user_id='.$uid.';');
					if (mysql_affected_rows()>0)
					{
						die('aa:Unbanned '.$m[1].'!');
					}
					else
					{
						die('aa:A user with that nick is not banned!');
					}
				}
				else
				{
					die('aa:A user with this nick does not exist!');
				}			
			}
			elseif (preg_match('#^/banlist$#',$ct,$m)>0)
			{
				$isCommand = true;
				$res = dbquery('SELECT
					user_id,reason,timestamp
				FROM
					chat_banns
				;');
				if (mysql_num_rows($res)>0)
				{
					$out='';
					while ($arr=mysql_fetch_assoc($res))
					{
						$tu = new User($arr['user_id']);
						if ($tu->isValid)
						{
							$out.= $tu->nick.': '.$arr['reason'].' ('.df($arr['timestamp']).")\n";
						}
					}
					die('bl:'.$out);
				}
				else
				{
					die('aa:Bannliste leer!');
				}
			}
		}
		if(!$isCommand)
		{
			$hash = md5($ct);
			// Woo Hoo, Md5 hashtable
			if ($ct!='' && (!isset($_SESSION['lastchatmsg']) || $_SESSION['lastchatmsg']!= $hash) )
			{
				dbquery("INSERT INTO
					chat
				(
					timestamp,
					nick,
					text,
					color,
					user_id,
					admin
				)
				VALUES
				(
					".time().",
					'".$s['user_nick']."',
					'".mysql_real_escape_string(($ct))."',
					'".(isset($_SESSION['ccolor'])?('#'.$_SESSION['ccolor']):'')."',
					'".$s['user_id']."',
					'".$admin."'
				);");
				dbquery("INSERT INTO
					chat_log
				(
					timestamp,
					nick,
					text,
					color,
					user_id,
					admin
				)
				VALUES
				(
					".time().",
					'".$s['user_nick']."',
					'".mysql_real_escape_string(($ct))."',
					'".(isset($_SESSION['ccolor'])?('#'.$_SESSION['ccolor']):'')."',
					'".$s['user_id']."',
					'".$admin."'
				);");			
				$_SESSION['lastchatmsg']=$hash;
			}
			else
			{
				die('de'); // zweimal gleiche Nachricht nacheinander
			}
		}
	}
	else
	{
		die('nl'); // !isset $s[userid] => not logged in
	}

?>