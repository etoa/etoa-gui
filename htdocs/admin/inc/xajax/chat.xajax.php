<?php
$xajax->register(XAJAX_FUNCTION,'loadChat');
$xajax->register(XAJAX_FUNCTION,'showChatUsers');
$xajax->register(XAJAX_FUNCTION,'showBannedChatUsers');


function loadChat($minId)
{
	$minId = intval($minId);
	$ajax = new xajaxResponse();

			$res = dbquery("
			SELECT
				id,
				nick,
				timestamp,
				text,
				color,
				user_id,
				admin
			FROM
				chat
			WHERE
				id>".$minId."
			ORDER BY
				timestamp ASC
			");
			$out='';
			if (mysql_num_rows($res)>0)
			{

				while ($arr=mysql_fetch_assoc($res))
				{
					if ($arr['admin']==1)
						$adminstr = "<img src=\"../images/star_y.gif\" />";
					else
						$adminstr = "";
					if ($arr['user_id']==0)
					{
						$out.= "<span style=\"color:#aaa\">";
						$out.= "&lt;".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text']);
						$out.= "</span><br/>";
					}
					elseif ($arr['color']!="")
					{
						$out.= "<span style=\"color:".$arr['color']."\">";
						$out.= "$adminstr&lt;<a style=\"color:".$arr['color']."\" href=\"?page=user&amp;sub=edit&amp;id=".$arr['user_id']."\">".$arr['nick']."</a> | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text']);
						$out.= "</span><br/>";
					}
					else {
						$out.= "$adminstr&lt;<a style=\"color:#fff\" href=\"?page=user&amp;sub=edit&amp;id=".$arr['user_id']."\">".$arr['nick']."</a> | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text'])."<br/>";
          }
					$lastid=$arr['id'];
				}
				$ajax->append("chatitems","innerHTML",$out);
				$ajax->assign("lastid","innerHTML",$lastid);
				$ajax->script('document.getElementById("chatitems").scrollTop = document.getElementById("chatitems").scrollHeight;');
			}
			$ajax->script("setTimeout(\"xajax_loadChat(document.getElementById('lastid').innerHTML)\",1000);");

  return $ajax;

}

function showChatUsers()
{
	$ajax = new xajaxResponse();
	$res = dbquery("
	SELECT
		nick,
		user_id,
		timestamp
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
			$out.= "<a href=\"?page=user&amp;sub=edit&amp;id=".$arr['user_id']."\">
			".$arr['nick']."</a> ".date("H:i:s",$arr['timestamp'])."
			<a href=\"?page=chat&amp;kick=".$arr['user_id']."\">Kick</a>
			<a href=\"?page=chat&amp;ban=".$arr['user_id']."\">Ban</a>
			<a href=\"?page=chat&amp;del=".$arr['user_id']."\">Del</a>
<br/>";
		}
	}
	else
		$out.="Keine User online!<br/>";
	$ajax->assign("chatuserlist","innerHTML",$out);

	$ajax->script("setTimeout(\"xajax_showChatUsers();\",1000);");
  return $ajax;
}

function showBannedChatUsers()
{
	ob_start();
	$ajax = new xajaxResponse();
	$res = dbquery("
	SELECT
		u.user_id,
		u.user_nick as nick,
		b.reason,
		b.timestamp
	FROM
		chat_banns b
	INNER JOIN
		users u ON u.user_id=b.user_id
	");
	$out="";
	$nr = mysql_num_rows($res);
	if ($nr>0)
	{
		$t = time();
		$out.= "<ul>";
		while ($arr=mysql_fetch_assoc($res))
		{
			$out.= "<li><a href=\"?page=user&amp;sub=edit&amp;id=".$arr['user_id']."\">".$arr['nick']."</a>
			 ".$arr['reason']." (".date("H:i:s",$arr['timestamp']).")
			<a href=\"?page=chat&amp;unban=".$arr['user_id']."\">Unbannen</a></li>";
		}
		$out.="</ul>";
	}
	else
		$out.="Keine User gebannt!<br/>";
	$ajax->assign("bannedchatuserlist","innerHTML",$out);

	$ajax->script("setTimeout(\"xajax_showBannedChatUsers();\",10000);");
	$out = ob_get_clean();
  return $ajax;
}


?>
