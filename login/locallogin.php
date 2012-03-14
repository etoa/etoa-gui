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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>EtoA | Das Sci-Fi Browsergame | Local testlogin </title>
	</head>

	<body>
		<div class="login">
			<form action="http://testrunde.localhost/" method="post" id="form"
				onsubmit="javascript:document.getElementById('form').action=document.getElementById('act').value;">
				Nick: <input type="text" name="<?PHP echo $nickField; ?>" size="11" maxlength="100" /><br />
				Password: <input type="password" name="<?PHP echo $passwordField; ?>" size="11" maxlength="100" /><br />
				Round url: <input type="text" name="roundurl" size="11" maxlength="100" id="act" /><br />
				<input class="btn-login_" type="submit" value="Login" name="login" />
				<input type="hidden" name="token" value="<?PHP echo $logintoken; ?>" />
			</form>
		</div>
	</body>
</html>
