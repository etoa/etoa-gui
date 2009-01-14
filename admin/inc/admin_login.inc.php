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
	// 	Dateiname: admin_login.inc.php	
	// 	Topic: Login-Verwaltung 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//


	function login_prompt($str="",$clr=0)
	{
		adminHtmlHeader();
		$logo = 1;
		global $page;
		if ($logo==1)
		{
			echo "<div  style=\"text-align:center\"><br/><a href=\"..\"><img src=\"../images/game_logo.jpg\" alt=\"Logo\" width=\"450\" height=\"150\" border=\"0\" /></a>";
			echo "<h1 style=\"text-align:center\">Administration - ".GAMEROUND_NAME."</h1><br/>";
		}
		if ($str!="")
		{
			if ($clr==3)
				echo "<div style=\"color:#f90;\">";
			if ($clr==2)
				echo "<div style=\"color:#f00;\">";
			if ($clr==1)
				echo "<div style=\"color:#0f0;\">";
			echo "$str</div><br/>";
		}
		echo "Gib dein Benutzername und dein Passwort ein um dich anzumelden.<br/>
		Klicke auf das Logo um zur Startseite zurückzukehren.<br/><br/>";
		echo "<form action=\"?page=$page\" method=\"post\">";
		echo "<table style=\"margin:0px auto;\">";
		echo "<tr><th class=\"tbltitle\">Benutzername:</th><td class=\"tbldata\"><input type=\"text\" name=\"login_nick\" maxlength=\"255\" size=\"25\" /></td></tr>";
		echo "<tr><th class=\"tbltitle\">Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"login_password\" maxlength=\"255\" size=\"25\" /></td></tr>";
		echo "</table><br/>
		<div style=\"margin:0px auto;\">
			<input type=\"submit\" name=\"login_submit\" value=\"Login\" /> &nbsp;
			<input type=\"button\" value=\"Passwort vergessen\" onclick=\"document.location='?sendpass=1'\" /> &nbsp; 
			<input type=\"button\" value=\"Zum Spiel-Login\" onclick=\"document.location='".LOGINSERVER_URL."'\" />
			
		</div></form></div>";
		echo '<script type="text/javascript">document.forms[0].elements[0].focus()</script>';
		adminHtmlFooter();
		exit;
	}
	
	function login_error($typ)
	{
		global $page;
		switch ($typ) 
		{
			case "password":
				$str = "Sorry, das Passwort ist falsch!<br/>Wenn du es vergessen hast, fordere bitte über untenstehenden Button ein neues an!";
				$clr = 2;
				break;
			case "nothing_entered_user":
				$str = "Sorry, du hast keinen Benutzernamen eingegeben!";
				$clr = 2;
				break;
			case "nothing_entered_password":
				$str = "Sorry, du hast keine Passwort eingegeben!";
				$clr = 2;
				break;
			case "cookie":
				$str = "Sorry, du konntest nicht im System authentifiziert werden! (Fehler 1)";
				$clr = 2;
				break;
			case "cookie2":
				$str = "Sorry, du konntest nicht im System authentifiziert werden! (Fehler 2)";
				$clr = 2;
				break;
			case "cookie3":
				$str = "Sorry, du konntest nicht im System authentifiziert werden! (Fehler 3)";
				$clr = 2;
				break;
			case "logout":
				$str = "Du hast dich erfolgreich abgemeldet!!";
				$clr = 1;
				break;
			case "timeout":
				$str = "Das Timeout von ".tf(TIMEOUT)." wurde erreicht und du wurdest ausgeloggt!";
				$clr = 3;
				break;
			case "invalid_user":
				$str = "Der Benutzer ist nicht vorhanden!";
				$clr = 2;
				break;
			case "locked":
				$str = "Der Benutzer gesperrt!";
				$clr = 2;
				break;
			default:
				$str = "Sorry, ein unbekannter Fehler trat auf!";
				$clr = 2;
		}
		$_SESSION[SESSION_NAME]=Null;
		login_prompt($str,$clr);	
	}
	
	function create_sess_array($arr)
	{
		$_SESSION[SESSION_NAME]['user_id']=$arr['user_id'];
		$_SESSION[SESSION_NAME]['user_nick']=$arr['user_nick'];
		$_SESSION[SESSION_NAME]['user_name']=$arr['user_name'];
		$_SESSION[SESSION_NAME]['user_email']=$arr['user_email'];
		$_SESSION[SESSION_NAME]['user_last_host']=$arr['user_hostname'];
		$_SESSION[SESSION_NAME]['user_last_ip']=$arr['user_ip'];
		$_SESSION[SESSION_NAME]['user_group_id']=$arr['user_admin_rank'];
		$_SESSION[SESSION_NAME]['user_last_login']=$arr['user_last_login'];
		$_SESSION[SESSION_NAME]['group_name']=$arr['group_name'];
		$_SESSION[SESSION_NAME]['group_level']=$arr['group_level'];
		$_SESSION[SESSION_NAME]['theme']=$arr['user_theme'];
	}
	
	if (isset($_GET['logout']) && $_SESSION[SESSION_NAME]['user_id']!="")
	{
		dbquery ("UPDATE ".USER_TABLE_NAME." SET user_session_key='' WHERE user_id=".$_SESSION[SESSION_NAME]['user_id'].";");
 		dbquery ("UPDATE admin_user_log SET log_logouttime=".time()." WHERE log_user_id=".$_SESSION[SESSION_NAME]['user_id']." AND log_session_key='".$_SESSION[SESSION_NAME]['key']."';");
		login_error("logout");		
	}
	
	if (isset($_SESSION[SESSION_NAME]['key']) && $_SESSION[SESSION_NAME]['key']!="")
	{
		if (substr($_SESSION[SESSION_NAME]['key'],64,32)==md5(GAMEROUND_NAME) && substr($_SESSION[SESSION_NAME]['key'],96,32)==md5($_SERVER['REMOTE_ADDR']) && substr($_SESSION[SESSION_NAME]['key'],128,32)==md5($_SERVER['HTTP_USER_AGENT']) && substr($_SESSION[SESSION_NAME]['key'],160)==session_id())
		{
			$res = dbquery("SELECT * FROM ".USER_TABLE_NAME.",admin_groups WHERE MD5(user_id)='".substr($_SESSION[SESSION_NAME]['key'],32,32)."' AND MD5(user_last_login)='".substr($_SESSION[SESSION_NAME]['key'],0,32)."' AND user_admin_rank=group_id AND user_locked=0;");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				if (time()-TIMEOUT < $arr['user_acttime'])
				{
					if ($arr['user_session_key']==$_SESSION[SESSION_NAME]['key'] && $arr['user_session_key']!="")
					{
						create_sess_array($arr);
  					dbquery ("UPDATE ".USER_TABLE_NAME." SET user_acttime=".time()." WHERE user_id=".$arr['user_id'].";");
 						dbquery ("UPDATE admin_user_log SET log_acttime=".time()." WHERE log_user_id=".$_SESSION[SESSION_NAME]['user_id']." AND log_session_key='".$_SESSION[SESSION_NAME]['key']."';");
  				}
  				else
  					login_error("cookie3");
				}
				else
					login_error("timeout");
			}	
			else
				login_error("cookie2");
		}	
		else
			login_error("cookie");
	}
	elseif (isset($_POST['login_submit']) && $_POST['login_submit']!="")
	{
		if ($_POST['login_nick']!="")
		{	
			if ($_POST['login_password']!="")
			{
				$_POST['login_nick']=str_replace("\'","",$_POST['login_nick']);
				$_POST['login_nick']=str_replace("'","",$_POST['login_nick']);
				$_POST['login_password']=str_replace("\'","",$_POST['login_password']);
				$_POST['login_password']=str_replace("'","",$_POST['login_password']);
				$res = dbquery("SELECT * FROM ".USER_TABLE_NAME.",admin_groups WHERE user_nick='".$_POST['login_nick']."' AND user_admin_rank>0 AND user_admin_rank=group_id;");
				if (mysql_num_rows($res)!=0)
				{					
					$arr = mysql_fetch_array($res);
					if ($arr['user_locked']==0)
					{
						if (pw_salt($_POST['login_password'],$arr['user_id']) == $arr['user_password'])
						{
							$login_time=time();
							// Session-Array mit Userdaten generieren
							create_sess_array($arr);
			  			// Eindeutige ID für diese Session generieren
			  			$_SESSION[SESSION_NAME]['key']=md5($login_time).md5($arr['user_id']).md5(GAMEROUND_NAME).md5($_SERVER['REMOTE_ADDR']).md5($_SERVER['HTTP_USER_AGENT']).session_id();
			  			// Loginzeit in DB speichern
			  			dbquery ("UPDATE ".USER_TABLE_NAME." SET user_last_login=".$login_time.",user_acttime=".time().",user_session_key='".$_SESSION[SESSION_NAME]['key']."',user_ip='".$_SERVER['REMOTE_ADDR']."',user_hostname='".$_SERVER['REMOTE_ADDR']."' WHERE user_id=".$arr['user_id'].";");
				  		dbquery ("INSERT INTO  admin_user_log (log_user_id,log_logintime,log_ip,log_hostname,log_session_key) VALUES (".$arr['user_id'].",".time().",'".$_SERVER['REMOTE_ADDR']."','".$_SERVER['REMOTE_ADDR']."','".$_SESSION[SESSION_NAME]['key']."');");
							
						}
						else
						{
							login_error("password");
						}
					}
					else
					{
						login_error("locked");
					}
	  		}
				else
				{
					login_error("invalid_user");
				}
			}
			else
			{
				login_error("nothing_entered_password");		
			}
		}
		else
		{
			login_error("nothing_entered_user");		
		}
	}
	elseif (isset($_GET['sendpass']))
	{
		adminHtmlHeader();
		echo "<div style=\"width:500px;margin:10px auto;text-align:center;\">" .
		"<br/><a href=\"..\"><img src=\"../images/game_logo.jpg\" alt=\"Logo\" width=\"450\" height=\"150\" border=\"0\" /></a>";
		echo "<h1 style=\"text-align:center;\">Administration - ".GAMEROUND_NAME."</h1>
		<h2 style=\"text-align:center;\">Passwort senden</h2>";
		if (isset($_POST['sendpass_submit']))
		{
			$res = dbquery("SELECT user_id,user_nick,user_email FROM admin_users WHERE user_nick='".addslashes($_POST['user_nick'])."';");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_row($res);			
				$pw = mt_rand(1000000,9999999);
				$msg = "Hallo ".$arr[1].".\n\nDu hast für die Administration der ".GAMEROUND_NAME." von EtoA ein neues Passwort angefordert.\n\n";
				$msg.= "Das neue Passwort lautet: $pw\n\n";
				$msg.= "Diese Anfrage wurde am ".date("d.m.Y")." um ".date("H:i")." Uhr vom Computer ".resolveIp($_SERVER['REMOTE_ADDR'])." aus in Auftrag gegeben.\nBitte denke daran, das Passwort nach dem ersten Login zu ändern!";
				send_mail(0,$arr[2],"Neues Administrationspasswort ".GAMEROUND_NAME."",$msg,'','');
				echo "Das Passwort wurde geändert und dir per Mail zugestellt!<br/><br/>";
				echo "<input type=\"button\" value=\"Zum Login\" onclick=\"document.location='?'\" />";
				dbquery("
				UPDATE 
					admin_users
				SET
					user_password='".pw_salt($pw,$arr[0])."',
					user_force_pwchange=1					
				WHERE
					user_id=".$arr[0].";");
				add_log(8,"Der Administrator ".$arr[1]." (ID: ".$arr[0].") fordert per E-Mail (".$arr[2].") von ".$_SERVER['REMOTE_ADDR']." aus ein neues Passwort an.",time());					
			}
			else
			{
				echo "Dieser Benutzer existiert nicht!<br/><br/>";
				echo "<input type=\"button\" value=\"Nochmals versuchen\" onclick=\"document.location='?sendpass=1'\" />";
			}
		}
		else
		{
			echo "Hier kannst du ein neues Passwort beantragen.<br/>Es wird dir dann an die eingestellte E-Mail-Adresse gesendet!<br/>";
			echo "<form action=\"?sendpass=1\" method=\"post\">";			
			echo '<table class="tb" style="width:400px;margin:10px auto;">';
			echo '<tr><th>Loginname:</th><td><input type="text" name="user_nick" /></td></tr>';
			echo '</table><br/><input type="submit" name="sendpass_submit" value="Neues Passwort senden" /> &nbsp; ';
			echo "<input type=\"button\" value=\"Zum Login\" onclick=\"document.location='?'\" />";
			echo "</form></div>";
			echo '<script type="text/javascript">document.forms[0].elements[0].focus()</script>';
		}
		adminHtmlFooter();
		exit;
	}
	else
	{
		$res = dbquery("SELECT COUNT(user_id) FROM admin_users;");
		$arr = mysql_fetch_row($res);
		if ($arr[0]==0)
		{
			adminHtmlHeader();
			if (isset($_POST['newuser_submit']) && $_POST['user_nick']!="" && $_POST['user_password']!='')
			{
				dbquery("INSERT INTO admin_users (user_nick,user_password,user_admin_rank) VALUES ('".$_POST['user_nick']."','".md5($_POST['user_password'])."',8);");
				echo "Benutzer wurde erstellt!<br/><br/><input type=\"button\" onclick=\"document.location='?'\" value=\"Weiterfahren\"/>";				
			}
			else
			{
				echo "<div style=\"width:500px;margin:10px auto;text-align:center;\">" .
				"<br/><a href=\"..\"><img src=\"../images/game_logo.jpg\" alt=\"Logo\" width=\"450\" height=\"150\" border=\"0\" /></a>";
				echo "<h1 style=\"text-align:center;\">Administration - ".GAMEROUND_NAME."</h1>";
				echo "<form action=\"?\" method=\"post\">";			
				echo "<h2 style=\"text-align:center;\">Admin-User erstellen</h2>";
				echo '<table class="tb" style="width:400px;margin:10px auto;">';
				echo '<tr><th>Loginname:</th><td><input type="text" name="user_nick" /></td></tr>';
				echo '<tr><th>Passwort:</th><td><input type="password" name="user_password" /></td></tr>';
				echo '</table><br/><input type="submit" name="newuser_submit" value="Admin-User erstellen" />';
				echo "</form></div>";
				echo '<script type="text/javascript">document.forms[0].elements[0].focus()</script>';
			}			
			adminHtmlFooter();
			exit;
		}
		else
		{
			login_prompt();	
		}
	}


?>
