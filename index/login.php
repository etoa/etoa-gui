<?PHP

	// Make this configurable
	$allow = false;
	
?>

<h1>Game-Login</h1>
	<form action="." method="post">
		<div style="width:300px;margin:0px auto;">
		<?PHP
			if ($allow)
			{
			tableStart("Bitte Logindaten eingeben:");
		?>
			<tr>
				<th>Name:</th>
				<td><input id="loginname" type="text" name="login_nick" value="" size="20" maxlength="250" /></td></tr>
			<tr>
				<th>Passwort:</th>
				<td><input id="loginpw" type="password" name="login_pw" value="" size="20" maxlength="250" /></td></tr>
			</td></tr>
		</table>
		<br/><input id="loginsubmit" type="submit" name="login_submit" value="Login" class="button" />
		<?PHP
			}
			else
			{
				echo "Ein Login ist zurzeit nur über unsere offizielle <a href=\"".LOGINSERVER_URL."\">Startseite</a> möglich!";
			}
		?>
		</div>
	</form>     	