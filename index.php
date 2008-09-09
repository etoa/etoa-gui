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
	require_once("functions.php");
	if (!@include_once("conf.inc.php"))
	{
		require("inc/install.inc.php");
		exit();
	}

	// Renderzeit-Start festlegen
	$render_time = explode(" ",microtime());
	$render_starttime=$render_time[1]+$render_time[0];


	// Mit der DB verbinden und Config-Werte laden
	dbconnect();

	$cfg = Config::getInstance();
	$conf = $cfg->getArray();

	require_once("def.inc.php");
	
	// Load smarty template engine
	require(SMARTY_DIR.'/Smarty.class.php');
	
	// Firstview
	$firstview = false;
	
	// Session-Array
	if (isset($_SESSION[ROUNDID]))
	{
		$s = $_SESSION[ROUNDID];
	}
	else
	{
		$s = array();
	}
	
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

	// Session prüfen
	if (!isset($s['user_id']) || $s['user_id']==0 || $s['user_id']=='')
	{
		session_destroy();
		header("Location: ".LOGINSERVER_URL."?page=err&err=nosession");
		echo "<h1>Session nicht mehr vorhanden!</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=nosession\">hier</a> klicken...";
		exit;
	}
	
	// Benutzerdaten laden
	$cu = new User($s['user_id']);

	// Check if is valid user
	if (!$cu->isValid())
	{
		session_destroy();
		header("Location: ".LOGINSERVER_URL."?page=err&err=usernotfound");
		echo "<h1>Benutzer nicht mehr vorhanden!</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=usernotfound\">hier</a> klicken...";
		exit;
	}
	
	// Check timeout	
	if ($cu->isTimeout())
	{
		session_destroy();
		header("Location: ".LOGINSERVER_URL."?page=err&err=timeout");
		echo "<h1>Timeout ".$cfg->value("user_timeout")."s</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=timeout\">hier</a> klicken...";
		exit;
	}

	// Session prüfen
	$session_valid=false;
	if ($s['key']!="")
	{
		// Valid browser values
		if (substr($s['key'],64,32)==md5(ROUNDID) 
		&& substr($s['key'],96,32)==md5($_SERVER['REMOTE_ADDR']) 
		&& substr($s['key'],128,32)==md5($_SERVER['HTTP_USER_AGENT']) 
		&& substr($s['key'],160)==session_id() )
		{
			// Valid user valies
			if ($cu->lt=substr($s['key'],0,32) && 
			$cu->uid==substr($s['key'],32,32) && 
			$cu->sk==$s['key'])
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

	//
	// Misc settings
	//

	// Layout-/Grafikdefinitionen
	if ($cu->css_style!='')
	{
		define('CSS_STYLE',DESIGN_DIRECTORY."/".$cu->css_style);
	}
	else
	{
		define('CSS_STYLE',DESIGN_DIRECTORY."/".$cfg->value('default_css_style'));
	}
	define('GAME_WIDTH',$cu->game_width);
	if ($cu->image_url!='' && $cu->image_ext!='')
	{
		define('IMAGE_PATH',$cu->image_url);
		define('IMAGE_EXT',$cu->image_ext);
	}
	else
	{
		define("IMAGE_PATH",$cfg->value('default_image_path'));
		define("IMAGE_EXT","gif");
	}
	
	// Check sitting
	require_once('inc/sitting.inc.php');

	// Set default content page
	$page = (isset($_GET['page']) && $_GET['page']!="") ? $_GET['page'] : DEFAULT_PAGE;

	// Initialize XAJAX and load functions
	require_once("inc/xajax.inc.php");

	// Show HTML Header
	Html::header();

	// Show body
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
			if (isset($_SERVER["HTTP_REFERER"]))
			{
				foreach ($referers as $rfr)
				{
					if (substr($_SERVER["HTTP_REFERER"],0,strlen($rfr))==$rfr)
					{
						$referer_allow=true;
					}
				}
			} 				

			// Spiel ist generell gesperrt (ausser für erlaubte IP's)
			$allowed_ips = explode("\n",$cfg->value('offline'));
			if ($cfg->value('offline')==1 && !in_array($_SERVER['REMOTE_ADDR'],$allowed_ips))
			{
				echo "<h1>Spiel offline</h1>
				<div style=\"width:900px;margin:10px auto;text-align:center;background:black;border:1px solid #ddd;\">
				<img src=\"images/maintenance.jpg\" alt=\"maintenance\" /><br/><br/>";
				if ($cfg->p2('offline')!="")
				{
					echo text2html($cfg->p2('offline'))."<br/><br/>";
				}
				else
				{
					echo "Das Spiel ist aufgrund von Wartungsarbeiten momentan offline! Schaue sp&auml;ter nochmals vorbei!<br/><br/>";
				}
				echo "<a href=\"".LOGINSERVER_URL."\">Zur Startseite</a><br/><br/></div>";
				session_destroy();
				$s=Null;
			}
			// Login ist gesperrt
			elseif ($cfg->value('enable_login')==0)
			{
				echo "<h1>Login deaktiviert</h1>
				Der Login nocht nicht aktiviert!<br/><br/>
				<a href=\"".LOGINSERVER_URL."\">Hauptseite</a>";
				session_destroy();
				$s=Null;
			}
			// Login ist erlaubt aber noch zeitlich zu früh
			elseif ($cfg->value('enable_login')==1 && $cfg->value('enable_login')!="" && $cfg->param1('enable_login') > time())
			{
				echo "<h1>Login noch nicht offen</h1>
				Das Spiel startet am ".date("d.m.Y",$cfg->param1('enable_login'))." ab ".date("H:i",$cfg->param1('enable_login'))."!<br/><br/>
				<a href=\"".LOGINSERVER_URL."\">Hauptseite</a>";
				session_destroy();
				$s=Null;
			}
			// Zugriff von anderen als eigenem Server bzw Login-Server sperren
			elseif (!$referer_allow && isset($_SERVER["HTTP_REFERER"]))
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
				dbquery("UPDATE users SET user_acttime='".time()."' WHERE user_id='".$s['user_id']."';");
				dbquery ("UPDATE user_sessionlog SET log_acttime=".time()." WHERE log_user_id=".$s['user_id']." AND log_session_key='".$s['key']."';");
				
				if ($cu->isSetup())
				{
					//
					// Load current planet
					//
					$res = dbquery("
					SELECT
						id,
						planet_user_main
					FROM	
						planets
					WHERE
						planet_user_id=".$cu->id()."
					");
					if (mysql_num_rows($res)>0)
					{
						$planets = array();
						$mainplanet=0;
						while ($arr=mysql_fetch_row($res))
						{
							$planets[] = $arr[0];
							if ($arr[1]==1)
							{
								$mainplanet = $arr[0];
							}							
						}
						// Todo: check if mainplanet is still 0
						
						// Wenn eine ID angegeben wurde (Wechsel des Planeten) wird diese überprüft
						if (isset($_GET['planet_id']) && $_GET['planet_id']>0 && in_array($_GET['planet_id'],$planets))
						{
							$cpid = $_GET['planet_id'];
							$s['cpid'] = $cpid;
						}	
						elseif (isset($s['cpid']) && in_array($s['cpid'],$planets))
						{
							$cpid = $s['cpid'];
						}
						else					
						{
							$cpid = $mainplanet;
							$s['cpid'] = $cpid;
						}						
						
						$cp = new Planet($cpid);
						
						$pm = new PlanetManager($planets);
					}
					else
					{
						$cu->setNotSetup($planets);
						unset($planets);
					}
				}

				// Navigation laden
				require_once('inc/nav.inc.php');
				require_once('inc/adds.inc.php');

				// Count Messages
				define('NEW_MESSAGES',Message::checkNew($cu->id()));
				
				// Count users
				$ucres=dbquery('SELECT COUNT(user_id) FROM users;');
				$ucarr=mysql_fetch_row($ucres);
				
				// Count online users
				$gres=dbquery('SELECT COUNT(user_id) FROM users WHERE user_acttime>'.(time()-$cfg->value('user_timeout')).';');
				$garr=mysql_fetch_row($gres);
				
				// Count notes
				$np = new Notepad($cu->id());
				$numNotes = $np->numNotes();
				unset($np);

				// Create template object
				$tpl = new Smarty;
				$tpl->template_dir = SMARTY_TEMPLATE_DIR;
				$tpl->compile_dir = SMARTY_COMPILE_DIR;

				// Assign template variables
				$tpl->assign("messages",NEW_MESSAGES);
				$tpl->assign("blinkMessages",$cu->msg_blink);
				$tpl->assign("buddys",check_buddys_online($s['user_id']));
				$tpl->assign("fleetAttack",check_fleet_incomming($cu->id()));
				$tpl->assign("templateDir",CSS_STYLE);
				$tpl->assign("serverTime",date('H:i:s'));
				$tpl->assign("currentPlanetName","Planet");
				
				if (isset($cp))
				{
					$tpl->assign("currentPlanetName",$cp);
					$tpl->assign("planetList",$pm->getLinkList());		
					$tpl->assign("nextPlanetId",$pm->nextId());
					$tpl->assign("prevPlanetId",$pm->prevId());
					$tpl->assign("selectField",$pm->getSelectField());
				}
				else
				{
					$tpl->assign("currentPlanetName","Unbekannt");
					$tpl->assign("planetList","");						
					$tpl->assign("nextPlanetId",0);
					$tpl->assign("prevPlanetId",0);
					$tpl->assign("selectField","");		
					
				}
				$tpl->assign("usersOnline",$garr[0]);
				$tpl->assign("usersTotal",$ucarr[0]);
				$tpl->assign("notes",$numNotes);
				$tpl->assign("userPoints",nf($cu->points));
				$tpl->assign("userNick",$cu->nick());
				$tpl->assign("gameWidth",GAME_WIDTH);
				$tpl->assign("page",$page);
				$tpl->assign("topNav",$topnav);
				$tpl->assign("gameNav",$navmenu);
				$tpl->assign("teamspeakUrl",TEAMSPEAK_URL);
				$tpl->assign("teamspeakOnclick",TEAMSPEAK_ONCLICK);
				$tpl->assign("rulesUrl",RULES_URL);
				$tpl->assign("rulesOnclick",RULES_ONCLICK);
				$tpl->assign("urlForum",FORUM_PATH);
				$tpl->assign("helpcenterUrl",HELPCENTER_URL);
				$tpl->assign("helpcenterOnclick",HELPCENTER_ONCLICK);
				$tpl->assign("chatUrl",CHAT_URL);
				$tpl->assign("chatOnclick",CHAT_ONCLICK);
						
				if (ADD_BANNER=="")		
					$tpl->assign("adds",false);
				elseif ($cu->show_adds==1 || FORCE_ADDS==1)
					$tpl->assign("adds",true);
				else
					$tpl->assign("adds",false);
				$tpl->assign("addBanner",ADD_BANNER);
				if ($cu->helpbox==1)
					$tpl->assign("helpBox",true);
				else
					$tpl->assign("helpBox",false);
				if ($cu->notebox==1)
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
				
				//echo $query_counter;
				// Display footer
				$tpl->display(getcwd()."/".CSS_STYLE."/footer.tpl");						
			}
			
			
			$_SESSION['lastpage']=$page;
			$_SESSION[ROUNDID] = $s;
			dbclose();
			$firstview = false; 

	Html::footer();
?>
