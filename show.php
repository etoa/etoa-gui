<?PHP
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: main_i.php
	// 	Topic: Alternative Include-Seite
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.03.2006
	// 	Kommentar:
	//

	/**
	* Alternative main file for out of game viewing of specific pages
	*
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/
	
	require_once("bootstrap.inc.php");

	function showTitle($title)
	{
		echo "<br/><a href=\"?\"><img src=\"images/game_logo.gif\" alt=\"EtoA Logo\" /></a>";
		echo "<h1>$title - ".ROUNDID."</h1>";
	}

	ini_set('arg_separator.output',  '&amp;');
	session_start();
	
	require_once("conf.inc.php");
	require_once("functions.php");

	dbconnect();
	
	$cfg = Config::getInstance();
	$conf = $cfg->getArray();
	include("def.inc.php");

	$indexpage = array();
	$contentpage = array();
	$indexpage['register']='Anmelden';
	$indexpage['pwforgot']='Passwort vergessen';
	$indexpage['ladder']='Rangliste';
	$indexpage['gamestats']='Serverstatistiken';
	$indexpage['pillory']='Pranger';
	$indexpage['feeds']='Feeds';
	$indexpage['contact']='Kontakt';
	$contentpage['help']='Hilfe';
	
	if (isset($_SESSION[ROUNDID]))
	{
		$s = $_SESSION[ROUNDID];
	}
	else
	{
		$s = array();
	}

	if (isset($s['user_id']))
	{
		$cu = new CurrentUser($s['user_id']);
	}
	else
		$cu = null;

	if (isset($cu->css_style) && $cu->css_style!="")
	{
		define("CSS_STYLE",$cu->css_style);
	}
	else
	{
		define("CSS_STYLE",$cfg->get('default_css_style'));
	}

	if (isset($cu->image_url) && $cu->image_url!='' && $cu->image_ext!='')
	{
		define('IMAGE_PATH',$cu->image_url);
		define('IMAGE_EXT',$cu->image_ext);
	}
	else
	{
		define("IMAGE_PATH",$cfg->get('default_image_path'));
		define("IMAGE_EXT","gif");
	}	
	
	$index = isset($_GET['index']) ? $_GET['index'] : null;
	$page = isset($_GET['page']) ? $_GET['page'] : null;
	$info = isset($_GET['info']) ? $_GET['info'] : null;

	//XAJAX
	include("inc/xajax.inc.php");
?>
<?PHP echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?PHP 
				echo $cfg->get('game_name')." ".$cfg->p1('game_name').' | '.ROUNDID;
				if ($index!="")
				{			
					echo " | ".$indexpage[$index];
				}
			?>
		</title>
		<meta name="author" content="EtoA Gaming" />
		<meta name="description" content="EtoA - Das kostenlose Sci-Fi Browsergame." />
		<meta name="keywords" content="Escape to Andromeda, Browsergame, Strategie, Simulation, Andromeda, MMPOG, RPG" />
		<meta name="date" content="<?PHP echo date('Y-m-d'); ?>" />
		<meta name="robots" content="index, follow" />
		<meta name="language" content="de" />
		<meta name="distribution" content="global" />
		<meta name="audience" content="all" />
		<meta name="author-mail" content="mail@etoa.de" />
		<meta name="publisher" content="EtoA Gaming" />
		<meta name="copyright" content="(c) 2006 by EtoA Gaming" />
		<meta name="revisit-after" content="15 days" />		
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="content-style-type" content="text/css" />
		<meta http-equiv="content-language" content="de" />

		<meta http-equiv="Page-Enter" content="blendTrans(Duration=0.3)" />
		<meta http-equiv="Page-Exit" content="blendTrans(Duration=0.3)" />
		
		<script src="js/main.js" type="text/javascript"></script>

		<?PHP
			// XAJAX
		 	echo $xajax->printJavascript(XAJAX_DIR);
		
			$style = DESIGN_DIRECTORY."/".CSS_STYLE."/style.css";
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$style\" />";  

			if (file_exists(CSS_STYLE."/scripts.js"))
			echo "<script src=\"".CSS_STYLE."/scripts.js\" type=\"text/javascript\"></script>";			 
		?>

		<link rel="stylesheet" href="css/general.css" type="text/css" />
		<link rel="stylesheet" href="css/outgame.css" type="text/css" />

	</head>
	<body id="outGameBody">
		<?PHP
			if ($index!="" || ($page=="help" && !isset($s['user_id'])))
			{
	    	echo '<div id="outGameTop">';
	    	echo '<div><a href="'.LOGINSERVER_URL.'">Startseite</a><a href="?">Übersicht</a></div><b>'.ROUNDID.'</b> &nbsp; ';
	    	foreach ($indexpage as $k => $v)
	    	{
	    		echo '<a href="?index='.$k.'">'.$v.'</a>';
	    	}
	    	foreach ($contentpage as $k => $v)
	    	{
	    		echo '<a href="?page='.$k.'">'.$v.'</a>';
	    	}	    	
	    	echo '<br style="clear:both;" /></div>';
	  	}

		initTT();		
		
		
		echo '<div id="outGameContent">';
			
		
			
			$show = true;
			if ($cfg->get('register_key')!="" && !isset($s['user_id']))
			{
				if (isset($_POST['reg_key_auth_submit']))
				{
				
					if ($_POST['reg_key_auth_value']==$cfg->get('register_key'))
					{
						$_SESSION['reg_key_auth']=$cfg->get('register_key');
					}
					else
					{
						echo "Falscher Schlüssel!<br/><br/>";						
					}
				}
				
				if ($_SESSION['reg_key_auth']!=$cfg->get('register_key'))
				{
					$show = false;
				}
			}
			
			
			if ($show)
			{
				
				if ($index!="")
				{
					$index = ($index=="stats") ? "ladder" : $index;
					$page=$index;
					$sub="index/";
				}
				elseif ($info!="")
				{
					$page=$info;
					$sub="info/";
				}		
				elseif (($page!="" && isset($s['user_id'])) || $page=="help")
				{	
					$showed=true;
					$page=$page;
					$sub="content/";
					$external=true;
				}
				else
				{
					showTitle('Öffentliche Seiten');
					echo '<table id="outgameOverviewTable">';
					echo '<tr>
						<td><a href="?index=register"><img src="images/outgame/register.png" alt="Anmelden" /><br/>Anmelden</a></td>
						<td><a href="?index=pwforgot"><img src="images/outgame/pwforgot.png" alt="Passwort anfordern" /><br/>Passwort vergessen?</a></td>
						<td><a href="?index=ladder"><img src="images/outgame/stats.png" alt="Rangliste" /><br/>Rangliste</a></td>
					</tr>';
					echo '<tr>
						<td><a href="?index=gamestats"><img src="images/outgame/gamestats.png" alt="Serverstatistiken" /><br/>Serverstatistiken</a></td>
						<td><a href="?index=pillory"><img src="images/outgame/pillory.png" alt="Pranger" /><br/>Pranger</a></td>
						<td><a href="?index=feeds"><img src="images/outgame/feeds.png" alt="Feeds" /><br/>Feeds</a></td>
					</tr>';
					echo '<tr>
						<td><a href="?index=contact"><img src="images/outgame/contact.png" alt="Kontakt" /><br/>Kontakt</a></td>
						<td><a href="?page=help"><img src="images/outgame/help.png" alt="Hilfe" /><br/>Hilfe</a></td>
						<td></td>
					</tr>';
					
					echo '</table>';
					exit;
				}
				
				
				if (!eregi("^[a-z\_]+$",$page) || strlen($page)>50)
				{
					die("<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br/><br/>
					<a href=\"javascript:window.close();\">Schliessen</a><br/><br/>");
				}
				if (file_exists($sub.$page.".php"))
				{
					$popup = true;
					include ($sub.$page.".php");
					echo "<br/><br/>";
				}
				else
				{
					echo "<h1>Fehler:</h1> Die Seite <b>".$page."</b> existiert nicht!<br/><br/>";
					echo '<script type="text/javascript">setTimeout("document.location=\'?\'",1000);</script>';
				}
				echo "<input type=\"button\" value=\"Fenster schliessen\" onclick=\"window.close();\" /><br/><br/>";
				
				dbclose();
				$_SESSION[ROUNDID] = $s;
			}
			else
			{
				echo "<h1>Zugang erfordert Schlüssel</h1>";
				echo "<form action=\"?index=".$_GET['index']."\" method=\"post\">
				Bitte Schlüssel eingeben: <input type=\"text\" value=\"\" name=\"reg_key_auth_value\" /> &nbsp;
				<input type=\"submit\" value=\"Prüfen\" name=\"reg_key_auth_submit\" />
				</form>";
			}
		?>
		
		</div>
	</body>
</html>
