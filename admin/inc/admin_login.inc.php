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

	$login_successfull=true;
	
	function login_prompt()
	{
		global $page, $login_successfull;
	
		echo "<div  style=\"text-align:center\"><br/><a href=\"..\"><img src=\"../images/game_logo.jpg\" alt=\"Logo\" width=\"450\" height=\"150\" border=\"0\" /></a>";
		echo "<h1 style=\"text-align:center\">Administration - ".GAMEROUND_NAME."</h1><br/>";
		echo "<form action=\"?page=$page\" method=\"post\">";
		echo "<table style=\"margin:0px auto;\">";
		echo "<tr><th class=\"tbltitle\">Benutzername:</th><td class=\"tbldata\"><input type=\"text\" name=\"login_nick\" maxlength=\"255\" size=\"25\" /></td></tr>";
		echo "<tr><th class=\"tbltitle\">Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"login_password\" maxlength=\"255\" size=\"25\" /></td></tr>";
		echo "</table><br/><div style=\"margin:0px auto;\"><input type=\"submit\" class=\"button\" name=\"login_submit\" value=\"Login!\" /></div></form></div>";
		echo '<script type="text/javascript">document.forms[0].elements[0].focus()</script>';
		$login_successfull=false;
	}
	
	function login_error($typ)
	{
		global $login_successfull, $page;
		switch ($typ) 
		{
			case "password":
				$str = "Sorry, dieser Benutzer existiert nicht oder das Passwort ist falsch!";
				break;
			case "nothing_entered_user":
				$str = "Sorry, du hast keinen Benutzernamen eingegeben!";
				break;
			case "nothing_entered_password":
				$str = "Sorry, du hast keine Passwort eingegeben!";
				break;
			case "cookie":
				$str = "Sorry, du konntest nicht im System authentifiziert werden! (Fehler 1)";
				break;
			case "cookie2":
				$str = "Sorry, du konntest nicht im System authentifiziert werden! (Fehler 2)";
				break;
			case "cookie3":
				$str = "Sorry, du konntest nicht im System authentifiziert werden! (Fehler 3)";
				break;
			case "logout":
				$str = "Du hast dich erfolgreich abgemeldet!!";
				break;
			case "timeout":
				$str = "Das Timeout von ".TIMEOUT."s wurde erreicht und du wurdest ausgeloggt!";
				break;
			default:
				$str = "Sorry, ein unbekannter Fehler trat auf!";
		}
		echo "<div style=\"text-align:center\"><br/><a href=\"".LOGINSERVER_URL."\"><img src=\"../images/game_logo.jpg\" alt=\"Logo\" width=\"450\" height=\"150\" border=\"0\" /></a><br/>";
		echo "<h1 style=\"text-align:center\">Administration - ".GAMEROUND_NAME."</h1>";
		echo "<br/><div style=\"margin:0px auto\">$str</div>";
		echo '<br/><input type="button" onclick="document.location=\'?\'" value="Neu anmelden" /></div>';
		echo '<script type="text/javascript">setTimeout(\'document.location="?"\',1000);</script>';
		$_SESSION[SESSION_NAME]=Null;
		$login_successfull=false;
		echo "</body></html>";
		die();
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
	}
	
	if (isset($_GET['logout']) && $_SESSION[SESSION_NAME]['user_id']!="")
	{
		dbquery ("UPDATE ".USER_TABLE_NAME." SET user_session_key='' WHERE user_id=".$_SESSION[SESSION_NAME]['user_id'].";");
 		dbquery ("UPDATE ".$db_table['admin_user_log']." SET log_logouttime=".time()." WHERE log_user_id=".$_SESSION[SESSION_NAME]['user_id']." AND log_session_key='".$_SESSION[SESSION_NAME]['key']."';");
		login_error("logout");		
	}
	
	if ($_SESSION[SESSION_NAME]['key']!="")
	{
		if (substr($_SESSION[SESSION_NAME]['key'],64,32)==md5(GAMEROUND_NAME) && substr($_SESSION[SESSION_NAME]['key'],96,32)==md5($_SERVER['REMOTE_ADDR']) && substr($_SESSION[SESSION_NAME]['key'],128,32)==md5($_SERVER['HTTP_USER_AGENT']) && substr($_SESSION[SESSION_NAME]['key'],160)==session_id())
		{
			$res = dbquery("SELECT * FROM ".USER_TABLE_NAME.",".$db_table['admin_groups']." WHERE MD5(user_id)='".substr($_SESSION[SESSION_NAME]['key'],32,32)."' AND MD5(user_last_login)='".substr($_SESSION[SESSION_NAME]['key'],0,32)."' AND user_admin_rank=group_id AND user_locked=0;");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				if (time()-TIMEOUT < $arr['user_acttime'])
				{
					if ($arr['user_session_key']==$_SESSION[SESSION_NAME]['key'] && $arr['user_session_key']!="")
					{
						create_sess_array($arr);
  					dbquery ("UPDATE ".USER_TABLE_NAME." SET user_acttime=".time()." WHERE user_id=".$arr['user_id'].";");
 						dbquery ("UPDATE ".$db_table['admin_user_log']." SET log_acttime=".time()." WHERE log_user_id=".$_SESSION[SESSION_NAME]['user_id']." AND log_session_key='".$_SESSION[SESSION_NAME]['key']."';");
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
				$res = dbquery("SELECT * FROM ".USER_TABLE_NAME.",".$db_table['admin_groups']." WHERE user_nick='".$_POST['login_nick']."' AND user_password='".md5($_POST['login_password'])."' AND user_admin_rank>0 AND user_admin_rank=group_id AND user_locked=0;");
				if (mysql_num_rows($res)!=0)
				{					
					$arr = mysql_fetch_array($res);
					$login_time=time();
					// Session-Array mit Userdaten generieren
					create_sess_array($arr);
	  			// Eindeutige ID für diese Session generieren
	  			$_SESSION[SESSION_NAME]['key']=md5($login_time).md5($arr['user_id']).md5(GAMEROUND_NAME).md5($_SERVER['REMOTE_ADDR']).md5($_SERVER['HTTP_USER_AGENT']).session_id();
	  			// Loginzeit in DB speichern
	  			dbquery ("UPDATE ".USER_TABLE_NAME." SET user_last_login=".$login_time.",user_acttime=".time().",user_session_key='".$_SESSION[SESSION_NAME]['key']."',user_ip='".$_SERVER['REMOTE_ADDR']."',user_hostname='".$_SERVER['REMOTE_ADDR']."' WHERE user_id=".$arr['user_id'].";");
		  			
		  		dbquery ("INSERT INTO  ".$db_table['admin_user_log']." (log_user_id,log_logintime,log_ip,log_hostname,log_session_key) VALUES (".$arr['user_id'].",".time().",'".$_SERVER['REMOTE_ADDR']."','".$_SERVER['REMOTE_ADDR']."','".$_SESSION[SESSION_NAME]['key']."');");
	  			

	  		}
				else
					login_error("password");
			}
			else
				login_error("nothing_entered_password");		
		}
		else
			login_error("nothing_entered_user");		
	}
	else
	{
		$res = dbquery("SELECT COUNT(user_id) FROM admin_users;");
		$arr = mysql_fetch_row($res);
		if ($arr[0]==0)
		{
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
			}
			$login_successfull=false;
		}
		else
		{
			login_prompt();	
		}
	}


?>
