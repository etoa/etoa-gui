<?PHP

// Load template engine
require_once(RELATIVE_ROOT."inc/template.inc.php");

$tpl->setView('install');
$tpl->setLayout('empty');

$tpl->assign("gameTitle", "Setup");
$tpl->assign("templateDir"," designs/Graphite");

$tpl->assign('title', 'EtoA Installation');

if (!isset($_SESSION)) {
    session_start();
}
	
if (!isset($_SESSION['INSTALL'])) {
	$_SESSION['INSTALL'] = array();
}

if (!configFileExists(DBManager::getInstance()->getConfigFile()))
{
	ob_start();
	
	$numSteps = 4;
	
	if (isset($_POST['install_check']))
	{
		$_SESSION['INSTALL']['db_server'] = $_POST['db_server'];
		$_SESSION['INSTALL']['db_name'] = $_POST['db_name'];
		$_SESSION['INSTALL']['db_user'] = $_POST['db_user'];
		$_SESSION['INSTALL']['db_password'] = $_POST['db_password'];

		
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
				$tpl->assign('msg', 'Datenbankverbindung erfolgreich!');
				
				$_SESSION['INSTALL']['step']=2;
				$step = 2;
			}
			else
			{
				$tpl->assign('errmsg', 'Verbindung fehlgeschlagen! Fehler: '.mysql_error());
				$_SESSION['INSTALL']['step']=1;
				$step = 1;
			}
		}
		else
		{
			$tpl->assign('errmsg', 'Achtung! Du hast nicht alle Felder ausgef&uuml;lt!');
		}
	}
	elseif(isset($_POST['step2_submit']) && $_POST['step2_submit'])
	{
		$step = 2;

		$_SESSION['INSTALL']['round_name'] = $_POST['round_name'];
		$_SESSION['INSTALL']['loginserver_url'] = $_POST['loginserver_url'];

		if ($_POST['round_name'] != "")
		{
			$step = 3;
			$_SESSION['INSTALL']['step'] = 3;
			
		}
		else
		{
			$tpl->assign('errmsg', 'Achtung! Du hast nicht alle Felder ausgef&uuml;lt!');
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
			$tpl->assign('errmsg', 'Achtung! Du hast nicht alle Felder ausgef&uuml;lt!');
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

	$navElems = array();
	for ($i=1; $i <= $numSteps; $i++) {
		if ($i <= $step) {
			$navElems['Schritt '.$i] = '?step='.$i;
		} else {
			$navElems['Schritt '.$i] = null;
		}
	}
	$tpl->assign('installMenu', $navElems);
	
	if($step==4)
	{
		$dbCfg = array(
			'host' => $_SESSION['INSTALL']['db_server'],
			'dbname' => $_SESSION['INSTALL']['db_name'],
			'user' => $_SESSION['INSTALL']['db_user'],
			'password' => $_SESSION['INSTALL']['db_password'],
		);			
		DBManager::getInstance()->connect(0, $dbCfg);

		$dbConfigSting = json_encode($dbCfg);
		
		$dbConfigStingEventHandler = "[mysql]
host = ".$dbCfg['host']."
database = ".$dbCfg['dbname']."
user = ".$dbCfg['user']."
password = ".$dbCfg['password']."
";
		$eventhandlerFile = "/etc/etoad/roundx.cfg";
		
		$cfg = Config::getInstance();
		$cfg->set("referers",$_SESSION['INSTALL']['referers']);
		$cfg->set("roundname",$_SESSION['INSTALL']['round_name']);
		$cfg->set("loginurl",$_SESSION['INSTALL']['loginserver_url']);

		writeConfigFile(DBManager::getInstance()->getConfigFile(), $dbConfigSting);
		
		$tpl->assign('msg', 'Konfiguration gespeichert!');
		
		if (!configFileExists(DBManager::getInstance()->getConfigFile()))
		{
			echo "Fertig! Du musst nun den folgenden Inhalt in eine neue Textdatei namens <b>".getConfigFilePath(DBManager::getInstance()->getConfigFile())."</b> speichern!<br/><br/>
			<pre class=\"code\">".$dbConfigSting."</pre><br /><br />";
		} else {
			$_SESSION['INSTALL']['step'] = 1;
		}
		
		echo "Für den Eventhandler musst du noch den folgenden Inhalt in eine Konfigurationsdatei, z.B. <b>".$eventhandlerFile."</b>, speichern:<br/><br/>
			<pre class=\"code\">".$dbConfigStingEventHandler."</pre>";
		echo "<p><input type=\"button\" onclick=\"document.location='admin'\" value=\"Zum Admin-Login\"/> &nbsp; 
		<input type=\"button\" onclick=\"document.location='".getLoginUrl()."'\" value=\"Zum Loginserver\"/></p>";
	
	}		
	
	elseif($step==3)
	{
		$dbCfg = array(
			'host' => $_SESSION['INSTALL']['db_server'],
			'dbname' => $_SESSION['INSTALL']['db_name'],
			'user' => $_SESSION['INSTALL']['db_user'],
			'password' => $_SESSION['INSTALL']['db_password'],
		);			
		DBManager::getInstance()->connect(0, $dbCfg);		
		
		$cfg = Config::getInstance();
		
		$str = "<form action=\"?\" method=\"post\">
		<fieldset>
			<legend>Weitere Einstellungen</legend>
			<table>
				<tr>
					<th>Referers:</th>
					<td><textarea name=\"referers\" rows=\"6\" cols=\"50\">".(isset($_SESSION['INSTALL']['referers']) ? $_SESSION['INSTALL']['referers'] : $cfg->get('referers'))."</textarea><br/>
					(alle Seiten, welche als Absender gelten sollen. Also der Loginserver, sowie der aktuelle Server. Mache für jeden Eintrag eine neue Linie!)</td>
					<td></td>
				</tr>
			</table>
		</fieldset>		
		<p><input type=\"submit\" name=\"step3_submit\" value=\"Weiter\" /></p>";
		$tpl->assign('installform', $str);
	}		
	
	elseif($step==2)
	{
		$str = "<form action=\"?\" method=\"post\">
		<fieldset>
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
					<td>(z.b. http://www.etoa.ch, leerlassen für lokales Login)</td>
				</tr>
			</table>
		</fieldset>		
		<p><input type=\"submit\" name=\"step2_submit\" value=\"Weiter\" /></p>";
		$tpl->assign('installform', $str);
	}	
	else
	{
		$str = "<fieldset>
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
		</fieldset>		
		<p><input type=\"submit\" name=\"install_check\" value=\"Eingaben prüfen\" /></p>";	
		$tpl->assign('installform', $str);
	}
	$tpl->assign('content', ob_get_clean());
}
else
{
	$tpl->assign('errmsg', "Ihre Konfigurationsdatei existiert bereits!");
}

$tpl->render();

?>
