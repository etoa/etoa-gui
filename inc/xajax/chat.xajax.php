<?PHP
$xajax->register(XAJAX_FUNCTION,'loadChat');
$xajax->register(XAJAX_FUNCTION,'sendChat');
$xajax->register(XAJAX_FUNCTION,'setChatUserOnline');
$xajax->register(XAJAX_FUNCTION,'showChatUsers');

function loadChat($minId)
{
	$minId = intval($minId);
	$ajax = new xajaxResponse();
	
	$s = $_SESSION[ROUNDID];
	if (isset($s['user_id']))
	{	
			$res = dbquery("
			SELECT
				id,
				nick,
				timestamp,
				text,
				color,
				user_id
			FROM
				chat
			WHERE
				id>".$minId."
			ORDER BY
				timestamp ASC
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_assoc($res))
				{
					if ($arr['user_id']==0)
					{
						$out.= "<span style=\"color:#aaa\">";
						$out.= "&lt;".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text']);					
						$out.= "</span><br/>";
					}
					elseif ($arr['color']!="")
					{
						$out.= "<span style=\"color:".$arr['color']."\">";
						$out.= "&lt;<a style=\"color:".$arr['color']."\" href=\"../index.php?page=userinfo&id=".$arr['user_id']."\" target=\"main\">".$arr['nick']."</a> | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text']);					
						$out.= "</span><br/>";
					}
					else
						$out.= "&lt;<a style=\"color:#fff\" href=\"../index.php?page=userinfo&id=".$arr['user_id']."\" target=\"main\">".$arr['nick']."</a> | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text'])."<br/>";					
					$lastid=$arr['id'];
				}
				$ajax->append("chatitems","innerHTML",$out);
				$ajax->assign("lastid","innerHTML",$lastid);
				$ajax->script("document.getElementById('chattext').scrollTop = document.getElementById('chattext').scrollHeight;");
			}
			$ajax->script("setTimeout(\"xajax_loadChat(document.getElementById('lastid').innerHTML)\",1000);");
	}
	else
	{
		$ajax->assign("chatitems","innerHTML","Sie sind ausgeloggt!");
		$ajax->script("parent.top.location=parent.main.location");
	}
	
  return $ajax;	
  
}

function sendChat($form)
{
	$ajax = new xajaxResponse();	
	$s = $_SESSION[ROUNDID];
	$ajax->assign("ctext","value","");
	if (isset($s['user_id']))
	{		
		if ($form['ctext']!="")
		{
			dbquery("INSERT INTO
				chat
			(
				timestamp,
				nick,
				text,
				color,
				user_id
			)
			VALUES
			(
				".time().",
				'".$s['user_nick']."',
				'".addslashes($form['ctext'])."',
				'".$form['ccolor']."',
				'".$s['user_id']."'
			)");
		}	
	}
  return $ajax;		
}

function setChatUserOnline($init=0)
{
	$ajax = new xajaxResponse();	
	$s = $_SESSION[ROUNDID];
	if (isset($s['user_id']))
	{		
		dbquery("INSERT INTO
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
		if ($init == 1)
		{
			//chatSystemMessage($s['user_nick']." betritt den Chat.");
		}
		$ajax->script("setTimeout('xajax_setChatUserOnline()',60000);");
	}
  return $ajax;		
}

function chatSystemMessage($msg)
{
	dbquery("INSERT INTO
		chat
	(
		timestamp,
		text
	)
	VALUES
	(
		".time().",
		'".addslashes($msg)."'
	)");	
}

function showChatUsers()
{
	$ajax = new xajaxResponse();	
	$res = dbquery("
	SELECT DISTINCT
		nick,
		user_id
	FROM
		chat_users
	");
	$out="<b>Chat-User</b><br/><br/>";
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_assoc($res))
		{
			$out.= "<a href=\"../index.php?page=userinfo&id=".$arr['user_id']."\" target=\"main\">".$arr['nick']."</a><br/>";					
		}
	}
	else
		$out.="Keine User online!<br/>";
	$out.="<br/>";
	$ajax->assign("userlist","innerHTML",$out);

  return $ajax;			
}

?>