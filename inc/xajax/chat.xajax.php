<?PHP
$xajax->register(XAJAX_FUNCTION,'loadChat');
$xajax->register(XAJAX_FUNCTION,'sendChat');
$xajax->register(XAJAX_FUNCTION,'setChatUserOnline');
$xajax->register(XAJAX_FUNCTION,'showChatUsers');
$xajax->register(XAJAX_FUNCTION,'logoutFromChat');
$xajax->register(XAJAX_FUNCTION,'appendToChatBox');
$xajax->register(XAJAX_FUNCTION,'checkChatLoggedIn');

function loadChat($minId)
{
	$minId = intval($minId);
	$ajax = new xajaxResponse();
	$s = $_SESSION;
	if (isset($s['user_id']))
	{
			$res = dbquery("
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
				id>".$minId."
			ORDER BY
				timestamp ASC
			");
			if (mysql_num_rows($res)>0)
			{
				$out = "";
				while ($arr=mysql_fetch_assoc($res))
				{
					$adminstr = "";
					if ($arr['admin']==1)
						$adminstr = "<img src=\"../images/star_y.gif\" />";

					if ($arr['user_id']==0)
					{
						$out.= "<span style=\"color:#aaa\">";
						$out.= "&lt;".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text']);
						$out.= "</span><br/>";
					}
					elseif ($arr['color']!="")
					{
						$out.= "<span style=\"color:".$arr['color']."\">";
						$out.= "$adminstr&lt;<a style=\"color:".$arr['color']."\" href=\"../index.php?page=userinfo&id=".$arr['user_id']."\" target=\"main\">".$arr['nick']."</a> | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text']);
						$out.= "</span><br/>";
					}
					else
						$out.= "$adminstr&lt;<a style=\"color:#fff\" href=\"../index.php?page=userinfo&id=".$arr['user_id']."\" target=\"main\">".$arr['nick']."</a> | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text'])."<br/>";

					$lastid = $arr['id'];
				}

				$ajax->append("chatitems","innerHTML",$out);
				$ajax->assign("lastid","innerHTML",$lastid);
				$ajax->script("window.scrollBy(0,100000);");
			}
			$ajax->script('xajax_checkChatLoggedIn()');
			$ajax->script('setTimeout("xajax_loadChat(document.getElementById(\'lastid\').innerHTML)",1000);');
	}
	else
	{
		$ajax->assign("chatitems","innerHTML","Sie sind ausgeloggt!");
		$ajax->script("parent.top.location=parent.main.location");
	}

  return $ajax;

}

function checkChatLoggedIn()
{
	$ajax = new xajaxResponse();
	$s = $_SESSION;
	$res = dbquery("
	SELECT
		kick
	FROM
		chat_users
	WHERE
		user_id=".$s['user_id']."
		AND kick!=''
	");
	if (mysql_num_rows($res)>0)
	{
		$arr = mysql_fetch_array($res);
		$ajax->alert("Du wurdest gekickt! Grund: ".$arr['kick']);
		dbquery("
		DELETE FROM
			chat_users
		WHERE
			user_id=".$s['user_id']."
		");
		$ajax->script("parent.top.location = '..'");

	}
  return $ajax;
}

function appendToChatBox($string)
{
	ob_start();
	$ajax = new xajaxResponse();
	$s = $_SESSION;
	if (isset($s['user_id']))
	{
		$out= "<span style=\"color:#aaa\">";
		$out.= "&lt;".date("H:i")."&gt; ".stripslashes($string);
		$out.= "</span><br/>";
		$ajax->append("chatitems","innerHTML",$out);
		$ajax->script("window.scrollBy(0,100000);");
	}
	$out = ob_get_clean();
	if ($out!="")
		$ajax->alert($out);
  return $ajax;
}

function kickChatUser($uid,$msg="Kicked by Chat-Admin")
{
	dbquery("
	UPDATE
		chat_users
	SET
		kick='".$msg."'
	WHERE
		user_id='".$uid."'");
	if (mysql_affected_rows()>0)
	{
		return true;
	}
	return false;
}

function sendChat($form)
{
	$ajax = new xajaxResponse();

	ob_start();

	$ajax->script('xajax_checkChatLoggedIn()');

	$s = $_SESSION;
	$ajax->assign("ctext","value","");
	if (isset($s['user_id']))
	{
		$admin = 0;
		$res = dbquery("
		SELECT
			user_chatadmin
		FROM
			users
		WHERE
			user_id=".$s['user_id']."
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			if ($arr[0] == 1)
				$admin = 1;
		}

		$ct = $form['ctext'];
		$m = array();
		if ($admin==1 && preg_match('#^/kick (.[^\'\"\?\<\>\$\!\=\;\&\s]+)$#',$ct,$m)>0)
		{
			$uid = User::findIdByNick($m[1]);
			if ($uid>0)
			{
				if (kickChatUser($uid))
				{
					chatSystemMessage($m[1]." wurde gekickt!");
				}
				else
				{
					$ajax->alert("User is not online in chat!");
				}
			}
			else
			{
				$ajax->alert("A user with this nick does not exist!");
			}
		}
		elseif ($admin==1 && preg_match('#^/kick (.[^\'\"\?\<\>\$\!\=\;\&\s]+)\s([A-Za-z0-9\s]+)$#',$ct,$m)>0)
		{
			$text = "";
			if (isset($m[2]))
				$text = $m[2];
			$uid = User::findIdByNick($m[1]);
			if ($uid>0)
			{
				if (kickChatUser($uid,$text))
				{
					chatSystemMessage($m[1]." wurde gekickt! Grund: ".$text.".");
				}
				else
				{
					$ajax->alert("User is not online in chat!");
				}
			}
			else
			{
				$ajax->alert("A user with this nick does not exist!");
			}
		}
		elseif ($admin==1 && preg_match('#^/ban (.[^\'\"\?\<\>\$\!\=\;\&\s]+)\s([A-Za-z0-9\s]+)$#',$ct,$m)>0)
		{
			$text = isset($m[2]) ? $m[2] : "";
			$uid = User::findIdByNick($m[1]);
			if ($uid>0)
			{
				dbquery("INSERT INTO
					chat_banns
				(user_id,reason,timestamp)
				VALUES (".$uid.",'".$text."',".time().")
				ON DUPLICATE KEY UPDATE timestamp=".time().",reason='".$text."'");
				kickChatUser($uid,$text);
				chatSystemMessage($m[1]." wurde gebannt! Grund: ".$text);
			}
			else
			{
				$ajax->alert("A user with this nick does not exist!");
			}
		}
		elseif ($admin==1 && preg_match('#^/unban (.[^\'\"\?\<\>\$\!\=\;\&\s]+)$#',$ct,$m)>0)
		{
			$uid = User::findIdByNick($m[1]);
			if ($uid>0)
			{
				dbquery("DELETE FROM
					chat_banns
				WHERE
					user_id=".$uid.";");
				if (mysql_affected_rows()>0)
				{
					$ajax->alert("Unbanned ".$m[1]."!");
				}
				else
				{
					$ajax->alert("A user with that nick is not banned!");
				}
			}
			else
			{
				$ajax->alert("A user with this nick does not exist!");
			}			
		}
		elseif ($admin==1 && preg_match('#^/banlist$#',$ct,$m)>0)
		{
			$res = dbquery("SELECT
				*
			FROM
				chat_banns
			;");
			if (mysql_num_rows($res)>0)
			{
				$out="";
				while ($arr=mysql_fetch_assoc($res))
				{
					$tu = new User($arr['user_id']);
					if ($tu->isValid)
					{
						$out.= $tu->nick.": ".$arr['reason']." (".df($arr['timestamp']).")\n";
					}
				}
				$ajax->alert($out);
			}
			else
			{
				$ajax->alert("Bannliste leer!");
			}
		}

		elseif ($ct!="" && $_SESSION['lastchatmsg']!=md5($form['ctext']))
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
				'".addslashes(htmlentities($ct))."',
				'".$form['ccolor']."',
				'".$s['user_id']."',
				'".$admin."'
			)");
			$_SESSION['lastchatmsg']=md5($form['ctext']);
			$ajax->script("xajax_setChatUserOnline()");
		}
	}
	$out = ob_get_clean();
	if ($out!="")
		$ajax->alert($out);

  return $ajax;
}

function setChatUserOnline($init=0)
{
	$ajax = new xajaxResponse();
	$s = $_SESSION;
	if (isset($s['user_id']))
	{
		if ($init == 1)
		{
			$res = dbquery("
			SELECT
				user_id
			FROM
				chat_users
			WHERE
				user_id=".$s['user_id']."
			");
			if (mysql_num_rows($res)==0)
			{
				chatSystemMessage($s['user_nick']." betritt den Chat.");
				$str = "Hallo ".$s['user_nick'].",  willkommen im EtoA-Chat. Bitte beachte das wir Spam nicht dulden und eine gepflegte Ausdrucksweise erwarten. Bei Verstössen gegen diese Regeln werden wir mit Banns und/oder Accountsperrungen vorgehen!";
				$ajax->script("xajax_appendToChatBox('".$str."');");
			}
		}
		dbquery("REPLACE INTO
			chat_users
		(
			timestamp,
			nick,
			user_id
		)
		VALUES
		(
			".time().",
			'".$s['user_nick']."',
			'".$s['user_id']."'
		)");
		$ajax->script("setTimeout('xajax_setChatUserOnline()',60000);");
	}
  return $ajax;
}

function showChatUsers()
{
	$ajax = new xajaxResponse();
	$res = dbquery("
	SELECT
		nick,
		user_id
	FROM
		chat_users
	");
	$out="";
	$nr = mysql_num_rows($res);
	if ($nr>0)
	{
		$t = time();
		while ($arr=mysql_fetch_assoc($res))
		{
			// (".tf($t-$arr['timestamp']).")
			$out.= "<a href=\"../index.php?page=userinfo&id=".$arr['user_id']."\" target=\"main\">".$arr['nick']."</a><br/>";
		}
	}
	else
		$out.="Keine User online!<br/>";
	$ajax->assign("userlist","innerHTML",$out);
	$ajax->append("userListButton","value"," ($nr)");

  return $ajax;
}

function logoutFromChat()
{
	$ajax = new xajaxResponse();
	$s = $_SESSION;
	if (isset($s['user_id']))
	{
		dbquery("
		DELETE FROM
			chat_users
		WHERE
			user_id=".$s['user_id']."
		");
		chatSystemMessage($s['user_nick']." verlässt den Chat.");
	}
  return $ajax;
}

?>