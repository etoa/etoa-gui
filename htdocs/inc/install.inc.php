<?PHP

// Load template engine
require_once(RELATIVE_ROOT."inc/template.inc.php");
$tpl->assign("gameTitle","Setup");
$tpl->assign("templateDir","designs/Discovery");
$indexpage = array();
$indexpage['feeds']=array('url'=>'.','label'=>'Setup');
$tpl->assign("topmenu",$indexpage);

if (!isset($_SESSION))
    session_start();
$tpl->display(getcwd()."/tpl/headerext.html");

if (!isset($_SESSION['INSTALL']))
	$_SESSION['INSTALL'] = array();

if (!configFileExists(DBManager::getInstance()->getConfigFile()))
{
	echo "<h1>EtoA Installation</h1>";


	if (isset($_POST['install_check']))
	{
		$_SESSION['INSTALL']['db_server'] = $_POST['db_server'];
		$_SESSION['INSTALL']['db_name'] = $_POST['db_name'];
		$_SESSION['INSTALL']['db_user'] = $_POST['db_user'];
		$_SESSION['INSTALL']['db_password'] = $_POST['db_password'];

		
		//echo "Prüfe Eingaben....<br/>";
		//echo "Prüfe Eingaben....<br/>";
		if ($_POST['db_server'] != "" && $_POST['db_name'] != "" && $_POST['db_user'] != "" && $_POST['db_password'] != "")
		{
			$dbCfg = array(
				'host' => $_SESSION['INSTALL']['db_server'],
				'dbname' => $_SESSION['INSTALL']['db_name'],
				'user' => $_SESSION['INSTALL']['db_user'],
				'password' => $_SESSION['INSTALL']['db_password'],
			);			
			if (DBManager::getInstance()->connect(0, $dbCfg))
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

		if ($_POST['round_name'] != "" && $_POST['loginserver_url'] != "")
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
		
		$dbCfg = array(
			'host' => $_SESSION['INSTALL']['db_server'],
			'dbname' => $_SESSION['INSTALL']['db_name'],
			'user' => $_SESSION['INSTALL']['db_user'],
			'password' => $_SESSION['INSTALL']['db_password'],
		);			
		DBManager::getInstance()->connect(0, $dbCfg);
		
		$cfg = Config::getInstance();
		$cfg->set("referers",$_SESSION['INSTALL']['referers']);
		$cfg->set("roundname",$_SESSION['INSTALL']['round_name']);
		$cfg->set("loginurl",$_SESSION['INSTALL']['loginserver_url']);

		echo "<div style=\"color:#0f0\">Refererliste gespeichert!</div><br/>";
		
		$dbCfg = array(
			'host' => $_SESSION['INSTALL']['db_server'],
			'dbname' => $_SESSION['INSTALL']['db_name'],
			'user' => $_SESSION['INSTALL']['db_user'],
			'password' => $_SESSION['INSTALL']['db_password'],
		);
		
		echo "Fertig! Du musst nun den folgenden Inhalt in eine neue Textdatei namens <b>config/".DBManager::getInstance()->getConfigFile()."</b> speichern!<br/><br/>
			<textarea style=\"width:900px;background:#eee;color:#000;font-family:courier new;margin:0px auto;text-align:left;\">
			".json_encode($dbCfg)."
		</textarea>";
		echo "<p> <a href=\"admin\">Zum Admin-Login</a> &nbsp; <a href=\"".$_SESSION['INSTALL']['loginserver_url']."\">Zum Loginserver</a></p>";
	
	}		
	
	elseif($step==3)
	{
		echo "<div style=\"font-weight:bold;color:#666;\">
		<a href=\"?step=1\" style=\"color:000\">Schritt 1</a> |
		<a href=\"?step=2\" style=\"color:000\">Schritt 2</a> |
		<a href=\"?step=3\" style=\"color:000\">Schritt 3</a> |
		Schritt 4
		</div><br/>";
		
		$dbCfg = array(
			'host' => $_SESSION['INSTALL']['db_server'],
			'dbname' => $_SESSION['INSTALL']['db_name'],
			'user' => $_SESSION['INSTALL']['db_user'],
			'password' => $_SESSION['INSTALL']['db_password'],
		);			
		DBManager::getInstance()->connect(0, $dbCfg);		
		
		$cfg = Config::getInstance();
		
		echo "<form action=\"?\" method=\"post\">
		<fieldset style=\"width:700px;margin:0px auto;\">
			<legend>Weitere Einstellungen</legend>
			<table>
				<tr>
					<th>Referers:</th>
					<td><textarea name=\"referers\" rows=\"6\" cols=\"50\">".(isset($_SESSION['INSTALL']['referers']) ? $_SESSION['INSTALL']['referers'] : $cfg->get('referers'))."</textarea></td>
					<td>(alle Seiten, welche als Absender gelten sollen. Also der Loginserver, sowie der aktuelle Server. Mache für jeden Eintrag eine neue Linie!)</td>
				</tr>
			</table>
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
			</table>
		</fieldset>		
		<br/><input type=\"submit\" name=\"step2_submit\" value=\"Weiter\" />						
		</form>";		
		
		
	}	
	else
	{
		echo "<div style=\"font-weight:bold;color:#666;\">
		<a href=\"?step=1\" style=\"color:000\">Schritt 1</a> |
		Schritt 2 |
		Schritt 3 |
		Schritt 4
		</div><br/>";
		
		echo "<p>Anscheinend existiert noch keine Konfigurationsdatei für diese EtoA-Instanz. Bitte erstelle
	eine indem du folgendes Formular ausfüllst:</p>";
		
		echo "<form action=\"?\" method=\"post\" autocomplete=\"off\">
		<fieldset style=\"width:400px;margin:0px auto;\">
			<legend>MySQL-Datenbank</legend>
			<table>
				<tr>
					<th>Server:</th>
					<td><input type=\"text\" name=\"db_server\" value=\"".(isset($_SESSION['INSTALL']['db_server']) ? $_SESSION['INSTALL']['db_server'] : '')."\" autocomplete=\"off\" /></td>
					<td>(z.b. localhost)</td>
				</tr>
				<tr>
					<th>Datenbank:</th>
					<td><input type=\"text\" name=\"db_name\" value=\"".(isset($_SESSION['INSTALL']['db_name']) ? $_SESSION['INSTALL']['db_name'] : '')."\" autocomplete=\"off\" /></td>
					<td>(z.b. etoaroundx)</td>
				</tr>
				<tr>
					<th>User:</th>
					<td><input type=\"text\" name=\"db_user\" value=\"".(isset($_SESSION['INSTALL']['db_user']) ? $_SESSION['INSTALL']['db_user'] : '')."\" autocomplete=\"off\" /></td>
					<td>(z.b. etoauser)</td>
				</tr>
				<tr>
					<th>Passwort:</th>
					<td><input type=\"password\" name=\"db_password\" value=\"".(isset($_SESSION['INSTALL']['db_password']) ? $_SESSION['INSTALL']['db_password'] : '')."\" autocomplete=\"off\" /></td>
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
