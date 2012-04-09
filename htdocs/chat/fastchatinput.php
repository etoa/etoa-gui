<?PHP
	/* fastchatinput von river */

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
		<script type="text/javascript" src="../web/js/fastchatinput.js"></script>
	</head>
	<body>
		<div id="chatinput">
			<form action="" method="post" autocomplete="off" id="cform" onsubmit="sendChat('ctext');return false;">
			<?PHP
				if ( isset($_SESSION['user_id']) )
				{
					$cu = new CurrentUser($_SESSION['user_id']);
					$_SESSION['ccolor'] = $cu->properties->chatColor;
					$res = dbquery("
					SELECT * FROM
						chat_banns
					WHERE
						user_id=".$cu->id.";");
					if (!isset($res) || mysql_num_rows($res)>0)
					{
						?>
							<p>Ein Fehler ist aufgetreten</p>
						<?PHP
					}
					else
					{
						echo 
						'Text: <input type="text" id="ctext" name="ctext" value="" size="40" maxlength="255" style="color:#'.$cu->properties->chatColor.'"/>
						<br/><br/>';
						?>
							<input type="button" onclick="sendChat('ctext');" value="Senden"/>&nbsp;
						<?PHP
					}
				}
				// Keine Fehlermeldung anzeigen (dies geschieht schon im grossen Frame)
			?>
			<input type="button" onclick="logoutFromChat();" value="Chat schliessen"/> <span id='msg'></span>
			</form>
		</div>
	</body>
</html>