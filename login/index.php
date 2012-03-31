<?PHP
	/* Tool for local login by river
	 *
	 * Put into your /var/www directory and
	 * add localhost to accepted login hostnames:
	 * table "config", config_name "referers"
	 */

	// Zufallsgenerator initialisieren
	mt_srand(time());

	$t = time();
	$logintoken = sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$t).dechex($t);
	$nickField = sha1("nick".$logintoken.$t);
	$passwordField = sha1("password".$logintoken.$t);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>EtoA | Login</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="reset.css" type="text/css" />
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script type="text/javascript" src="jquery-1.7.2.min.js"></script>
		<script>
			$(function() {
				$('#nick').focus();
			});
		</script>
	</head>
	<body>
		<div id="layoutbox">
			<div id="header">
				<a href="."><img id="logo" src="../htdocs/web/images/admin/logo.png" alt="Logo" /></a>
				<div id="slogan">EtoA Login</div>
			</div>
			<div id="main">
				<form action="../htdocs" method="post" id="loginform">
					<label for="nick">Nick</label> <input type="text" id="nick" name="<?PHP echo $nickField; ?>" size="11" maxlength="100" /><br />
					<label for="password">Password</label> <input type="password" id="password" name="<?PHP echo $passwordField; ?>" size="11" maxlength="100" /><br />
					<p class="buttons">
						<input type="submit" value="Login" name="login" />&nbsp;
						<input type="button" value="Admin" onclick="document.location='../htdocs/admin';" />
					</p>
					<input type="hidden" name="token" value="<?PHP echo $logintoken; ?>" />
				</form>
			</div>
		</div>
	</body>
</html>
