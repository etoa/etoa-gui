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
	// 	Topic: Admin Index
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 23.04.2006
	// 	Kommentar: 	Layout und generelle Definitionen f� Admin-Modus
	//

	// Seitenwahl zuweisen
	$page = isset($_GET['page']) ? $_GET['page'] : 'home';
	$sub = isset($_GET['sub']) ? $_GET['sub'] : '';

	// Renderzeit-Start festlegen
	$render_time = explode(" ",microtime());
	$render_starttime=$render_time[1]+$render_time[0];

	// Navigation laden
	require_once('nav.php');

	// Session-Cookie setzen
	ini_set('arg_separator.output',  '&amp;');
	session_start();

	// Funktionen und Config einlesen
	if (!@include_once("../conf.inc.php")) die("conf.inc.php does not exists, please read INSTALL for how to create this file!");
	require("../functions.php");
	require("../inc/fleet_action.inc.php");
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
	define('USER_TABLE_NAME',$db_table['admin_users']);

	define('URL_SEARCH_STRING', "page=$page&amp;sub=$sub&amp;tmp=1");
	define('URL_SEARCH_STRING2', "page=$page");
	define('URL_SEARCH_STRING3', "page=$page");

	define('DATE_FORMAT',$conf['admin_dateformat']['v']);
	define('TIMEOUT',$conf['admin_timeout']['v']);

	define('HTPASSWD_COMMAND',$conf['htaccess']['v']);
	define('HTPASSWD_FILE',$conf['htaccess']['p2']);
	define('HTPASSWD_USER',$conf['admin_htaccess']['p1']);

	// User-Farben
	define('USER_COLOR_DEFAULT',$conf['color_default']['v']);
	define('USER_COLOR_BANNED',$conf['color_banned']['v']);
	define('USER_COLOR_INACTIVE',$conf['color_inactive']['v']);
	define('USER_COLOR_HOLIDAY',$conf['color_umod']['v']);
	define('USER_COLOR_FRIEND',$conf['color_friend']['v']);
	define('USER_COLOR_ENEMY',$conf['color_enemy']['v']);
	define('USER_COLOR_DELETED','#09f');

	define('USER_BLOCKED_DEFAULT_TIME',3600*24*$conf['user_ban_min_length']['v']);	// Standardsperrzeit
	define('USER_HMODE_DEFAULT_TIME',3600*24*$conf['user_umod_min_length']['v']);	// Standardurlaubszeit

	define('ADMIN_FILESHARING_DIR',"cache/admin");

	// XAJAX
	include("inc/xajax_admin.inc.php");


	// Release update lock
	if (isset($_GET['releaseupdate']) && $_GET['releaseupdate']==1)
	{
		dbquery("UPDATE config SET config_value=0 WHERE config_name='updating';");
	}
	
	// Release fleet update lock  
	if (isset($_GET['releasefleetupdate']) && $_GET['releasefleetupdate']==1)
	{
		dbquery("UPDATE config SET config_value=0 WHERE config_name='updating_fleet';");
	}
	
	// Activate update system
	if (isset($_GET['activateupdate']) && $_GET['activateupdate']==1)
	{
		dbquery("UPDATE config SET config_value=1 WHERE config_name='update_enabled';");
	}

	// Zufallsgenerator initialisieren
	mt_srand(time());
?>

<?PHP	echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">	
	<head>
		<title><? echo $conf['game_name']['v']." ".$conf['game_name']['p1']." Administration - ".GAMEROUND_NAME;?></title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" href="../general.css" type="text/css" />

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
			// Check Login
			include("inc/admin_login.inc.php");
			
			if ($login_successfull)
			{
				// Define s as the current session variable
				$s = $_SESSION[SESSION_NAME];
				

				// Admin-Gruppen laden				
				$admingroup=array();
				$gres=dbquery("SELECT * FROM ".$db_table['admin_groups']." ORDER BY group_level DESC;");
				while ($garr=mysql_fetch_array($gres))
				{
					$admingroup[$garr['group_id']] =$garr['group_name'];
					$adminlevel[$garr['group_level']] =$garr['group_name'];
				}
				?>

				<!-- Stuff for DHTML Tipps -->
				<div id="Migoicons" style="visibility:hidden;position:absolute;z-index:1000;top:0;border:none"></div>
				<script  type="text/javascript">
					stl=["white","##222255","","","",,"white","#606578","","","",,,,2,"#222255",2,,,,,"",,,,]
					var TipId="Migoicons"
					var FiltersEnabled = 1
					mig_clay()
				</script>

				<table id="layoutbox">
					<tr>
						<td id="topbar" colspan="3">
							<?PHP
								foreach ($topnav as $title=> $data)
								{
									echo "<a href=\"".$data['url']."\" target=\"_blank\">$title</a> | ";
								}
							?>
							<a href="?logout=1" style="color:#f90;">Logout</a>
						</td>
					</tr>
					<tr>
						<td id="logo">&nbsp;</td>
						<td id="banner" colspan="2"><?PHP echo GAMEROUND_NAME;?></td>
					</tr>
					<tr>
						<td id="menu1">
							<?php
								//
								// Linke Navigation anzeigen
								//
								foreach ($navmenu as $cat=> $item)
								{
									$nitem = array_values($item);
									echo "<a href=\"?page=".$nitem[0]['page']."\" class=\"menu1Title\">$cat</a>";
									if ($nitem[0]['page']==$page)
									{
										foreach ($item as $title=> $data)
										{
											if ($title=="bar")
											{
												echo "<hr noshade=\"noshade\" size=\"1\" style=\"background:#fff;margin:0px 20px 0px 20px;\" />";
											}
											else
											{
												if ($data['level']<=$_SESSION[SESSION_NAME]['group_level'])
												{
													if ($data['sub']!="")
													{
														echo "<a href=\"?page=".$data['page']."&amp;sub=".$data['sub']."\" class=\"menu1Item\" >";
														if ($page==$data['page'] && $sub==$data['sub'])
															echo "<b>&gt;</b> ";
														echo "$title </a>";
													}
													else
													{
														echo "<a href=\"?page=".$data['page']."\" class=\"menu1Item\" >";
														if ($page==$data['page'] && $sub=="")
															echo "<b>&gt;</b> ";
														echo "$title</a>";
													}
												}
											}
										}
									}
								}

								echo "<br/><form action=\"?page=user&amp;action=search\" method=\"post\">
										<input type=\"text\" name=\"user_nick_search\" size=\"10\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick_search','user_search_box1');\"/><br><div class=\"citybox\" id=\"user_search_box1\">&nbsp;</div>
										<input type=\"hidden\" name=\"qmode[user_nick_search]\" value=\"LIKE '%\" />
										<input type=\"submit\" name=\"user_search\" value=\"Usersuche\" />
									</form>";
								//
								//Notepad
								//
								echo "<br><input type=\"submit\" value=\"NotePad\" name=\"NotePad\" onclick=\"window.open('misc/notepad.php?id=".$s['user_id']."','Notepad','width=600, height=500, status=no, scrollbars=yes')\"><br>";

								//
								// Auslastung
								//
								$g_style=" style=\"color:#0f0\"";
								$y_style=" style=\"color:#ff0\"";
								$o_style=" style=\"color:#fa0\"";
								$r_style=" style=\"color:#f55\"";

								$ures=dbquery("SELECT count(*) FROM ".$db_table['users'].";");
								$uarr=mysql_fetch_row($ures);
								$up=$uarr[0]/$conf['enable_register']['p2'];
								$p1res=dbquery("SELECT count(*) FROM ".$db_table['planets']." WHERE planet_user_id>0;");
								$p1arr=mysql_fetch_row($p1res);
								$p2res=dbquery("SELECT count(*) FROM ".$db_table['planets'].";");
								$p2arr=mysql_fetch_row($p2res);
								if ($p2arr[0]>0)
									$pp=$p1arr[0]/$p2arr[0];
								else
									$pp=0;
								$s1res=dbquery("SELECT count(*) FROM ".$db_table['space_cells'].",".$db_table['planets']." WHERE planet_solsys_id=cell_id AND planet_user_id>0 GROUP BY cell_id;");
								$s1arr=mysql_num_rows($s1res);
								$s2res=dbquery("SELECT count(*) FROM ".$db_table['space_cells']." WHERE cell_solsys_solsys_sol_type>0;");
								$s2arr=mysql_fetch_row($s2res);
								if ($s2arr[0]>0)
									$sp=$s1arr/$s2arr[0];
								else
									$sp=0;
								echo "<br/><div class=\"menutitle\">Auslastung:</div>";
								echo "<table class=\"tb\">";
								echo "<tr><th>User:</th>";
								if ($up<0.5) $tbs=$g_style;
								elseif ($up<0.8) $tbs=$y_style;
								elseif ($up<0.9) $tbs=$o_style;
								else $tbs=$r_style;
								echo "<td $tbs>".$uarr[0]." / ".$conf['enable_register']['p2']."</td><td $tbs>".round($up*100,1)."%</td></tr>";
								echo "<tr><th>Planeten:</th>";
								if ($pp<0.5) $tbs=$g_style;
								elseif ($pp<0.8) $tbs=$y_style;
								elseif ($pp<0.9) $tbs=$o_style;
								else $tbs=$r_style;
								echo "<td $tbs>".$p1arr[0]." / ".$p2arr[0]."</td><td $tbs>".round($pp*100,1)."%</td></tr>";
								echo "<tr><th>Systeme:</th> ";
								if ($sp<0.5) $tbs=$g_style;
								elseif ($sp<0.8) $tbs=$y_style;
								elseif ($sp<0.9) $tbs=$o_style;
								else $tbs=$r_style;
								echo "<td $tbs>".$s1arr." / ".$s2arr[0]."</td><td $tbs>".round($sp*100,1)."%</td></tr>";
								echo "</table>";

								// Online
								echo "<br/><div class=\"menutitle\">Online:</div>";
								$gres=dbquery("SELECT COUNT(*) FROM ".$db_table['users']." WHERE user_acttime>".(time()-$conf['user_timeout']['v']).";");
								$garr=mysql_fetch_row($gres);
								if ($uarr[0]>0)
									$gp=$garr[0]/$uarr[0]*100;
								else
									$gp=0;
								$a1res=dbquery("SELECT COUNT(*)  FROM ".$db_table['admin_users']." WHERE user_acttime>".(time()-TIMEOUT)." AND user_session_key!='';");
								$a1arr=mysql_fetch_row($a1res);
								$a2res=dbquery("SELECT COUNT(*)  FROM ".$db_table['admin_users'].";");
								$a2arr=mysql_fetch_row($a2res);
								if ($a2arr[0]>0)
									$ap=$a1arr[0]/$a2arr[0]*100;
								else
									$ap=0;
								echo "<table class=\"tb\">";
								echo "<tr><th><a href=\"?page=user&amp;sub=userlog\">User:</a></th><td>".$garr[0]." / ".$uarr[0]."</td><td>".round($gp,1)."%</td></tr>";
								echo "<tr><th><a href=\"?page=home&amp;sub=adminlog\">Admins:</a></th><td>".$a1arr[0]." / ".$a2arr[0]."</td><td>".round($ap,1)."%</td></tr>";
								echo "</table><br/>";
								echo "<div style=\"padding-left:10px;\">
								<b>PHP:</b> ".substr(phpversion(),0,10)."<br/>
								<b>MySQL:</b> ".mysql_get_client_info()."<br/>
								<b>Webserver:</b> ".apache_get_version()."
								</div>";
							?>
						</td>
						<td id="content">
							<?php
								// Inhalt einbinden

								if ($conf['updating']['v']!=0 && ($conf['updating']['p2']=="" || $conf['updating']['p2']<time()-120))
								{
									echo "<br/>";
									infobox_start("Update-Problem");
									echo "Das Update k&ouml;nnte unter Umst&auml;nden festh&auml;ngen.";
									if ($conf['updating']['p2']>0)
										echo "Es wurde um ".date("d.m.Y, H:i",$conf['updating']['p2'])." zuletzt ausgeführt";
									echo " <a href=\"?page=$page&amp;releaseupdate=1\">L&ouml;sen</a>";
									infobox_end();
								}
								if ($conf['updating_fleet']['v']!=0 && ($conf['updating_fleet']['p2']=="" || $conf['updating_fleet']['p2']<time()-120))
								{
									echo "<br/>";
									infobox_start("Flottenupdate-Problem");
									echo "Das Flottenupdate k&ouml;nnte unter Umst&auml;nden festh&auml;ngen.";
									if ($conf['updating_fleet']['p2']>0)
										echo "Es wurde um ".date("d.m.Y, H:i",$conf['updating_fleet']['p2'])." zuletzt ausgefhrt";
									echo " <a href=\"?page=$page&amp;releasefleetupdate=1\">L&ouml;sen</a>";
									infobox_end();
								}
								if ($conf['update_enabled']['v']!=1)
								{
									echo "<br/>";
									infobox_start("Updates deaktiviert");
									echo "Die Updates sind momentan deaktiviert!";
									echo " <a href=\"?page=$page&amp;activateupdate=1\">Aktivieren</a>";
									infobox_end();
								}
								
								$allow_inc=false;
								foreach ($navmenu as $cat=> $item)
								{
									foreach ($item as $title=> $data)
									{
										if ($title != "bar" && $data['page']==$page && $data['sub']==$sub)
										{
											$rank=$data['level'];
											if ($data['level']<=$_SESSION[SESSION_NAME]['group_level'])
												$allow_inc=true;
										}
									}
								}
								if ($allow_inc || $rank=="")
								{
									if (eregi("^[a-z\_]+$",$page)  && strlen($page)<=50)
									{
										if (!include("content/".$page.".php"))
											cms_err_msg("Die Seite $page wurde nicht gefunden!");
									}
									else
										echo "<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br><br><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
								}
								else
									echo "<h1>Kein Zugriff</h1> Du hast keinen Zugriff auf diese Seite!<br/><br/> Erwartet: <b>".$adminlevel[$rank]." ($rank)</b>, du bist <b>".$_SESSION[SESSION_NAME]['group_name']." (".$_SESSION[SESSION_NAME]['group_level'].")</b>.";
							?>
						</td>
					</tr>
					<tr>
						<td id="copy">
							&copy;<?PHP echo date('Y');?> by etoa.ch
						</td>
						<td id="bottombar" colspan="2">
							<?php
								echo "<b>Zeit: </b>".date("H:i:s")." &nbsp; ";
								// Renderzeit
								$render_time = explode(" ",microtime());
								$rtime = $render_time[1]+$render_time[0]-$render_starttime;
								echo "<b>Renderzeit:</b> ".round($rtime,3)." sec &nbsp; ";
								// Nickname
								echo "<b>Eingeloggt als: </b>".$_SESSION[SESSION_NAME]['user_nick']." &nbsp; ";
							?>
						</td>
					</tr>
				</table>
				
				<?
				// Write all changes of $s to the session variable
				$_SESSION[SESSION_NAME]=$s;
			}
		?>
	</body>
</html>

