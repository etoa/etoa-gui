<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
// $Id$
//////////////////////////////////////////////////////

if (isset($_GET['sendpass']))
{
	echo "<div style=\"width:500px;margin:10px auto;text-align:center;\">" .
	"<br/><a href=\"..\"><img src=\"../images/game_logo.jpg\" alt=\"Logo\" width=\"450\" height=\"150\" border=\"0\" /></a>";
	echo "<h1 style=\"text-align:center;\">Administration - ".Config::getInstance()->roundname->v."</h1>
	<h2 style=\"text-align:center;\">Passwort senden</h2>";
	if (isset($_POST['sendpass_submit']))
	{
		$res = dbQuerySave("
		SELECT 
			user_id,
			user_nick,
			user_email 
		FROM 
			admin_users 
		WHERE 
			user_nick=?;
		",array($_POST['user_nick']));
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);			
			$pw = mt_rand(1000000,9999999);
			$msg = "Hallo ".$arr[1].".\n\nDu hast für die Administration der ".Config::getInstance()->roundname->v." von EtoA ein neues Passwort angefordert.\n\n";
			$msg.= "Das neue Passwort lautet: $pw\n\n";
			$msg.= "Diese Anfrage wurde am ".date("d.m.Y")." um ".date("H:i")." Uhr vom Computer ".Net::getHost($_SERVER['REMOTE_ADDR'])." aus in Auftrag gegeben.\nBitte denke daran, das Passwort nach dem ersten Login zu ändern!";
			$mail = new Mail("Neues Administrationspasswort",$msg);
			$mail->send($arr[2]);
			echo "Das Passwort wurde geändert und dir per Mail zugestellt!<br/><br/>";
			echo "<input type=\"button\" value=\"Zum Login\" onclick=\"document.location='?'\" />";
			dbquery("
			UPDATE 
				admin_users
			SET
				user_password='".pw_salt($pw,$arr[0])."',
				user_force_pwchange=1					
			WHERE
				user_id=".$arr[0].";");
			add_log(8,"Der Administrator ".$arr[1]." (ID: ".$arr[0].") fordert per E-Mail (".$arr[2].") von ".$_SERVER['REMOTE_ADDR']." aus ein neues Passwort an.",time());					
		}
		else
		{
			echo "Dieser Benutzer existiert nicht!<br/><br/>";
			echo "<input type=\"button\" value=\"Nochmals versuchen\" onclick=\"document.location='?sendpass=1'\" />";
		}
	}
	else
	{
		echo "Hier kannst du ein neues Passwort beantragen.<br/>Es wird dir dann an die eingestellte E-Mail-Adresse gesendet!<br/>";
		echo "<form action=\"?sendpass=1\" method=\"post\">";			
		echo '<table class="tb" style="width:400px;margin:10px auto;">';
		echo '<tr><th>Loginname:</th><td><input type="text" name="user_nick" /></td></tr>';
		echo '</table><br/><input type="submit" name="sendpass_submit" value="Neues Passwort senden" /> &nbsp; ';
		echo "<input type=\"button\" value=\"Zum Login\" onclick=\"document.location='?'\" />";
		echo "</form></div>";
		echo '<script type="text/javascript">document.forms[0].elements[0].focus()</script>';
	}
}
else
{
	$res = dbquery("SELECT COUNT(user_id) FROM admin_users;");
	$arr = mysql_fetch_row($res);
	if ($arr[0]==0)
	{
		if (isset($_POST['newuser_submit']) && $_POST['user_nick']!="" && $_POST['user_password']!='')
		{
			dbquery("INSERT INTO admin_users (user_nick,user_password,user_admin_rank) VALUES ('".$_POST['user_nick']."','".md5($_POST['user_password'])."',8);");
			echo "Benutzer wurde erstellt!<br/><br/><input type=\"button\" onclick=\"document.location='?'\" value=\"Weiterfahren\"/>";				
		}
		else
		{
			echo "<div style=\"width:500px;margin:10px auto;text-align:center;\">" .
			"<br/><a href=\"..\"><img src=\"../images/game_logo.jpg\" alt=\"Logo\" width=\"450\" height=\"150\" border=\"0\" /></a>";
			echo "<h1 style=\"text-align:center;\">Administration - ".Config::getInstance()->roundname->v."</h1>";
			echo "<form action=\"?\" method=\"post\">";			
			echo "<h2 style=\"text-align:center;\">Admin-User erstellen</h2>";
			echo '<table class="tb" style="width:400px;margin:10px auto;">';
			echo '<tr><th>Loginname:</th><td><input type="text" name="user_nick" /></td></tr>';
			echo '<tr><th>Passwort:</th><td><input type="password" name="user_password" /></td></tr>';
			echo '</table><br/><input type="submit" name="newuser_submit" value="Admin-User erstellen" />';
			echo "</form></div>";
			echo '<script type="text/javascript">document.forms[0].elements[0].focus()</script>';
		}			
	}
	else
	{
		$str = $s->lastError;
		$clr = 3;

		$logo = 1;
		global $page;
		if ($logo==1)
		{
			echo "<div  style=\"text-align:center\"><br/><a href=\"..\"><img src=\"../images/game_logo.jpg\" alt=\"Logo\" width=\"450\" height=\"150\" border=\"0\" /></a>";
			echo "<h1 style=\"text-align:center\">Administration - ".Config::getInstance()->roundname->v."</h1><br/>";
		}
		if ($str!="" && $s->lastErrorCode!="nologin")
		{
			if ($clr==3)
				echo "<div style=\"color:#f90;\">";
			elseif ($clr==2)
				echo "<div style=\"color:#f00;\">";
			elseif ($clr==1)
				echo "<div style=\"color:#0f0;\">";
			else
				echo "<div>";
			echo "$str</div><br/>";
		}
		echo "Gib dein Benutzername und dein Passwort ein um dich anzumelden.<br/>
		Klicke auf das Logo um zur Startseite zurückzukehren.<br/><br/>";
		echo "<form action=\"?".$_SERVER['QUERY_STRING']."\" method=\"post\">";
		echo "<table style=\"margin:0px auto;\">";
		echo "<tr><th class=\"tbltitle\">Benutzername:</th><td class=\"tbldata\"><input type=\"text\" name=\"login_nick\" maxlength=\"255\" size=\"25\" /></td></tr>";
		echo "<tr><th class=\"tbltitle\">Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"login_pw\" maxlength=\"255\" size=\"25\" /></td></tr>";
		echo "</table><br/>
		<div style=\"margin:0px auto;\">
			<input type=\"submit\" name=\"login_submit\" value=\"Login\" /> &nbsp;
			<input type=\"button\" value=\"Passwort vergessen\" onclick=\"document.location='?sendpass=1'\" /> &nbsp;
			<input type=\"button\" value=\"Zum Spiel-Login\" onclick=\"document.location='".Config::getInstance()->loginurl->v."'\" />

		</div></form></div>";
		echo '<script type="text/javascript">document.forms[0].elements[0].focus()</script>';
	}
}
?>
