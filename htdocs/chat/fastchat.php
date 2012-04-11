<?PHP
	/* fastchat von river */

	define('RELATIVE_ROOT','../');
	include_once(RELATIVE_ROOT.'inc/bootstrap.inc.php');
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
		<title>EtoA Chat</title>
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
	 	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="content-style-type" content="text/css" />
		<meta http-equiv="content-language" content="de" />
		<link rel="stylesheet" type="text/css" href="../web/css/chat.css" />
		<script type="text/javascript" src="../web/js/jquery.min.js"></script>
		<script type="text/javascript" src="../web/js/fastchat-jq.js" ></script>
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
						<input type="button" id="userListButton" value="User anzeigen"/><span id="loading" style="color: #aaa;"></span>
					</div>
					<?PHP
				}
			}
			dbclose();
		?>
	</body>
</html>
