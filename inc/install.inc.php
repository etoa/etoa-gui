e<?PHP

define('ADMIN_ROOT_GROUP',8);

// Load template engine
require_once(RELATIVE_ROOT."inc/template.inc.php");
$tpl->assign("gameTitle","Setup");
$tpl->assign("templateDir","designs/Discovery");
$indexpage = array();
$indexpage['feeds']=array('url'=>'.','label'=>'Setup');
$tpl->assign("topmenu",$indexpage);
$tpl->display(getcwd()."/tpl/headerext.html");

session_start();

if (!isset($_SESSION['INSTALL']))
	$_SESSION['INSTALL'] = array();

if (!file_exists(RELATIVE_ROOT."config/db.config.php"))
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
	elseif(isset($_POST['step2_submit']) && $_POST['step2_submit'])
	{
		$step = 2;

		$_SESSION['INSTALL']['round_name'] = $_POST['round_name'];
		$_SESSION['INSTALL']['loginserver_url'] = $_POST['loginserver_url'];
		$_SESSION['INSTALL']['password_salt'] = $_POST['password_salt'];

		if ($_POST['round_name'] != "" && $_POST['loginserver_url'] != "" && $_POST['password_salt'] != "")
		{
			$step = 3;
			$_SESSION['INSTALL']['step'] = 3;
			
		}
		else
		{
			echo "<div style=\"color:#f00;\">Achtung! Du hast nicht alle Felder ausgef&uuml;lt!</div>";
		}		
	}	
	
	elseif(isset($_POST['step3_submit']) && $_POST['step3_submit'])
	{
		$step = 3;
		if ($_POST['referers'] != "")
		{
			$step = 4;
			$_SESSION['INSTALL']['step'] = 4;
			$_SESSION['INSTALL']['referers'] = $_POST['referers'];
			if (isset($_POST['admin_user']))
				$_SESSION['INSTALL']['admin_user'] = $_POST['admin_user'];
			if (isset($_POST['admin_user_pw']))
				$_SESSION['INSTALL']['admin_user_pw'] = $_POST['admin_user_pw'];
			
		}
		else
		{
			echo "<div style=\"color:#f00;\">Achtung! Du hast nicht alle Felder ausgef&uuml;lt!</div>";
		}		
	}		
	
	if (isset($_SESSION['INSTALL']['step']) && isset($_GET['step']) && $_GET['step']>0)
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
		$cfg->set("roundname",$_SESSION['INSTALL']['round_name']);
		$cfg->set("loginurl",$_SESSION['INSTALL']['loginserver_url']);
		$cfg->set("password_salt",$_SESSION['INSTALL']['password_salt']);

		echo "<div style=\"color:#0f0\">Refererliste gespeichert!</div><br/>";
		
		if (isset($_SESSION['INSTALL']['admin_user']) && $_SESSION['INSTALL']['admin_user']!="" && $_SESSION['INSTALL']['admin_user_pw']!="")
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
	
?&gt;";

		echo "Fertig! Du musst nun den folgenden Inhalt in eine neue Textdatei namens <b>config/db.config.php</b> speichern!<br/><br/>
			<div style=\"width:900px;background:#eee;color:#000;font-family:courier new;margin:0px auto;text-align:left;\">
			".nl2br($out)."
		</div>";
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
		<fieldset style=\"width:700px;margin:0px auto;\">
			<legend>Weitere Einstellungen</legend>
			<table>
				<tr>
					<th>Referers:</th>
					<td><textarea name=\"referers\" rows=\"6\" cols=\"50\">".(isset($_SESSION['INSTALL']['referers']) ? $_SESSION['INSTALL']['referers'] : $cfg->get('referers'))."</textarea></td>
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
		<fieldset style=\"width:700px;;margin:0px auto;\">
			<legend>Allgemeine Daten</legend>
			<table>
				<tr>
					<th>Name der Runde:</th>
					<td><input type=\"text\" name=\"round_name\" value=\"".(isset($_SESSION['INSTALL']['round_name']) ? $_SESSION['INSTALL']['round_name'] : 'Runde X')."\" /></td>
					<td>(z.b. Runde 1)</td>
				</tr>
				<tr>
					<th>Loginserver-URL:</th>
					<td><input type=\"text\" name=\"loginserver_url\" value=\"".(isset($_SESSION['INSTALL']['loginserver_url']) ? $_SESSION['INSTALL']['loginserver_url'] : 'http://www.etoa.ch')."\" /></td>
					<td>(z.b. http://www.etoa.ch)</td>
				</tr>
				<tr>
					<th>Passwort-Salt:</th>
					<td><input type=\"text\" name=\"password_salt\" value=\"".(isset($_SESSION['INSTALL']['password_salt']) ? $_SESSION['INSTALL']['password_salt'] : '')."\" /></td>
					<td>(mit diesem Schlüssel werden alle Passwörter zusätzlich verschlüsselt; darf während einer laufenden Runde nicht ge&auml;ndert werde da sonst die Passw&ouml;rter nicht mehr gehen)</td>
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
		<fieldset style=\"width:400px;margin:0px auto;\">
			<legend>MySQL-Datenbank</legend>
			<table>
				<tr>
					<th>Server:</th>
					<td><input type=\"text\" name=\"db_server\" value=\"".(isset($_SESSION['INSTALL']['db_server']) ? $_SESSION['INSTALL']['db_server'] : '')."\" /></td>
					<td>(z.b. localhost)</td>
				</tr>
				<tr>
					<th>Datenbank:</th>
					<td><input type=\"text\" name=\"db_name\" value=\"".(isset($_SESSION['INSTALL']['db_name']) ? $_SESSION['INSTALL']['db_name'] : '')."\" /></td>
					<td>(z.b. etoaroundx)</td>
				</tr>
				<tr>
					<th>User:</th>
					<td><input type=\"text\" name=\"db_user\" value=\"".(isset($_SESSION['INSTALL']['db_user']) ? $_SESSION['INSTALL']['db_user'] : '')."\" /></td>
					<td>(z.b. etoauser)</td>
				</tr>
				<tr>
					<th>Passwort:</th>
					<td><input type=\"password\" name=\"db_password\" value=\"".(isset($_SESSION['INSTALL']['db_password']) ? $_SESSION['INSTALL']['db_password'] : '')."\" /></td>
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

$tpl->display(getcwd()."/tpl/footer.html");


?>