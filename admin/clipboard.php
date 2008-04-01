<?PHP
	// Session-Cookie setzen
	ini_set('arg_separator.output',  '&amp;');
	session_start();

	// Funktionen und Config einlesen
	if (!@include_once("../conf.inc.php")) die("conf.inc.php does not exists, please read INSTALL for how to create this file!");
	require("../functions.php");
	require("inc/admin_functions.inc.php");

	// Mit der DB verbinden
	dbconnect();
	
	// Admin defs
	
	define('CACHE_ROOT','../cache');
	define('CLASS_ROOT','../classes');
	
	// Config-Werte laden
	$conf = get_all_config();
	include("../def.inc.php");
	
	// Feste Konstanten
	define('SESSION_NAME',"adminsession");

	// XAJAX
	include("inc/xajax_admin.inc.php");

	mt_srand(time());
?>

<?PHP	echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">	
	<head>
		<title>Clipboard</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" href="../css/general.css" type="text/css" />

		<meta name="author" content="Nicolas Perrenoud" />
		<meta name="keywords" content="Escape to Andromeda, Browsergame, Strategie, Simulation, Andromeda, MMPOG, RPG" />
		<meta name="date" content="2004-10-01" />
		<meta name="robots" content="nofollow" />

		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="content-language" content="de" />

		<script src="../scripts.js" type="text/javascript"></script>
		<script src="scripts.js" type="text/javascript"></script>
		<?PHP
			$xajax->printJavascript("../".XAJAX_DIR); 
		?>
	</head>
	<body class="index">
		<?PHP

		$s = $_SESSION[SESSION_NAME];
		if ($s['user_id']>0)
		{
			echo "<h1>Zwischenablage</h1>";
			
			if ($_GET['add_user']>0)
			{
				$s['cp_users'][$_GET['add_user']]=$_GET['add_user'];
			}
			if ($_GET['rem_user']>0)
			{
				$s['cp_users'][$_GET['rem_user']]=null;
			}			
			
			echo "<h2>Benutzer [<a href=\"index.php?page=home&amp;sub=stats\" target=\"main\">alle</a>]</h2>";
			if (count($s['cp_users'])>0)
			{
				foreach ($s['cp_users'] as $uid)
				{
					if ($uid>0)
					{
						$res = dbquery("SELECT user_nick FROM users WHERE user_id=".$uid.";");
						if (mysql_num_rows($res)>0)
						{
							$arr = mysql_fetch_row($res);
							echo "<a href=\"index.php?page=user&amp;sub=edit&amp;user_id=".$uid."\" target=\"main\">
							<a href=\"index.php?page=user&amp;sub=edit&amp;user_id=".$uid."\" target=\"main\">
							".$arr[0]."</a>&nbsp;
							<a href=\"?rem_user=".$uid."\" target=\"_self\">
							<img src=\"../images/delete.gif\" style=\"border:none;height:10px;\"></a>

							<br/>";
						}
					}
				}
			}
			else
			{			
				echo "<i>Nichts vorhanden!</i><br/><br/>";
			}
			
			echo "<br/><br/>
			[<a href=\"?\" target=\"_self\">Aktualisieren</a>]
			[<a href=\"index.php?cbclose=1\" target=\"_top\">Schliessen</a>]";
		}
		else
		{
			echo "Nicht eingeloggt!";
		}
		$_SESSION[SESSION_NAME]=$s;
	?>
</body>
</html>
				
				
