<h1>InGame-Chat</h1>
<h2>Userliste</h2>
<?PHP
	$res = dbquery("
	SELECT DISTINCT
		nick,
		user_id
	FROM
		chat_users
	");
	$out="";
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_assoc($res))
		{
			$out.= "<a href=\"?page=user&sub=edit&user_id=".$arr['user_id']."\" target=\"main\">".$arr['nick']."</a><br/>";					
		}
	}
	else
		$out.="Keine User online!<br/>";
	echo $out;
?>
<h2>Protokoll</h2>
<?PHP
	$out="";
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
	ORDER BY
		timestamp ASC
	");
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_assoc($res))
		{
			if ($arr['color']!="")
			{
				$out.= "<span style=\"color:".$arr['color']."\">";
				$out.= "&lt;<a style=\"color:".$arr['color']."\" href=\"?page=user&sub=edit&user_id=".$arr['user_id']."\">".$arr['nick']."</a> | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text']);					
				$out.= "</span><br/>";
			}
			else
				$out.= "&lt;<a style=\"color:#fff\" href=\"?page=user&sub=edit&user_id=".$arr['user_id']."\">".$arr['nick']."</a> | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text'])."<br/>";					
			$lastid=$arr['id'];
		}
		echo $out;	
	}

?>