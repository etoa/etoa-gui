<?PHP
$xajax->register(XAJAX_FUNCTION,'loadChat');
$xajax->register(XAJAX_FUNCTION,'sendChat');
$xajax->register(XAJAX_FUNCTION,'setChatUserOnline');
$xajax->register(XAJAX_FUNCTION,'showChatUsers');
$xajax->register(XAJAX_FUNCTION,'logoutFromChat');
$xajax->register(XAJAX_FUNCTION,'appendToChatBox');


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
				while ($arr=mysql_fetch_assoc($res))
				{
					if ($arr['admin']==1)
						$adminstr = "<img src=\"../images/star_y.gif\" />";
					else
						$adminstr = "";
					if ($arr['private']==0 || $s['user_id']==$arr['user_id'])
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
					$lastid=$arr['id'];
				}
				$ajax->append("chatitems","innerHTML",$out);
				$ajax->assign("lastid","innerHTML",$lastid);
				$ajax->script("window.scrollBy(0,100000);");
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

function appendToChatBox($string)
{
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
  return $ajax;		
}

function sendChat($form)
{
	$ajax = new xajaxResponse();	
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
		if ($form['ctext']!="" && $_SESSION['lastchatmsg']!=md5($form['ctext']))
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
				'".addslashes($form['ctext'])."',
				'".$form['ccolor']."',
				'".$s['user_id']."',
				'".$admin."'
			)");
			$_SESSION['lastchatmsg']=md5($form['ctext']);
			$ajax->script("xajax_setChatUserOnline()");
		}	
	}
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