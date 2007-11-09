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
	// 	Dateiname: index.php	
	// 	Topic: Startseite Gameserver
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.03.2006
	// 	Kommentar: 	
	//

	// Session-Cookie setzen
	ini_set('arg_separator.output',  '&amp;');
	session_start();  

	// Globale Variabeln definieren
	$_GET = $HTTP_GET_VARS;
	$_POST = $HTTP_POST_VARS;

	// Funktionen und Config einlesen
	include("conf.inc.php");
	include("functions.php");

	if ($_GET['logout']!=null)
	{
		session_destroy();
		unset($sc);
		$_GET['page']="logout";
	}

	if (!isset($sc) && $_POST['login_submit']==null && $_GET['indexpage']=="")
	{
		header("Location: ".LOGINSERVER_URL); 
		exit;    
	}

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 //DE"                    
        "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">               
		<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="de">   
			<head>
				<link rel="stylesheet" type="text/css" href="style.css" />

				<meta name="author" content="Nicolas Perrenoud" />
				<meta name="keywords" content="Escape to Andromeda, Browsergame, Strategie, Simulation, Andromeda, MMPOG, RPG" />
				<meta name="date" content="2004-10-01" />
				<meta name="robots" content="follow" />
				<meta name="generator" content="Ultra Edit 10.0" />
				
				<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
				<meta http-equiv="Content-Script-Type" content="text/javascript" />
				<meta http-equiv="Content-Style-Type" content="text/css" />
				<meta http-equiv="content-language" content="de" />
				<meta name="revisit-after" content="15 days" />
				
				<script src="scripts.js" type="text/javascript"></script>
	<?PHP	

	// Mit der DB verbinden und Config-Werte laden
	dbconnect();
	$conf = get_all_config();
	
	// Zufallsgenerator initialisieren
	mt_srand(time());	
	
	// Update am Laufen
	define(UPDATE_RUNNING,$conf['update_running']['v']);
	
	?>
		<title><? echo $conf['game_name']['v']." - Version ".$conf['game_name']['p1']; ?></title>
	</head>	
	<?PHP			

			
			if ($_GET['indexpage']!="")
			{
				$indexpage=$_GET['indexpage'];
				echo "<body class=\"index\">";
				if (!@include("index/".$_GET['indexpage'].".php"))
					echo "Die Datei <b>".$_GET['indexpage']."</b> kann nicht gefunden werden!<br/><br/><a href=\"?\">Zur Startseite</a>";
				echo "</body>";
			}			
			else
			{
				if ($_POST['login_submit']!="")
				{
					if ($_POST['login_nick']!="" && !stristr($_POST['login_nick'],"'") && !stristr($_POST['login_pw'],"'"))
					{
						$o_time = time()+$conf['user_timeout']['v'];
						$res = dbquery("SELECT * FROM ".$db_table['users']." WHERE LCASE(user_nick)='".strtolower($_POST['login_nick'])."' AND user_password='".md5($_POST['login_pw'])."';");
						$arr = mysql_fetch_array($res);      	
						
						
						if (mysql_num_rows($res)>0)
						{
							$_SESSION[ROUNDID]['user']['id']=$arr['user_id'];
							$_SESSION[ROUNDID]['user']['nick']=$arr['user_nick'];
							$_SESSION[ROUNDID]['user']['email']=$arr['user_email'];
							$_SESSION[ROUNDID]['user']['last_online']=$arr['user_last_online'];
							$_SESSION[ROUNDID]['user']['race_id']=$arr['user_race_id'];
							$_SESSION[ROUNDID]['user']['points']=$arr['user_points'];
							$_SESSION[ROUNDID]['user']['points_updated']=$arr['user_points_updated'];
							$_SESSION[ROUNDID]['user']['alliance_id']=$arr['user_alliance_id'];
							$_SESSION[ROUNDID]['user']['image_url']=$arr['user_image_url'];
							$_SESSION[ROUNDID]['user']['image_ext']=$arr['user_image_ext'];
							$_SESSION[ROUNDID]['user']['blocked_from']=$arr['user_blocked_from'];
							$_SESSION[ROUNDID]['user']['blocked_to']=$arr['user_blocked_to'];
							$_SESSION[ROUNDID]['user']['hmode_from']=$arr['user_hmode_from'];
							$_SESSION[ROUNDID]['user']['hmode_to']=$arr['user_hmode_to'];
							if ($arr['user_alliance_application']!="")
								$_SESSION[ROUNDID]['user']['alliance_application']=1;
							else
								$_SESSION[ROUNDID]['user']['alliance_application']=0;
							$_SESSION[ROUNDID]['user']['ip']=$_SERVER['REMOTE_ADDR'];
							session_register('sc');
							dbquery("UPDATE ".$db_table['users']." SET user_last_online='".time()."', user_ip='".$_SERVER['REMOTE_ADDR']."' WHERE user_id='".$arr['user_id']."';");
						}
						else
						{
							$scr['round_key']=Null;
							echo "<script>document.location='".LOGINSERVER_URL."?page=err&err=pass'</script>";				
						}
					}
					else
					{
						$scr['round_key']=Null;
						echo "<script>document.location='".LOGINSERVER_URL."?page=err&err=name'</script>";				
					}
				}
				else
				{
					$arr=mysql_fetch_array($res = dbquery("SELECT * FROM ".$db_table['users']." WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."';"));
					if ($arr['user_last_online']+$conf['user_timeout']['v'] < time())
					{
						session_destroy();
						echo "<script>document.location='".LOGINSERVER_URL."?page=err&err=timeout'</script>";				
					}
				}		
		
				if (session_is_registered("sc"))
				{
					// DEFINITIONEN //
					
					// Layout
					define("TBL_SPACING",$conf['general_table_offset']['v']);
					define("TBL_PADDING",$conf['general_table_offset']['p1']);
					
					// Startwerte
					
					define("USR_START_METAL",$conf['user_start_metal']['v']);
					define("USR_START_CRYSTAL",$conf['user_start_crystal']['v']);
					define("USR_START_PLASTIC",$conf['user_start_plastic']['v']);
					define("USR_START_FUEL",$conf['user_start_fuel']['v']);
					define("USR_START_FOOD",$conf['user_start_food']['v']);
					define("USR_START_PEOPLE",$conf['user_start_people']['v']);
					define("USR_PLANET_NAME",$conf['user_planet_name']['v']);
					
					// Technologien
										
					define("SPY_TECH_ID",7);
					define("SPY_TECH_SHOW_ATTITUDE",1);
					define("SPY_TECH_SHOW_NUM",3);
					define("SPY_TECH_SHOW_SHIPS",5);
					define("SPY_TECH_SHOW_NUMSHIPS",7);
					define("SPY_TECH_SHOW_ACTION",9);
					define("RECYC_TECH_ID",12);
					define("BUILD_TECH_ID",14);
					
					// Geb&auml;ude
					
					define("BUILD_BUILDING_ID",6);
					define("SHIP_BUILDING_ID",9);
					define("DEF_BUILDING_ID",10);

					// Anf&auml;ngerschutz
			
					define("USER_ATTACK_MIN_POINTS",$conf['user_attack_min_points']['v']);
					define("USER_ATTACK_PERCENTAGE",$conf['user_attack_percentage']['v']);
					
					// Flotten

					define("INVADE_POSSIBILITY",$conf['invade_possibility']['v']);
					define("INVADE_SHIP_DESTROY",$conf['invade_ship_destroy']['v']);
		
					// Bilder

					define("IMAGE_TECHNOLOGY_DIR","technologies");
					define("IMAGE_SHIP_DIR","ships");
					define("IMAGE_PLANET_DIR","planets");
					define("IMAGE_BUILDING_DIR","buildings");				
					define("IMAGE_DEF_DIR","defense");

					define(IMAGEPACK_DIRECTORY,"images/themes");

					if ($_SESSION[ROUNDID]['user']['image_url']!="" && $_SESSION[ROUNDID]['user']['image_ext']!="")
					{
						define("IMAGE_PATH",$_SESSION[ROUNDID]['user']['image_url']);
						define("IMAGE_EXT",$_SESSION[ROUNDID]['user']['image_ext']);
					}
					else
					{
						define("IMAGE_PATH",DEFAULT_IMAGE_PATH);
						define("IMAGE_EXT","jpg");
					}
					
					// Kampfsystem
					
					define("SHIP_WAR_MSG_CAT_ID",3);																	// Kategorie der Kriegsnachrichten
					define("BATTLE_ROUNDS",5); 																				// Anzahl Runden
					define("STRUCTURE_TECH_ID",9);																		// ID der Strukturtechnik
					define("SHIELD_TECH_ID",10);																			// ID der Schildtechnik
					define("WEAPON_TECH_ID",8);																				// ID der Waffentechnik
					define("DEF_RESTORE_PERCENT",$conf['def_restore_percent']['v']);	// Wiederaufbau der Def
					define("DEF_WF_PERCENT",$conf['def_wf_percent']['v']);						// Def ins Tr&uuml;mmerfeld
					define("SHIP_WF_PERCENT",$conf['ship_wf_percent']['v']);					// Def ins Tr&uuml;mmerfeld
					
					// Nachrichten
	
					define("SHIP_SPY_MSG_CAT_ID",2);  
					define("SHIP_WAR_MSG_CAT_ID",3);  
					define("SHIP_MONITOR_MSG_CAT_ID",4);
					define("SHIP_MISC_MSG_CAT_ID",5);	
	
					// Sonstiges
					
					define("RECYC_MAX_PAYBACK",0.8);
					define("STD_FIELDS",intval($conf['def_store_capacity']['v']));
					define("SHIPDEFBUILD_CANCEL_TIME",$conf['shipdefbuild_cancel_time']['v']);
					define("PEOPLE_FOOD_USE",$conf['people_food_use']['v']);
					define("COLLECT_FUEL_MAX_AMOUNT",10000);

					// START SKRIPT //				

					$allowed_ips = explode("\n",$conf['update_running']['p1']);
					if (UPDATE_RUNNING==1 && !in_array($_SERVER['REMOTE_ADDR'],$allowed_ips))
					{
						echo "Das Spiel ist kurzzeitig offline, weil gerade ein Update hochgeladen wird! Schau in ein paar Minuten nochmals vorbei oder besuch das <a href=\"http://etoa.ch/forum\">Forum</a>!";
						session_destroy();
					}
					elseif ($conf['enable_login']['v']==0)				
					{
						echo "<p>Man kann sich noch nicht einloggen!</p>";
						echo "<br/><br/><a href=\"index.php\" target=\"_top\">Zur Startseite</a>";
						session_destroy();
					}
					elseif ($conf['enable_login']['v']==1 && $conf['enable_login']['p1']!="" && $conf['enable_login']['p1']>time())				
					{
						echo "<p>Das Spiel startet am ".date("d.m.Y",$conf['enable_login']['p1'])." ab ".date("H:i",$conf['enable_login']['p1'])."!</p>";
						echo "<br/><br/><a href=\"index.php\" target=\"_top\">Zur Startseite</a>";
						session_destroy();
					}
					elseif (substr($_SERVER["HTTP_REFERER"],0,strlen(GAMESERVER_REFERER))!=GAMESERVER_REFERER && substr($_SERVER["HTTP_REFERER"],0,strlen(LOGINSERVER_REFERER))!=LOGINSERVER_REFERER && substr($_SERVER["HTTP_REFERER"],0,strlen(LOGINSERVER_REFERER2))!=LOGINSERVER_REFERER2 && $_SERVER["HTTP_REFERER"]!="")
					{
						echo "<p><b>Achtung:</b> Aufgrund von Sicherheitsproblemen ist der Zugriff auf die InGame-Seiten nur noch von anderen EtoA-Seiten aus m&ouml;glich! Ein externes Verlinken direkt in das Game hinein ist nicht gestattet! Dein Referer: ".$_SERVER["HTTP_REFERER"]."</p>";
						echo "<br/><br/><a href=\"index.php\" target=\"_top\">Zur Startseite</a>";
						session_destroy();
					}	
					else
					{
						echo "<body class=\"game\">";
					
						echo "<table id=\"gamelayout\">";
						echo "<tr><td id=\"gametitle\" colspan=\"2\">";
						include("top.include.php");
						echo "</td></tr>";
						echo "<tr><td id=\"gameleft\">";
						include("left.include.php");
						echo "</td>";
						echo "<td id=\"gamecontent\">";
						include("content.include.php");
						echo "</td></tr>";
						//echo "<div id=\"gameright\">";
						//include("right.include.php");
						//echo "</div>";
						echo "</body>";
					}
				}
				else
				{
					echo "<script>document.location='?page=err&err=session'</script>";
					echo "Es trat ein Fehler auf! Klicke <a href=\"?page=err&err=session\">hier</a> um die Fehlermeldung anzuzeigen!";						
				}
			}	
		dbclose();
		echo "</html>";

?>
