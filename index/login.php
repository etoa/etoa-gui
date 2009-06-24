<?PHP

	// Make this configurable
	$allow = false;

	if ($allow)
	{
?>

<h1>Game-Login</h1>
	<form action="." method="post">
		<div style="width:300px;margin:0px auto;">
		<?PHP
			tableStart("Bitte Logindaten eingeben:");
		?>
			<tr>
				<th>Name:</th>
				<td>
					<input id="loginname" type="text" name="login_nick" value="" size="20" maxlength="250" />
				</td>
			</tr>
			<tr>
				<th>Passwort:</th>
				<td><input id="loginpw" type="password" name="login_pw" value="" size="20" maxlength="250" /></td>
			</tr>
		<?PHP
			tableEnd();
		?>
		<br/><input id="loginsubmit" type="submit" name="login_submit" value="Login" class="button" />
		</div>
	</form> 
	<?PHP
	}
	else
	{
		forward(Config::getInstance()->loginurl->v);
	}
?>