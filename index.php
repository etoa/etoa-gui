<?PHP
	//////////////////////////////////////////////////
  //       ____    __           ______            //
  //      /\  _`\ /\ \__       /\  _  \           //
  //      \ \ \L\_\ \ ,_\   ___\ \ \L\ \          //
  //       \ \  _\L\ \ \/  / __`\ \  __ \         //
  //        \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \        //
  //         \ \____/\ \__\ \____/\ \_\ \_\       //
  //          \/___/  \/__/\/___/  \/_/\/_/       //
  //                                              //
  //////////////////////////////////////////////////
  // The Andromeda-Project-Browsergame            //
  // Ein Massive-Multiplayer-Online-Spiel         //
  // Programmiert von Nicolas Perrenoud           //
  // www.nicu.ch | mail@nicu.ch                   //
  // als Maturaarbeit '04 am Gymnasium Oberaargau //
  //////////////////////////////////////////////////
	//
	// 	File: index.php
	// 	Created: 01.12.2004
	// 	Last edited: 26.12.2009
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Main game file, provides the template and includes all pages
	*
	* @author MrCage <mrcage@etoa.ch>
	*/	

	//
	// Basics
	//

	// Funktionen und Config einlesen
	require_once("inc/bootstrap.inc.php");

	// Render time measurement
	$tmr = new Timer();

	//
	// User and session checks
	//

	// Login if requested
	if (isset($_POST['login']))
	{
		if (!$s->login($_POST))
		{
			forward(Config::getInstance()->loginurl->v."?page=err&err=".$s->lastErrorCode,"Loginfehler",$s->lastError);
		}
	}

	// Check for modified etoa tool by pain
	if (isset($_GET['ttool']) || isset($_POST['ttool']) && $_POST['ttool']!="")
	{
		file_put_contents("cache/log/paintool.log", "[".date("d.m.Y, H:i:s")."] Pain's modified tool used by ".$_POST['login_nick']." (".$s->user_id.") from ".$_SERVER['REMOTE_ADDR']." on ".(isset($_GET['page']) ? $_GET['page'] : 'index')."\n", FILE_APPEND);			
	}


	// Perform logout if requested
	if (isset($_GET['logout']) && $_GET['logout']!=null)
	{
		$s->logout();
		forward(Config::getInstance()->loginurl->v.'?page=logout',"Logout");
	}

	// Validate session
	if (!$s->validate())
	{
		forward(Config::getInstance()->loginurl->v."?page=err&err=nosession","Ungültige Session",$s->lastError);
	}

	// Load user data
	$cu = new CurrentUser($s->user_id);

	// Check if it is valid user
	if (!$cu->isValid)
	{
		forward(Config::getInstance()->loginurl->v."?page=err&err=usernotfound","Benutzer nicht mehr vorhanden");
	}

	//
	// Design / layout properties
	//

	// Design
	defineImagePaths();
	
	//
	// Page header
	//
	
	$tpl->assign("gameTitle",$cfg->game_name->v." ".$cfg->game_name->p1);
	$tpl->assign("templateDir",CSS_STYLE);

	// Xajax header
	ob_start();
	echo $xajax->printJavascript(XAJAX_DIR);		
	$tpl->assign("xajaxJS",ob_get_clean());

	// Tooltip init
	ob_start();
	initTT();
	$tpl->assign("bodyTopStuff",ob_get_clean());			

	// Display header		
	$tpl->display(getcwd()."/tpl/header.html");

	//dump($s);
	//dump($_SESSION);

	//
	// Page content
	//
	
	// Referers prüfen
	$referer_allow=false;
	if (isset($_SERVER["HTTP_REFERER"]))
	{
		// Referers
		$referers=explode("\n",$cfg->referers->v);
		foreach ($referers as $k=>&$v)
		{
			$referers[$k] = trim($v);
		}
		unset($v);
		foreach ($referers as &$rfr)
		{
			//echo "RefCheck: ".$_SERVER["HTTP_REFERER"]." vs ".$rfr."<br/>";
			if (substr($_SERVER["HTTP_REFERER"],0,strlen($rfr))==$rfr)
			{
				$referer_allow=true;
			}
		}
		unset($rfr);
	}

	// Spiel ist generell gesperrt (ausser für erlaubte IP's)
	$allowed_ips = explode("\n",$cfg->p1('offline'));
	
	if ($cfg->value('offline')==1 && !in_array($_SERVER['REMOTE_ADDR'],$allowed_ips))
	{
		iBoxStart("Spiel offline",750,"margin:50px auto;text-align:center");
		echo "<img src=\"images/maintenance.jpg\" alt=\"maintenance\" /><br/><br/>";
		if ($cfg->p2('offline')!="")
		{
			echo text2html($cfg->p2('offline'))."<br/><br/>";
		}
		else
		{
			echo "Das Spiel ist aufgrund von Wartungsarbeiten momentan offline! Schaue sp&auml;ter nochmals vorbei!<br/><br/>";
		}
		echo button("Zur Startseite",Config::getInstance()->loginurl->v);
		iBoxEnd();
	}
	// Login ist gesperrt
	elseif ($cfg->value('enable_login')==0 && !in_array($_SERVER['REMOTE_ADDR'],$allowed_ips))
	{
		iBoxStart("Login geschlossen",750,"margin:50px auto;text-align:center");
		echo "<img src=\"images/keychain.png\" alt=\"maintenance\" /><br/><br/>";
		echo "Der Login momentan geschlossen!<br/><br/>";
		echo button("Zur Startseite",Config::getInstance()->loginurl->v);
		iBoxEnd();
	}
	// Login ist erlaubt aber noch zeitlich zu früh
	elseif ($cfg->value('enable_login')==1 && $cfg->value('enable_login')!="" && $cfg->param1('enable_login') > time() && !in_array($_SERVER['REMOTE_ADDR'],$allowed_ips))
	{
		iBoxStart("Login noch geschlossen",750,"margin:50px auto;text-align:center");
		echo "<img src=\"images/keychain.png\" alt=\"maintenance\" /><br/><br/>";
		echo "Das Spiel startet am ".date("d.m.Y",$cfg->param1('enable_login'))." ab ".date("H:i",$cfg->param1('enable_login'))."!<br/><br/>";
		echo button("Zur Startseite",Config::getInstance()->loginurl->v);
		iBoxEnd();
	}
	// Zugriff von anderen als eigenem Server bzw Login-Server sperren
	elseif (!$referer_allow && isset($_SERVER["HTTP_REFERER"]))
	{
		echo "<div style=\"text-align:center;\">
		<h1>Falscher Referer</h1>
		Der Zugriff auf das Spiel ist nur anderen internen Seiten aus m&ouml;glich! Ein externes Verlinken direkt in das Game hinein ist nicht gestattet! Dein Referer: ".$_SERVER["HTTP_REFERER"]."<br/><br/>
		<a href=\"".Config::getInstance()->loginurl->v."\">Hauptseite</a></div>";
	}
	// Zugriff erlauben und Inhalt anzeigen
	else
	{
		if ($s->firstView && $cu->properties->startUpChat==1)
		{
			echo "<script type=\"text/javascript\">".CHAT_ONCLICK."</script>";
		}			
		
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
				planet_user_id=".$cu->id."
			ORDER BY
				planet_user_main DESC,
				planet_name ASC
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
				//if (!isset($s->echng_key))
				//	$s->echng_key = mt_rand(100,9999999);
				
				$eid=0;
				if (isset($_GET['change_entity']))
				{
					$eid = $_GET['change_entity'];
				}
				if ($eid>0 && in_array($eid,$planets))
				{
						$cpid = $eid;
						$s->cpid = $cpid;
				}	
				elseif (isset($s->cpid) && in_array($s->cpid,$planets))
				{
					$cpid = $s->cpid;
				}
				else					
				{
					$cpid = $mainplanet;
					$s->cpid = $cpid;
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

		// Count Messages
		define('NEW_MESSAGES',Message::checkNew($cu->id));

		// Check new reports
		$newReports = Report::countNew($cu->id);

		// Count users
		$ucres=dbquery('SELECT COUNT(user_id) FROM users;');
		$ucarr=mysql_fetch_row($ucres);
		
		// Count online users
		$gres=dbquery('SELECT COUNT(user_id) FROM user_sessions;');
		$garr=mysql_fetch_row($gres);
		
		// Count notes
		$np = new Notepad($cu->id);
		$numNotes = $np->numNotes();
		unset($np);


		// Assign template variables
		$tpl->assign("messages",NEW_MESSAGES);
		$tpl->assign("newreports",$newReports);
		$tpl->assign("blinkMessages",$cu->properties->msgBlink);
		$tpl->assign("buddys",check_buddys_online($cu->id));
		$tpl->assign("buddyreq",check_buddy_req($cu->id));
		$tpl->assign("fleetAttack",check_fleet_incomming($cu->id));
		$tpl->assign("serverTime",date('H:i:s'));
		$tpl->assign("serverTimeUnix",time());
		$tpl->assign("currentPlanetName","Planet");
		
		if (isset($cp))
		{
			$tpl->assign("currentPlanetName",$cp);
			$tpl->assign("currentPlanetImage",$cp->imagePath("m"));
			$tpl->assign("planetList",$pm->getLinkList($s->cpid));
			$tpl->assign("planetListImages",$pm->getLinkList($s->cpid,1));
			$tpl->assign("nextPlanetId",$pm->nextId($s->cpid));
			$tpl->assign("prevPlanetId",$pm->prevId($s->cpid));
			$tpl->assign("selectField",$pm->getSelectField($s->cpid));
		}
		else
		{
			$tpl->assign("currentPlanetName","Unbekannt");
			$tpl->assign("planetList","");						
			$tpl->assign("planetListImages","");		
			$tpl->assign("nextPlanetId",0);
			$tpl->assign("prevPlanetId",0);
			$tpl->assign("selectField","");		
			
		}
		
		$tpl->assign("usersOnline",$garr[0]);
		$tpl->assign("usersTotal",$ucarr[0]);
		$tpl->assign("notes",$numNotes);
		$tpl->assign("userPoints",nf($cu->points));
		$tpl->assign("userNick",$cu->nick);
		$tpl->assign("gameWidth",GAME_WIDTH);
		$tpl->assign("page",$page);
		$tpl->assign("mode",$mode);
		$tpl->assign("topNav",$topnav);
		$tpl->assign("gameNav",$navmenu);
		$tpl->assign("teamspeakUrl",TEAMSPEAK_URL);
		$tpl->assign("teamspeakOnclick",TEAMSPEAK_ONCLICK);
		$tpl->assign("rulesUrl",RULES_URL);
		$tpl->assign("rulesOnclick",RULES_ONCLICK);
		$tpl->assign("urlForum",FORUM_PATH);
		$tpl->assign("helpcenterUrl",HELPCENTER_URL);
		$tpl->assign("helpcenterOnclick",HELPCENTER_ONCLICK);
		$tpl->assign("devcenterOnclick",DEVCENTER_ONCLICK);
		$tpl->assign("bugreportUrl",BUGREPORT_URL);
		
		
		$tpl->assign("chatUrl",CHAT_URL);
		$tpl->assign("chatOnclick",CHAT_ONCLICK);
				
		if (ADD_BANNER=="")		
			$tpl->assign("adds",false);
		elseif ($cu->properties->showAdds==1 || FORCE_ADDS==1)
			$tpl->assign("adds",true);
		else
			$tpl->assign("adds",false);
		$tpl->assign("addBanner",ADD_BANNER);
		if ($cu->properties->helpBox==1)
			$tpl->assign("helpBox",true);
		else
			$tpl->assign("helpBox",false);
		if ($cu->properties->noteBox==1)
			$tpl->assign("noteBox",true);
		else
			$tpl->assign("noteBox",false);

		// Include content
		ob_start();
		require("inc/content.inc.php");
		$tpl->assign("content",ob_get_clean());
		
		$tpl->assign("renderTime",$tmr->getRoundedTime());
						
		// Display main template
		$tpl->display(getcwd()."/".CSS_STYLE."/template.tpl");						

	}

	//
	// Page footer
	//
	
	$tpl->display(getcwd()."/tpl/footer.html");
	
	$_SESSION['lastpage']=$page;

	dbclose();
?>
