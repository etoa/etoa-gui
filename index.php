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
	// 	File: index.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.03.2006
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Main game file, provides the template and includes all pages
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	//
	// Basic stuff
	//

	//Fehler ausgabe definiert (Lamborghini)
  ini_set('display_errors', 1);
	ini_set('arg_separator.output',  '&amp;');

	// Session-Cookie setzen
	session_start();

	// Zufallsgenerator initialisieren
	mt_srand(time());

	// Funktionen und Config einlesen
	require_once("../conf.inc.php");
	require_once("functions.php");
	require_once("classes.php");

	// Renderzeit-Start festlegen
	$render_time = explode(" ",microtime());
	$render_starttime=$render_time[1]+$render_time[0];


	// Mit der DB verbinden und Config-Werte laden
	dbconnect();

	$conf = get_all_config();	
	require_once("def.inc.php");
	
	// Load smarty template engine
	require(SMARTY_DIR.'/Smarty.class.php');
	
	// Firstview
	$firstview = false;
	
	//
	// Session- & Login - Checks
	//

	// User einloggen falls nötig
	if (isset($_POST['login_submit']))
	{
		require_once("inc/login.inc.php");
	}

	// Falls Logout zum Loginserver wechseln
	if (isset($_GET['logout']) && $_GET['logout']!=null)
	{
		require_once("inc/logout.inc.php");
	}


	if (isset($_SESSION[ROUNDID]))
	{
		$s = $_SESSION[ROUNDID];
	}
	else
	{
		$s = array();
	}

	// Session prüfen
	if (!isset($s['user']['id']) || $s['user']['id']==0 || $s['user']['id']=='')
	{
		session_destroy();
		header("Location: ".LOGINSERVER_URL."?page=err&err=nosession");
		//print_r($_SESSION);
		echo "<br/><br/>";
		print_r($s);
		echo "<h1>Session nicht mehr vorhanden!</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=nosession\">hier</a> klicken...";
		exit;
	}
	// Benutzerdaten laden
	$ures = dbquery("
	SELECT 
		user_acttime,
		MD5(user_id) AS uid,
		MD5(user_logintime) AS lt,
		user_session_key AS sk,
    user_race_id,
    user_blocked_from,
    user_blocked_to,
    user_ban_reason,
    user_ban_admin_id,
    user_hmode_from,
    user_hmode_to,
    user_points,
    user_deleted,
    user_alliance_application,
    user_registered,
    user_irc_name,
    user_irc_pw
	FROM 
		users 
	WHERE 
		user_id='".$s['user']['id']."' 
	;");
	if (mysql_num_rows($ures)==0)
	{
		session_destroy();
		header("Location: ".LOGINSERVER_URL."?page=err&err=usernotfound");
		print_r($_SESSION);
		echo "<br/><br/>";
		print_r($s);
		echo "<h1>Benutzer nicht mehr vorhanden!</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=usernotfound\">hier</a> klicken...";
		exit;
	}
	$uarr = mysql_fetch_array($ures);
	
	// Check timeout	
	if ($uarr['user_acttime']+$conf['user_timeout']['v'] < time())
	{
		session_destroy();
		header("Location: ".LOGINSERVER_URL."?page=err&err=timeout");
		echo "<h1>Timeout ".$conf["user_timeout"]["v"]."s</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=timeout\">hier</a> klicken...";
		exit;
	}

	// Session prüfen
	$session_valid=false;
	if ($s['key']!="")
	{
		//&& substr($s['key'],96,32)==md5(gethostbyaddr($_SERVER['REMOTE_ADDR'])) 

		if (substr($s['key'],64,32)==md5(GAMEROUND_NAME) 
		&& substr($s['key'],128,32)==md5($_SERVER['HTTP_USER_AGENT']) 
		&& substr($s['key'],160)==session_id() )
		{
			if ($uarr['lt']=substr($s['key'],0,32) && 
			$uarr['uid']==substr($s['key'],32,32) && 
			$uarr['sk']==$s['key'])
			{
				$session_valid=true;
			}
		}
	}
	if (!$session_valid)
	{
		// Zum Loginserver wechseln falls das Session-Cookie noch nicht gesetzt oder fehlerhaft ist
		session_destroy();
		header("Location: ".LOGINSERVER_URL."?page=err&err=session2");
		echo "<h1>Session fehlerhaft</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=session2\">hier</a> klicken...";
		exit;
	}

	// Session-Variabeln zuweisen
	$s['user']['alliance_application'] = $uarr['user_alliance_application']!="" ? 1 : 0;
	$s['user']['race_id'] = $uarr['user_race_id'];
	$s['user']['blocked_from'] = $uarr['user_blocked_from'];
	$s['user']['blocked_to'] = $uarr['user_blocked_to'];
	$s['user']['ban_reason'] = $uarr['user_ban_reason'];
	$s['user']['ban_admin_id'] = $uarr['user_ban_admin_id'];
	$s['user']['hmode_from'] = $uarr['user_hmode_from'];
	$s['user']['hmode_to'] = $uarr['user_hmode_to'];
	$s['user']['points'] = $uarr['user_points'];
	$s['user']['deleted'] = $uarr['user_deleted'];

		//
	// Misc settings
	//

	// Layout-/Grafikdefinitionen
	if ($s['user']['css_style']!='')
	{
		define('CSS_STYLE',DESIGN_DIRECTORY."/".$s['user']['css_style']);
	}
	else
	{
		define('CSS_STYLE',DESIGN_DIRECTORY."/".$conf['default_css_style']['v']);
	}
	define('GAME_WIDTH',$s['user']['game_width']);
	if ($s['user']['image_url']!='' && $s['user']['image_ext']!='')
	{
		define('IMAGE_PATH',$s['user']['image_url']);
		define('IMAGE_EXT',$s['user']['image_ext']);
	}
	else
	{
		define("IMAGE_PATH",$conf['default_image_path']['v']);
		define("IMAGE_EXT","gif");
	}
	

	
	// Chat
  if ($uarr['user_irc_name']!="")
  {  	
	  $crypt = new crypt;  
		$irc_name = $crypt->decrypt(md5(PASSWORD_SALT.$uarr['user_registered']), $uarr['user_irc_name']);								            	            	
	  if ($uarr['user_irc_pw']!="")
	  {            	
			$irc_pw = $crypt->decrypt(md5(PASSWORD_SALT.$uarr['user_registered']), $uarr['user_irc_pw']);								            	            	
	  } 	
	}

	$args = 'nick='.$s['user']['nick'].';points='.$s['user']['points'].';title='.GAMEROUND_NAME;
	if (isset($irc_name))
	{
		$args.=";acc=".$irc_name;
	}
	if (isset($irc_pw))
	{
		$args.=";pw=".$irc_pw;
	}

	$a = base64_encode($args);
	$h = md5($args).md5($a);
	define('CHAT_PATH',CHAT_URL.'/?a='.$a.'&amp;h='.$h);
	define('CHAT_STRING',"window.open('".CHAT_PATH."','chat','width=900,height=700');");

	// Check sitting
	require_once('inc/sitting.inc.php');

	// Set default content page
	$page = (isset($_GET['page']) && $_GET['page']!="") ? $_GET['page'] : DEFAULT_PAGE;

	// Initialize XAJAX and load functions
	include("xajax_etoa.inc.php");

?>
<?PHP echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
';?>
<html xmlns="http://www.w3.org/1999/xhtml">	
	<head>
		<meta name="author" content="EtoA Gaming" />
		<meta name="keywords" content="Escape to Andromeda, Browsergame, Strategie, Simulation, Andromeda, MMPOG, RPG" />
		<meta name="robots" content="nofollow" />
		<meta name="language" content="de" />
		<meta name="distribution" content="global" />
		<meta name="audience" content="all" />
		<meta name="author-mail" content="mail@etoa.de" />
		<meta name="publisher" content="EtoA Gaming" />
		<meta name="copyright" content="(c) 2007 by EtoA Gaming" />

		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
	 	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="content-style-type" content="text/css" />
		<meta http-equiv="content-language" content="de" />

		<title>
			<?PHP 
				echo $conf['game_name']['v']." ".$conf['game_name']['p1'];
				if (file_exists("svnversion"))
				{
					echo " | Rev #";
					readfile("svnversion");
				}
			?>
		</title>
		<link rel="stylesheet" href="general.css" type="text/css" />
		<script src="scripts.js" type="text/javascript"></script>
		<?PHP 
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".CSS_STYLE."/style.css\" />";
			echo $objAjax->printJavascript('xajax');
			echo file_exists(CSS_STYLE."/scripts.js") ? "<script src=\"".CSS_STYLE."/scripts.js\" type=\"text/javascript\"></script>" : ''; 
		?>
	</head>
		<?PHP
			echo file_exists(CSS_STYLE."/template.php") ? '<body onload="preloadImages();">' : '<body>';
		?>	

		<!-- Stuff for DHTML Tipps -->
		<div id="Migoicons" style="visibility:hidden;position:absolute;z-index:1000;top:0px;border:none;"></div>
		<script type="text/javascript">
			//stl=["white","##222255","","","",,"white","#606578","","","",,,,2,"#222255",2,,,,,"",,,,]
			var TipId="Migoicons"
			var FiltersEnabled = 1
			mig_clay()
		</script>

		<?PHP
			// Referers prüfen
			$referer_allow=false;
			foreach ($referers as $rfr)
			{
				if (substr($_SERVER["HTTP_REFERER"],0,strlen($rfr))==$rfr)
				{
					$referer_allow=true;
				}
			}

			// Spiel ist generell gesperrt (ausser für erlaubte IP's)
			$allowed_ips = explode("\n",$conf['offline']['p1']);
			if ($conf['offline']['v']==1 && !in_array($_SERVER['REMOTE_ADDR'],$allowed_ips))
			{
				echo "<h1>Spiel offline</h1>
				Das Spiel ist momentan offline! Schaue sp&auml;ter nochmals vorbei!<br/><br/>
				<a href=\"".LOGINSERVER_URL."\">Hauptseite</a>";
				session_destroy();
				$s=Null;
			}
			// Login ist gesperrt
			elseif ($conf['enable_login']['v']==0)
			{
				echo "<h1>Login deaktiviert</h1>
				Der Login nocht nicht aktiviert!<br/><br/>
				<a href=\"".LOGINSERVER_URL."\">Hauptseite</a>";
				session_destroy();
				$s=Null;
			}
			// Login ist erlaubt aber noch zeitlich zu früh
			elseif ($conf['enable_login']['v']==1 && $conf['enable_login']['p1']!="" && $conf['enable_login']['p1']>time())
			{
				echo "<h1>Login noch nicht offen</h1>
				Das Spiel startet am ".date("d.m.Y",$conf['enable_login']['p1'])." ab ".date("H:i",$conf['enable_login']['p1'])."!<br/><br/>
				<a href=\"".LOGINSERVER_URL."\">Hauptseite</a>";
				session_destroy();
				$s=Null;
			}
			// Zugriff von anderen als eigenem Server bzw Login-Server sperren
			elseif (!$referer_allow && $_SERVER["HTTP_REFERER"]!="")
			{
				echo "<h1>Falscher Referer</h1>
				Der Zugriff auf das Spiel ist nur anderen internen Seiten aus m&ouml;glich! Ein externes Verlinken direkt in das Game hinein ist nicht gestattet! Dein Referer: ".$_SERVER["HTTP_REFERER"]."<br/><br/>
				<a href=\"".LOGINSERVER_URL."\">Hauptseite</a>";
				session_destroy();
				$s=Null;
			}
			// Zugriff erlauben und Inhalt anzeigen
			else
			{
				// Zeit der letzten User-Aktion speichern
				dbquery("UPDATE users SET user_acttime='".time()."' WHERE user_id='".$s['user']['id']."';");
				dbquery ("UPDATE user_log SET log_acttime=".time()." WHERE log_user_id=".$s['user']['id']." AND log_session_key='".$s['key']."';");
				
				// ???
				$user=$s['user'];

				// Wenn eine ID angegeben wurde (Wechsel des Planeten) wird diese überprüft
				if (isset($_GET['planet_id']) && $_GET['planet_id']>0)
				{
					$cpid = $_GET['planet_id'];
				}
				elseif (isset($s['currentPlanetId']) && $s['currentPlanetId']>0)
				{
					$cpid = $s['currentPlanetId'];
				}
				else
				{
					$cpid = 0;
				}
					
				// Planetenklasse laden
				$planets = new Planets($cpid);

				// Daten in Session speichern
				$s['currentPlanetId']=$planets->getCurrentId();
				$c = $planets->getCurrentData();

				// Planet aktualisieren
				$updatedPages=array(
				"overview",
				"planetoverview",
				"economy",
				"specialists",
				"planetstats",
				"population",
				"havens",
				"market",
				"buildings",
				"research",
				"shipyard",
				"defense",
				"recycling");
				if (in_array($page,$updatedPages))
				{
					$c->update();
				}

				
				// Flottenupdate (Prüfen ob nicht bereits ein Flottenupdate läuft)
				//if($conf['updating_fleet']['v']==0)
				//{
	      //  $res = dbquery("
	      //  SELECT
	      //      *
	      //  FROM
	      //      ".$db_table['fleet']."
	      //  WHERE
	      //      fleet_landtime<".time()."
	      //      AND fleet_updating=0
	      //  ORDER BY
	      //      fleet_landtime ASC
	      //   ;"); 
	      //  if (mysql_num_rows($res) > 0)
	      //  {
	      //    require_once("inc/fleet_action.inc.php");
	      //    require_once("inc/fleet_update.inc.php");
	      //    while ($arr=mysql_fetch_array($res))
	      //    {
	      //    	update_fleet($arr,0);
	      //    }
	      //  }
				//}				
				//check_missiles();
				
				
				// Navigation laden
				require_once('inc/nav.inc.php');

				// Count Messages
				define('NEW_MESSAGES',check_new_messages($s["user"]["id"]));
				
				// Count users
				$ucres=dbquery('SELECT COUNT(user_id) FROM users;');
				$ucarr=mysql_fetch_row($ucres);
				
				// Count online users
				$gres=dbquery('SELECT COUNT(user_id) FROM users WHERE user_acttime>'.(time()-$conf['user_timeout']['v']).';');
				$garr=mysql_fetch_row($gres);
				
				// Count notes
				$res=dbquery("SELECT COUNT(note_id) FROM notepad WHERE note_user_id=".$s['user']['id'].";");
				$narr=mysql_fetch_row($res);

				// Create template object
				$tpl = new Smarty;
				$tpl->template_dir = SMARTY_TEMPLATE_DIR;
				$tpl->compile_dir = SMARTY_COMPILE_DIR;

				// Assign template variables
				$tpl->assign("messages",NEW_MESSAGES);
				$tpl->assign("blinkMessages",$s['user']['msg_blink']);
				$tpl->assign("buddys",check_buddys_online($s['user']['id']));
				$tpl->assign("fleetAttack",check_fleet_incomming($_SESSION[ROUNDID]['user']['id']));
				$tpl->assign("templateDir",CSS_STYLE);
				$tpl->assign("serverTime",date('H:i:s'));
				$tpl->assign("currentPlanetName","Planet");
				$tpl->assign("currentPlanetName",$planets->current->getString());
				$tpl->assign("planetList",$planets->getLinkList());						
				$tpl->assign("usersOnline",$garr[0]);
				$tpl->assign("usersTotal",$ucarr[0]);
				$tpl->assign("notes",$narr[0]);
				$tpl->assign("userPoints",nf($s["user"]["points"]));
				$tpl->assign("userNick",$s["user"]["nick"]);
				$tpl->assign("gameWidth",GAME_WIDTH);
				$tpl->assign("nextPlanetId",$planets->nextId);
				$tpl->assign("prevPlanetId",$planets->prevId);
				$tpl->assign("page",$page);
				$tpl->assign("topNav",$topnav);
				$tpl->assign("gameNav",$navmenu);
				$tpl->assign("selectField",$planets->getSelectField());		
				$tpl->assign("urlTeamspeak",$conf['url_teamspeak']['v']);
				$tpl->assign("urlRules",$conf['url_rules']['v']);
				$tpl->assign("urlForum","http://www.etoa.ch/forum");
				$tpl->assign("urlHelpcenter",HELPCENTER_PATH);
				$tpl->assign("chatString",CHAT_STRING);
								
				if ($s['user']['show_adds']==1 || FORCE_ADDS==1)
					$tpl->assign("adds",true);
				else
					$tpl->assign("adds",false);
				if ($s['user']['helpbox']==1)
					$tpl->assign("helpBox",true);
				else
					$tpl->assign("helpBox",false);
				if ($s['user']['notebox']==1)
					$tpl->assign("noteBox",true);
				else
					$tpl->assign("noteBox",false);
					
				// Display header		
				$tpl->display(getcwd()."/".CSS_STYLE."/header.tpl");
				
				// Include content
				require("inc/content.inc.php");
				
				$render_time = explode(' ',microtime());
				$rtime = $render_time[1]+$render_time[0]-$render_starttime;
				$tpl->assign("renderTime",round($rtime,3));
				
				// Display footer
				$tpl->display(getcwd()."/".CSS_STYLE."/footer.tpl");						
			}
			$_SESSION['lastpage']=$page;
			$_SESSION[ROUNDID] = $s;
			dbclose();
			$firstview = false; 
		?>
	</body>
</html>
