<?PHP
	/* fastchatlogout von river */
	
	define('RELATIVE_ROOT','../');
	include_once(RELATIVE_ROOT.'inc/bootstrap.inc.php');
	
	if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_nick']))
	{
		die();
	}
	
	$res = dbquery('
	DELETE FROM
		chat_users
	WHERE
		user_id = '.$_SESSION['user_id'].';');
	
	chatSystemMessage($_SESSION['user_nick'].' verlässt den Chat.');
	$_SESSION['chatlogouttime'] = time();
?>