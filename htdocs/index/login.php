<?PHP
	$loginUrl = Config::getInstance()->loginurl->v;
	if (empty($loginUrl))
	{
		?>
		<h1>Game-Login</h1>
		<?PHP
		if (isset($_GET['err'])) {
		    iBoxStart("Fehler beim Login!");
			switch ($_GET['err']) {
				case "name":
					echo "Du hast vergessen einen Namen oder ein Passwort einzugeben!";
					break;
				case "pass":
					echo "Falsches Passwort oder falscher Benutzername!<br/><br/><a href=\"pwrequest\">Passwort vergessen?</a>";
					break;
				case "ip":
					echo "IP-Adresse-&Uuml;berpr&uuml;fungsfehler! Kein Login von diesem Computer m&ouml;glich, da schon eine andere IP mit diesem Account verbunden ist!";
					break;
				case "timeout":
					echo "Das Timeout wurde erreicht und du wurdest automatisch ausgeloggt!";
					break;
				case "session":
					echo "Session-Cookie-Fehler. &Uuml;berpr&uuml;fe ob dein Browser wirklich Sitzungscookies akzeptiert!";
					break;
				case "tomanywindows":
					echo "Es wurden zu viele Fenster ge&ouml;ffnet oder aktualisiert, dies ist leider nicht erlaubt!";
					break;
				case "session2":
					echo "Deine Session ist nicht mehr vorhanden! Sie wurde entweder gel&ouml;scht oder sie ist fehlerhaft. Dies kann passieren wenn du dich an einem anderen PC einloggst obwohl du noch mit diesem online warst!";
					break;
				case "nosession":
					echo "Deine Session ist nicht mehr vorhanden! Sie wurde entweder gel&ouml;scht oder sie ist fehlerhaft. Dies kann passieren wenn du dich an einem anderen PC einloggst obwohl du noch mit diesem online warst!";
					break;
				case "verification":
					echo "Falscher Grafikcode! Bitte gib den linksstehenden Code in der Grafik korrekt in das Feld darunter ein!
					Diese Massnahme ist leider n&ouml;tig um das Benutzen von automatisierten Programmen (Bots) zu erschweren.";
					break;
				case "logintimeout":
					echo "Der Login-Schlüssel ist abgelaufen! Bitte logge dich neu ein!";
					break;
				case "sameloginkey":
					echo "Der Login-Schlüssel wurde bereits verwendet! Bitte logge dich neu ein!";
					break;
				case "wrongloginkey":
					echo "Falscher Login-Schlüssel! Ein Login ist nur von der offiziellen EtoA-Startseite aus möglich!";
					break;
				case "nologinkey":
					echo "Kein Login-Schlüssel! Ein Login ist nur von der offiziellen EtoA-Startseite aus möglich!";
					break;
				case "general":
					echo "Ein allgemeiner Fehler ist aufgetreten. Bitte den Entwickler kontaktieren!";
					break;
				default:
					echo "Unbekannter Fehler (<b>".$err."</b>). Bitte den Entwickler kontaktieren!";
			}
			iBoxEnd();
		}

		$t = time();
		$logintoken = sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$t).dechex($t);
		$nickField = sha1("nick".$logintoken.$t);
		$passwordField = sha1("password".$logintoken.$t)

		?>
		<form action="." method="post">
			<div style="width:300px;margin:0px auto;">
				<?PHP
					tableStart("Bitte Logindaten eingeben:");
				?>
				<tr>
					<th>Name:</th>
					<td>
						<input id="loginname" type="text" name="<?PHP echo $nickField; ?>" value="" size="20" maxlength="250" />
					</td>
				</tr>
				<tr>
					<th>Passwort:</th>
					<td><input id="loginpw" type="password" name="<?PHP echo $passwordField; ?>" value="" size="20" maxlength="250" /></td>
				</tr>
				<?PHP
					tableEnd();
				?>
				<br/><input id="loginsubmit" type="submit" name="login" value="Login" class="button" /> &nbsp;
				<input type="button" onclick="document.location='admin'" value="Zum Admin-Login" class="button" />
			</div>
			<input type="hidden" name="token" value="<?PHP echo $logintoken; ?>" />
		</form>
		<script type="text/javascript">
		$(function(){
			$('#loginname').focus();
		});
		</script>
		<?PHP
	}
	else
	{
		forward($loginUrl);
	}
?>