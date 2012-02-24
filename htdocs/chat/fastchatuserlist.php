<?PHP
/* fastchatuserlist von river */
	define('RELATIVE_ROOT','../');

	include_once(RELATIVE_ROOT.'inc/bootstrap.inc.php');
	$s = $_SESSION;
	
	$res = dbquery('
	SELECT
		nick,
		user_id
	FROM
		chat_users
	WHERE
		user_id ="'.$s['user_id'].'"');

	if (mysql_num_rows($res) == 0)
	{
	    die();
	}

	$res = dbquery('
	SELECT
		nick,
		user_id
	FROM
		chat_users');

	$out='';
	$nr = mysql_num_rows($res);

	if ($nr > 0)
	{
		$out = strval($nr).':';
		while ($arr=mysql_fetch_assoc($res))
		{
			$out.= '<a href="../index.php?page=userinfo&id='.$arr['user_id'].'" target="main">'.$arr['nick'].'</a><br/>';
		}
	}

	// no text = no users online
	echo $out;
?>