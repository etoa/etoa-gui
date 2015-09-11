<?PHP

	function getErrMsg($err) {
		switch ($err) {
			case "name":
				return "Du hast vergessen einen Namen oder ein Passwort einzugeben!";
				break;
			case "pass":
				return "Falsches Passwort oder falscher Benutzername!";
				break;
			case "ip":
				return "IP-Adresse-&Uuml;berpr&uuml;fungsfehler! Kein Login von diesem Computer m&ouml;glich, da schon eine andere IP mit diesem Account verbunden ist!";
				break;
			case "timeout":
				return "Das Timeout wurde erreicht und du wurdest automatisch ausgeloggt!";
				break;
			case "session":
				return "Session-Cookie-Fehler. &Uuml;berpr&uuml;fe ob dein Browser wirklich Sitzungscookies akzeptiert!";
				break;
			case "tomanywindows":
				return "Es wurden zu viele Fenster ge&ouml;ffnet oder aktualisiert, dies ist leider nicht erlaubt!";
				break;
			case "session2":
				return "Deine Session ist nicht mehr vorhanden! Sie wurde entweder gel&ouml;scht oder sie ist fehlerhaft. Dies kann passieren wenn du dich an einem anderen PC einloggst obwohl du noch mit diesem online warst!";
				break;
			case "nosession":
				return "Deine Session ist nicht mehr vorhanden! Sie wurde entweder gel&ouml;scht oder sie ist fehlerhaft. Dies kann passieren wenn du dich an einem anderen PC einloggst obwohl du noch mit diesem online warst!";
				break;
			case "verification":
				return "Falscher Grafikcode! Bitte gib den linksstehenden Code in der Grafik korrekt in das Feld darunter ein!
				Diese Massnahme ist leider n&ouml;tig um das Benutzen von automatisierten Programmen (Bots) zu erschweren.";
				break;
			case "logintimeout":
				return "Der Login-Schlüssel ist abgelaufen! Bitte logge dich neu ein!";
				break;
			case "sameloginkey":
				return "Der Login-Schlüssel wurde bereits verwendet! Bitte logge dich neu ein!";
				break;
			case "wrongloginkey":
				return "Falscher Login-Schlüssel! Ein Login ist nur von der offiziellen EtoA-Startseite aus möglich!";
				break;
			case "nologinkey":
				return "Kein Login-Schlüssel! Ein Login ist nur von der offiziellen EtoA-Startseite aus möglich!";
				break;
			case "general":
				return "Ein allgemeiner Fehler ist aufgetreten. Bitte den Entwickler kontaktieren!";
				break;
			default:
				return "Unbekannter Fehler (<b>".$err."</b>). Bitte den Entwickler kontaktieren!";
		}
	}

	$loginUrl = Config::getInstance()->loginurl->v;
	if (empty($loginUrl))
	{
		$t = time();
		$logintoken = sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$t).dechex($t);
		$nickField = sha1("nick".$logintoken.$t);
		$passwordField = sha1("password".$logintoken.$t);
		?>
		
		<h1>Game-Login</h1>
		
		<?PHP
		if (isset($_GET['err'])) {
			$msg = getErrMsg($_GET['err']);
			error_msg($msg);
		}
		?>
		
		<form action="." method="post" class="styled-form styled-form-medium">
			<p>Wilkommen in der <?=Config::getInstance()->roundname->v?>. Bitte melde dich mit deinen Logindaten an:</p>
			<p>
				<label for="loginname">Name</label>
				<input id="loginname" type="text" name="<?=$nickField?>" value="" size="20" maxlength="250" tabindex="1" /> &nbsp; <a href="?index=register">Kein Account? Hier registrieren</a>
			</p>
			<p>
				<label for="loginpw">Passwort</label>
				<input id="loginpw" type="password" name="<?=$passwordField?>" value="" size="20" maxlength="250" tabindex="2" /> &nbsp; <a href="?index=pwforgot">Passwort vergessen?</a>
			</p>
			<p class="form-buttons">
				<input id="loginsubmit" type="submit" name="login" value="Login" class="button" /> &nbsp;
				<a href="admin">Zum Admin-Login</a>
			</p>
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