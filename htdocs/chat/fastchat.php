<?PHP
	/* fastchat von river */

	define('RELATIVE_ROOT','../');
/*	include_once(RELATIVE_ROOT.'classes/isingleton.class.php');
	include_once(RELATIVE_ROOT.'classes/dbmanager.class.php');
	include_once(RELATIVE_ROOT.'classes/dbexception.class.php');
	include_once(RELATIVE_ROOT.'inc/functions.inc.php');*/
	include_once(RELATIVE_ROOT.'inc/bootstrap.inc.php');
	/*
	include_once(RELATIVE_ROOT.'classes/user.class.php');
	include_once(RELATIVE_ROOT.'classes/session.class.php');
	include_once(RELATIVE_ROOT.'classes/usersession.class.php');*/
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>EtoA Chat</title>
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
	 	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="content-style-type" content="text/css" />
		<meta http-equiv="content-language" content="de" />
		<link rel="stylesheet" type="text/css" href="../css/chat.css" />
		<script src="js/fastchat.js" type="text/javascript"></script>
	</head>
	<body>
		<?PHP
			if ( !isset($_SESSION['user_id']) )
			{
				echo('<p>Du bist nicht eingeloggt.</p>');
			}
			else
			{
				$res = dbquery("
				SELECT * FROM
					chat_banns
				WHERE
					user_id=".$_SESSION['user_id'].";");
				if (!isset($res)) {
					echo "<p>Irgend etwas lief schief. Versuche den Chat neu zu laden.</p>";
				}
				elseif (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_assoc($res);
					echo "<p>Du wurdest vom Chat gebannt!<br/><br/>
					<b>Grund:</b> ".$arr['reason']."<br/>
					"/*<b>Zeit:</b> ".df($arr['timestamp'])."</p>"*/;
				}
				else
				{
					?>
					<div id="chatitems"></div>
					<div id="userlist" style="display:none;"></div>
					<div id="chatchannelcontrols">
						<input type="button" id="userListButton" onclick="showUserList()" value="User anzeigen"/><span id="loading" style="color: #aaa;"></span>
					</div>
					<?PHP
				}
			}
			dbclose();
		?>
	</body>
</html>
