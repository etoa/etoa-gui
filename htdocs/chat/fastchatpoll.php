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
			die('bn:'.$arr['reason']); // banned
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
				die('ki:'.$arr['kick']);	//ki = Kicked
			}

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
				$text = $arr['text'];//replace_ascii_control_chars(htmlspecialchars($arr['text']));
				if ($arr['admin']>=1)
				{
					$color = 'y';
					// chatadmins = 2, admins = 1, entwickler = 3
					switch($arr['admin'])
					{
						case 1: $color = 'y';break; //yellow star
						case 2: $color = 'g';break; //grey star
						case 3: $color = 'r';break; //red star
						default:$color = 'y';
					}
					$adminstr = '<img src="../images/star_'.$color.'.gif" />';
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