<?PHP
/* fastchatpoll von river */
	define('RELATIVE_ROOT','../');

	include_once(RELATIVE_ROOT.'inc/bootstrap.inc.php');

	$minId = intval($_POST['minId']); // ISSET?
	$s = $_SESSION;
	if (isset($s['user_id']))
	{
		$gettext = true;
		// User is logged in
		// Query for ban
		$res = dbquery('
		SELECT
			user_id,reason,timestamp
		FROM
			chat_banns
		WHERE
			user_id='.$s['user_id'].';');

		// die if banned
		if(mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			die('bn:'.replace_ascii_control_chars_unicode($arr['reason'])); // banned
		}

		// else query user and kicked
		$res = dbquery('
		SELECT
			user_id,kick
		FROM
			chat_users
		WHERE
			user_id='.$s['user_id'].';');

		if (mysql_num_rows($res)>0)
		{
			// User already exists
			$arr = mysql_fetch_assoc($res);
			if($arr['kick'] != '')
			{
				// User got kicked
				dbquery('
				DELETE FROM
					chat_users
				WHERE
					user_id='.$s['user_id'].';');
				die('ki:'.replace_ascii_control_chars_unicode($arr['kick']));	//ki = Kicked
			}

		}
		else if(isset($_SESSION['chatlogouttime']) && ((time() - $_SESSION['chatlogouttime']) < 3))
		{
			// dirty prevention of re-login while being logged out
			// (interferences between chat and chatinput)
			die('');
		}
		else
		{
			// User does not exist yet
			chatSystemMessage($s['user_nick'].' betritt den Chat.');
			echo 'li:'.$s['user_nick'];
			$gettext = false;
		}

		// Update user timeout
		dbquery('REPLACE INTO
			chat_users
		(
			timestamp,
			nick,
			user_id
		)
		VALUES
		(
			'.time().',
			\''.$s['user_nick'].'\',
			\''.$s['user_id'].'\'
		)');

		if(!$gettext) die();

		// Query new messages
		$res = dbquery('
		SELECT
			id,
			nick,
			timestamp,
			text,
			color,
			user_id,
			private,
			admin
		FROM
			chat
		WHERE
			id>'.$minId.'
		ORDER BY
			timestamp ASC
		');

		if (mysql_num_rows($res)>0)
		{
			// new messages available
			$lastid = $minId;
			$out = '';
			while ($arr=mysql_fetch_assoc($res))
			{
				$adminstr = '';
				$text = replace_ascii_control_chars(htmlspecialchars($arr['text']));
				if ($arr['admin']>=1)
				{
					// chatadmins = 2, admins = 1, entwickler = 3, leiter team community = 4
					switch($arr['admin'])
					{
						case 1: $adminstr = '<img src="../images/star_y.gif" '. //yellow star
								'alt="Admin" title="Admin" />'; break;
						case 2: $adminstr = '<img src="../images/star_s.gif" '. //silver star
								'alt="Chat-Moderator" title="Chat-Moderator" />'; break;
						case 3: $adminstr = '<img src="../images/star_r.gif" '. //red star
								'alt="Entwickler" title="Entwickler" />'; break;
						case 4: $adminstr = '<img src="../images/star_g.gif" '. //green star
								'alt="Leiter Team Community" title="Leiter Team Community" />'; break;
						default:$adminstr = // default: yellow without alt-text
							'<img src="../images/star_y.gif" />'; break;
					}
				}

				if ($arr['user_id']==0)
				{
					$out.= '<span style="color:#aaa">';
					$out.= '&lt;'.date("H:i",$arr['timestamp']).'&gt; '.$text;
					$out.= '</span><br/>';
				}
				elseif ($arr['color']!='')
				{
					$out.= '<span style="color:'.$arr['color'].'">';
					$out.= $adminstr.'&lt;<a style="color:'.$arr['color'].'" href="../index.php?page=userinfo&id='.$arr['user_id'].'" target="main">'.$arr['nick'].'</a> | '.date("H:i",$arr['timestamp']).'&gt; '.$text;
					$out.= '</span><br/>';
				}
				else
				{
					$out.= $adminstr.'&lt;<a style="color:#fff" href="../index.php?page=userinfo&id='.$arr['user_id'].'" target="main">'.$arr['nick'].'</a> | '.date("H:i",$arr['timestamp']).'&gt; '.$text.'<br/>';
				}
				$lastid = $arr['id'];
			}
			echo 'up:'.intval($lastid).':'.$out; //up+':'+int(lastid)+':' = new text available
		}
		//no output = no new text available
	}
	else
	{
		echo 'lo'; // 'lo' = logged out
	}

?>