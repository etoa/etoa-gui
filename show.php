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

	function showTitle($title)
	{
		echo "<br/><a href=\"?\"><img src=\"images/game_logo.gif\" alt=\"EtoA Logo\" /></a>";
		echo "<h1>$title - ".GAMEROUND_NAME."</h1>";
	}

	ini_set('arg_separator.output',  '&amp;');
	session_start();
	mt_srand();
	
	define('IS_ADMIN_MODE',false);

	require_once("conf.inc.php");
	require_once("functions.php");

	dbconnect();

	$conf = get_all_config();
	include("def.inc.php");

	$indexpage = array();
	$contentpage = array();
	$indexpage['register']='Anmelden';
	$indexpage['pwforgot']='Passwort vergessen';
	$indexpage['stats']='Rangliste';
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
		define("CSS_STYLE",$conf['default_css_style']['v']);
	}

	if (isset($cu->image_url) && $cu->image_url!='' && $cu->image_ext!='')
	{
		define('IMAGE_PATH',$cu->image_url);
		define('IMAGE_EXT',$cu->image_ext);
	}
	else
	{
		define("IMAGE_PATH",$conf['default_image_path']['v']);
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
				echo $conf['game_name']['v']." ".$conf['game_name']['p1'].' | '.GAMEROUND_NAME;
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

	</head>
	<body id="outGameBody">
		<?PHP
			if ($index!="" || ($page=="help" && !isset($s['user_id'])))
			{
	    	echo '<div id="outGameTop">';
	    	echo '<div><a href="'.LOGINSERVER_URL.'">Startseite</a><a href="?">Übersicht</a></div><b>'.GAMEROUND_NAME.'</b> &nbsp; ';
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
		?>
		
		<!-- Stuff for DHTML Tipps -->
		<div id="Migoicons" style="visibility:hidden;position:absolute;z-index:1000;top:-100px;border:none"></div>
		<script type="text/javascript">
			//stl=["white","##222255","","","",,"white","#606578","","","",,,,2,"#222255",2,,,,,"",,,,]
			var TipId="Migoicons"
			var FiltersEnabled = 1
			mig_clay()
		</script>		
		
		
		
		<div id="outGameContent">
			
		<?PHP
			
			$show = true;
			if ($conf['register_key']['v']!="" && !isset($s['user_id']))
			{
				if (isset($_POST['reg_key_auth_submit']))
				{
				
					if ($_POST['reg_key_auth_value']==$conf['register_key']['v'])
					{
						$_SESSION['reg_key_auth']=$conf['register_key']['v'];
					}
					else
					{
						echo "Falscher Schlüssel!<br/><br/>";						
					}
				}
				
				if ($_SESSION['reg_key_auth']!=$conf['register_key']['v'])
				{
					$show = false;
				}
			}
			
			
			if ($show)
			{
				
				if ($index!="")
				{
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
					showTitle('Übersicht öffentlicher Seiten');
					echo '<table id="outgameOverviewTable">';
					echo '<tr>
						<td><a href="?index=register"><img src="images/outgame/register.png" alt="Anmelden" /><br/>Anmelden</a></td>
						<td><a href="?index=pwforgot"><img src="images/outgame/pwforgot.png" alt="Passwort anfordern" /><br/>Passwort vergessen?</a></td>
						<td><a href="?index=stats"><img src="images/outgame/stats.png" alt="Rangliste" /><br/>Rangliste</a></td>
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
