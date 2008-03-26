<?PHP

define('ADMIN_ROOT_GROUP',8);


session_start();

if (!file_exists("conf.inc.php") && !file_exists("../conf.inc.php"))
{
	echo "<h1>EtoA Installation</h1>";


	if (isset($_POST['install_check']))
	{
		$_SESSION['INSTALL']['db_server'] = $_POST['db_server'];
		$_SESSION['INSTALL']['db_name'] = $_POST['db_name'];
		$_SESSION['INSTALL']['db_user'] = $_POST['db_user'];
		$_SESSION['INSTALL']['db_password'] = $_POST['db_password'];

		
		echo "Prüfe Eingaben....<br/>";
		if ($_POST['db_server'] != "" && $_POST['db_name'] != "" && $_POST['db_user'] != "" && $_POST['db_password'] != "")
		{
			define('DB_SERVER',$_POST['db_server']);
			define('DB_USER',$_POST['db_user']);
			define('DB_PASSWORD',$_POST['db_password']);
			define('DB_DATABASE',$_POST['db_name']);	
			if (dbconnect(0))
			{
				echo "<div style=\"color:#0f0\">Datenbankverbindung erfolgreich!</div><br/>";
				
				$_SESSION['INSTALL']['step']=2;
				$step = 2;
			}
			else
			{
				echo "<div style=\"color:#f00;\">Verbindung fehlgeschlagen! Fehler: ".mysql_error()."</div>";
			}
		}
		else
		{
			echo "<div style=\"color:#f00;\">Achtung! Du hast nicht alle Felder ausgef&uuml;lt!</div>";
		}
		echo "<br/>";
	}
	elseif($_POST['step2_submit'])
	{
		$step = 2;
		if ($_POST['round_name'] != "" && $_POST['loginserver_url'] != "" && $_POST['password_salt'] != "")
		{
			$step = 3;
			$_SESSION['INSTALL']['step'] = 3;
			$_SESSION['INSTALL']['round_name'] = $_POST['round_name'];
			$_SESSION['INSTALL']['loginserver_url'] = $_POST['loginserver_url'];
			$_SESSION['INSTALL']['password_salt'] = $_POST['password_salt'];
			$_SESSION['INSTALL']['etoa_debug'] = $_POST['etoa_debug']==1 ? 1 : 0;
			
		}
		else
		{
			echo "<div style=\"color:#f00;\">Achtung! Du hast nicht alle Felder ausgef&uuml;lt!</div>";
		}		
	}	
	
	elseif($_POST['step3_submit'])
	{
		$step = 3;
		if ($_POST['referers'] != "")
		{
			$step = 4;
			$_SESSION['INSTALL']['step'] = 4;
			$_SESSION['INSTALL']['referers'] = $_POST['referers'];
			$_SESSION['INSTALL']['admin_user'] = $_POST['admin_user'];
			$_SESSION['INSTALL']['admin_user_pw'] = $_POST['admin_user_pw'];
			
		}
		else
		{
			echo "<div style=\"color:#f00;\">Achtung! Du hast nicht alle Felder ausgef&uuml;lt!</div>";
		}		
	}		
	
	if (isset($_SESSION['INSTALL']['step']) && $_GET['step']>0)
	{
		$step = $_GET['step'];
	}
	else
	{
		$step = isset($_SESSION['INSTALL']['step']) ? $_SESSION['INSTALL']['step'] : 1;
	}
	
	if($step==4)
	{
		echo "<div style=\"font-weight:bold;color:#666;\">
		<a href=\"?step=1\" style=\"color:000\">Schritt 1</a> |
		<a href=\"?step=2\" style=\"color:000\">Schritt 2</a> |
		<a href=\"?step=3\" style=\"color:000\">Schritt 3</a> |
		<a href=\"?step=4\" style=\"color:000\">Schritt 4</a> |
		</div><br/>";
		
		define('DB_SERVER',$_SESSION['INSTALL']['db_server']);
		define('DB_USER',$_SESSION['INSTALL']['db_user']);
		define('DB_PASSWORD',$_SESSION['INSTALL']['db_password']);
		define('DB_DATABASE',$_SESSION['INSTALL']['db_name']);	
		define('PASSWORD_SALT',$_SESSION['INSTALL']['password_salt']);

		dbconnect();
		
		$cfg = Config::getInstance();
		$cfg->set("referers",$_SESSION['INSTALL']['referers']);
		echo "<div style=\"color:#0f0\">Refererliste gespeichert!</div><br/>";
		
		if ($_SESSION['INSTALL']['admin_user']!="" && $_SESSION['INSTALL']['admin_user_pw']!="")
		{
			$res = dbquery("SELECT COUNT(*) FROM admin_users WHERE user_nick='".$_SESSION['INSTALL']['admin_user']."';");
			$arr = mysql_fetch_row($res);
			if ($arr[0]==0)
			{
				dbquery("
				INSERT INTO
					admin_users
				(
					user_nick,
					user_name,
					user_email,
					user_admin_rank
				)
				VALUES
				(
					'".$_SESSION['INSTALL']['admin_user']."',
					'".$_SESSION['INSTALL']['admin_user']."',
					'".$_SESSION['INSTALL']['admin_user']."',
					".ADMIN_ROOT_GROUP."
				)
				");
				$id = mysql_insert_id();
				dbquery("UPDATE admin_users SET user_password='".pw_salt($_SESSION['INSTALL']['admin_user_pw'],$id)."' WHERE user_id=".$id.";");			
				echo "<div style=\"color:#0f0\">Admin-User ".$_SESSION['INSTALL']['admin_user']." mit Passwort ".$_SESSION['INSTALL']['admin_user_pw']." gespeichert!</div><br/>";
			}
		}
		
		
		$out="&lt;?PHP
	// EtoA main config file
	// Generated: ".date("d.m.Y H:i")."

	define('DB_SERVER','".$_SESSION['INSTALL']['db_server']."');
	define('DB_USER','".$_SESSION['INSTALL']['db_user']."');
	define('DB_PASSWORD','".$_SESSION['INSTALL']['db_password']."');
	define('DB_DATABASE','".$_SESSION['INSTALL']['db_name']."');	
	
	define('PASSWORD_SALT','".$_SESSION['INSTALL']['password_salt']."');
	define('LOGINSERVER_URL','".$_SESSION['INSTALL']['loginserver_url']."');
	define('ROUNDID','".$_SESSION['INSTALL']['round_name']."');
	define('ETOA_DEBUG',".$_SESSION['INSTALL']['etoa_debug'].");
?&gt;";

		echo "Fertig! Du kannst nun den folgenden Inhalt in eine neue Datei namens conf.inc.php speichern und 
		diese im EtoA-Hauptverzeichnis platzieren!<br/><br/><fieldset style=\"width:900px;background:#eee;font-family:courier new;\">
			<legend>conf.inc.php</legend>
			".nl2br($out)."
		</fieldset>";		
		echo "<br/>&gt;&gt; <a href=\"admin\">Zum Admin-Login</a><br/><br/>
		&gt;&gt; <a href=\"".$_SESSION['INSTALL']['loginserver_url']."\">Zum Loginserver</a><br/><br/>";
	
	}		
	
	elseif($step==3)
	{
		echo "<div style=\"font-weight:bold;color:#666;\">
		<a href=\"?step=1\" style=\"color:000\">Schritt 1</a> |
		<a href=\"?step=2\" style=\"color:000\">Schritt 2</a> |
		<a href=\"?step=3\" style=\"color:000\">Schritt 3</a> |
		Schritt 4
		</div><br/>";
		
		define('DB_SERVER',$_SESSION['INSTALL']['db_server']);
		define('DB_USER',$_SESSION['INSTALL']['db_user']);
		define('DB_PASSWORD',$_SESSION['INSTALL']['db_password']);
		define('DB_DATABASE',$_SESSION['INSTALL']['db_name']);	
		dbconnect();
		
		$cfg = Config::getInstance();
		
		echo "<form action=\"?\" method=\"post\">
		<fieldset style=\"width:900px;\">
			<legend>Weitere Einstellungen</legend>
			<table>
				<tr>
					<th>Referers:</th>
					<td><textarea name=\"referers\" rows=\"6\" cols=\"50\">".($_SESSION['INSTALL']['referers']!="" ? $_SESSION['INSTALL']['referers'] : $cfg->get('referers'))."</textarea></td>
					<td>(alle Seiten, welche als Absender gelten sollen. Also der Loginserver, sowie der aktuelle Server. Mache für jeden Eintrag eine neue Linie!)</td>
				</tr>";
				$res = dbquery("SELECT COUNT(*) FROM admin_users;");
				$arr = mysql_fetch_row($res);
				if ($arr[0]==0)
				{
					echo "<tr>
					<th>Admin-User:</th>
						<td><input type=\"text\" name=\"admin_user\" value=\"".$_SESSION['INSTALL']['admin_user']."\" /></td>
						<td>(neuer Admin-User-Name)</td>
					</tr>";
					echo "<tr>
					<th>Admin-User Passwort:</th>
						<td><input type=\"password\" name=\"admin_user_pw\" value=\"".$_SESSION['INSTALL']['admin_user_pw']."\" /></td>
						<td>(Admin-User Passwort)</td>
					</tr>";
				}
			echo "</table>
		</fieldset>		
		<br/><input type=\"submit\" name=\"step3_submit\" value=\"Weiter\" />						
		</form>";		
	}		
	
	elseif($step==2)
	{
		echo "<div style=\"font-weight:bold;color:#666;\">
		<a href=\"?step=1\" style=\"color:000\">Schritt 1</a> |
		<a href=\"?step=2\" style=\"color:000\">Schritt 2</a> |
		Schritt 3 |
		Schritt 4
		</div><br/>";
		echo "<form action=\"?\" method=\"post\">
		<fieldset style=\"width:700px;\">
			<legend>Allgemeine Daten</legend>
			<table>
				<tr>
					<th>Name der Runde:</th>
					<td><input type=\"text\" name=\"round_name\" value=\"".$_SESSION['INSTALL']['round_name']."\" /></td>
					<td>(z.b. Runde 1)</td>
				</tr>
				<tr>
					<th>Loginserver-URL:</th>
					<td><input type=\"text\" name=\"loginserver_url\" value=\"".$_SESSION['INSTALL']['loginserver_url']."\" /></td>
					<td>(z.b. http://www.etoa.ch)</td>
				</tr>
				<tr>
					<th>Passwort-Salt:</th>
					<td><input type=\"text\" name=\"password_salt\" value=\"".$_SESSION['INSTALL']['password_salt']."\" /></td>
					<td>(mit diesem Schlüssel werden alle Passwörter zusätzlich verschlüsselt; darf während einer laufenden Runde nicht ge&auml;ndert werde da sonst die Passw&ouml;rter nicht mehr gehen)</td>
				</tr>
				<tr>
					<th>Debug-Modus:</th>
					<td><input type=\"checkbox\" name=\"etoa_debug\" value=\"1\" ";
					if ($_SESSION['INSTALL']['etoa_debug']==1) echo " checked=\"checked\"";
					echo "/></td>
					<td>(zeigt PHP-Warnungen an)</td>
				</tr>
			</table>
		</fieldset>		
		<br/><input type=\"submit\" name=\"step2_submit\" value=\"Weiter\" />						
		</form>";		
		
		
	}	
	else
	{
		echo "Anscheinend existiert noch keine Konfigurationsdatei für diese EtoA-Instanz. Bitte erstelle
	eine indem du folgendes Formular ausfüllst:<br/><br/>";
		
		echo "<div style=\"font-weight:bold;color:#666;\">
		<a href=\"?step=1\" style=\"color:000\">Schritt 1</a> |
		Schritt 2 |
		Schritt 3 |
		Schritt 4
		</div><br/>";
		echo "<form action=\"?\" method=\"post\">
		<fieldset style=\"width:400px;\">
			<legend>MySQL-Datenbank</legend>
			<table>
				<tr>
					<th>Server:</th>
					<td><input type=\"text\" name=\"db_server\" value=\"".$_SESSION['INSTALL']['db_server']."\" /></td>
					<td>(z.b. localhost)</td>
				</tr>
				<tr>
					<th>Datenbank:</th>
					<td><input type=\"text\" name=\"db_name\" value=\"".$_SESSION['INSTALL']['db_name']."\" /></td>
					<td>(z.b. etoaroundx)</td>
				</tr>
				<tr>
					<th>User:</th>
					<td><input type=\"text\" name=\"db_user\" value=\"".$_SESSION['INSTALL']['db_user']."\" /></td>
					<td>(z.b. etoauser)</td>
				</tr>
				<tr>
					<th>Passwort:</th>
					<td><input type=\"password\" name=\"db_password\" value=\"".$_SESSION['INSTALL']['db_password']."\" /></td>
					<td>(mind. 10 Zeichen)</td>
				</tr>
			</table>
		</fieldset><br/>
		
		<br/><input type=\"submit\" name=\"install_check\" value=\"Eingaben prüfen\" />
		</form>";	
	}
}
else
{
	echo "Ihre Konfigurationsdatei existiert bereits!";
}

	define("PASSWORD_SALT","wokife63wigire64reyodi69");
	define('LOGINSERVER_URL',"http://dev.etoa.ch");
	define('ROUNDID',"Testrunde");
	define('ETOA_DEBUG',0);
?>