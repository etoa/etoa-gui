<?

/* Chat Functions */

function chatReload($login_time)
{
	/* Content-Filter (sorry for naming such evil words ;) ) */
	$chat_filter=array(
	'arsch',
	'idiot',
	'wichser',
	'fuck',
	'mongo',
	'schwanz',
	'pimmel',
	'fotze',
	'fick',
	'blödmann',
	'asshole',
	'wixer',
	'wixxer',
	'shit'
	);	
	
	global $db_table;
  $objResponse = new xajaxResponse();

	dbquery("
	DELETE FROM
		chat
	WHERE 
		chat_timestamp<".(time()-3600)."
	;");

  $res=dbquery("
  SELECT
  	chat_user_nick,
  	chat_text,
  	chat_color,
  	chat_timestamp
  FROM
  	chat
  WHERE
  	chat_timestamp>'".$login_time."'
  ORDER BY chat_timestamp DESC");
  $out='';
  if (mysql_num_rows($res)>0)
  {
  	while ($arr=mysql_fetch_row($res))
  	{
  		$txt = stripslashes($arr[1]);
  		foreach ($chat_filter as $cf)
  		{
				$txt = str_replace($cf,'***',$txt);
  		}
  		$str = '<[b]'.$arr[0].'[/b] | '.date("H:i:s",$arr[3]).'> '.$txt."\n";
  		$str = text2html($str);
  		if ($arr[2]!="")
  		{
  			$out.= '<span style="color:'.$arr[2].'">'.$str.'</span>';
  		}
  		else
  		{
  			$out.=$str;
  		}
  	}  	
  }
  else
  {
  	$out="<i>Keine Nachrichten!</i>";
  }
    		
  $objResponse->addAssign("chatBox","innerHTML",$out);
	return $objResponse->getXML();
}

function chatSend($text)
{
	global $db_table;
  $objResponse = new xajaxResponse();
  if ($text!='')
  {
	  $res=dbquery("
	  INSERT INTO
	  	chat
	  (
	  	chat_user_id,
	  	chat_user_nick,
	  	chat_timestamp,
	  	chat_color,
	  	chat_text	  
	  )
	  VALUES
	  (
			'".$_SESSION[ROUNDID]["user"]["id"]."',
			'".$_SESSION[ROUNDID]["user"]["nick"]."',
			UNIX_TIMESTAMP(),
			'".$_SESSION[ROUNDID]["user"]["chat_color"]."',
			'".addslashes($text)."'
		);");	
		
	  $res=dbquery("
	  INSERT INTO
	  	chat_log
	  (
	  	chat_user_id,
	  	chat_user_nick,
	  	chat_timestamp,
	  	chat_text,
	  	chat_ip  
	  )
	  VALUES
	  (
			'".$_SESSION[ROUNDID]["user"]["id"]."',
			'".$_SESSION[ROUNDID]["user"]["nick"]."',
			UNIX_TIMESTAMP(),
			'".addslashes($text)."',
			'".$_SERVER['REMOTE_ADDR']."'
		);");		
		
	 	$objResponse->addAssign("chatMsg", "value", '');
	}
	$objResponse->addScript("document.getElementById('chatMsg').focus();");
	return $objResponse->getXML();
}

function chatChangeColor($col)
{
	global $db_table;
  $objResponse = new xajaxResponse();

	if ($col!="")
	{
		$_SESSION[ROUNDID]["user"]["chat_color"]=$col;
 		$objResponse->addAssign("chatMsg", "style.color", $col);
	}
	else
	{
		$_SESSION[ROUNDID]["user"]["chat_color"]='#fff';
 		$objResponse->addAssign("chatMsg", "style.color", '#fff');
	}
	$objResponse->addScript("document.getElementById('chatMsg').focus();");
	return $objResponse->getXML();
}

function chatLoadUsers()
{
	global $db,$db_table,$conf;
  $objResponse = new xajaxResponse();
  $out='';
  $cnt=0;
  $users=array();
  
  $res=dbquery("
  SELECT
  	user_id,
		user_nick
	FROM
		".$db_table['buddylist']." AS bl
		INNER JOIN ".$db_table['users']." AS u
		ON bl.bl_buddy_id = u.user_id
		AND bl_user_id='".$_SESSION[ROUNDID]["user"]["id"]."'
		AND bl_allow=1
		AND user_acttime>".(time()-$conf['online_threshold']['v']*60)."
	;");
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_row($res))
		{
			$users[$arr[0]]=$arr[1];
		}		
	}
	
	
	if ($_SESSION[ROUNDID]["user"]["alliance_id"]>0)
	{
	  $res=dbquery("
	  SELECT
	  	user_id,
			user_nick
		FROM
			".$db_table['users']." AS bl
		WHERE
			user_alliance_id=".$_SESSION[ROUNDID]["user"]["alliance_id"]."
			AND user_id!=".$_SESSION[ROUNDID]["user"]["id"]."
			AND user_acttime>".(time()-$conf['online_threshold']['v']*60)."
		;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_row($res))
			{
				$users[$arr[0]]=$arr[1];
			}		
		}		
	}

	foreach ($users as $k=>$v)
	{
		$out.=$v."<br/>";
		$cnt++;
	}
				
	$gres=dbquery('SELECT COUNT(user_id) FROM '.$db_table['users'].' WHERE user_acttime>'.(time()-$conf['user_timeout']['v']).';');
	$garr=mysql_fetch_row($gres);
	if($cnt==0)
	{
		$out.=$garr[0]." User online";
	}
	else
	{	
		$c = $garr[0]-$cnt;
		if ($c>0)
		{
			$out.=($garr[0]-$cnt)." weitere User online";
		}
	}
  
	$objResponse->addAssign("chatUsers", "innerHTML", $out);
	return $objResponse->getXML();
}
	
	$objAjax->registerFunction('chatReload');
	$objAjax->registerFunction('chatSend');
	$objAjax->registerFunction('chatChangeColor');
	$objAjax->registerFunction('chatLoadUsers');


?>