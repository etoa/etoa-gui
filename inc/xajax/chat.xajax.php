<?PHP
$xajax->register(XAJAX_FUNCTION,'loadChat');
$xajax->register(XAJAX_FUNCTION,'sendChat');

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
				color
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
					if ($arr['color']!="")
					{
						$out.= "<span style=\"color:".$arr['color']."\">";
						$out.= "&lt;".$arr['nick']." | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text']);					
						$out.= "</span><br/>";
					}
					else
						$out.= "&lt;".$arr['nick']." | ".date("H:i",$arr['timestamp'])."&gt; ".stripslashes($arr['text'])."<br/>";					
					$lastid=$arr['id'];
				}
				$ajax->append("chatitems","innerHTML",$out);
				$ajax->assign("lastid","innerHTML",$lastid);
			}

		$ajax->script("setTimeout(\"xajax_loadChat(document.getElementById('lastid').innerHTML)\",1000);");
		$ajax->script("setTimeout(\"window.location.hash = '#bancor';\",50);");
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
				color
			)
			VALUES
			(
				".time().",
				'".$s['user_nick']."',
				'".addslashes($form['ctext'])."',
				'".$form['ccolor']."'
			)");
		}	
	}
  return $ajax;		
}

?>