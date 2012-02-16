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
	// 	Dateiname: home.php	
	// 	Topic: Willkommensseite der Administration 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//

	//
	// Offline
	//
	if ($sub=="offline")
	{
		echo "<h1>Spiel offline nehmen</h1>";
		
		if (isset($_GET['off']) && $_GET['off']==1)
		{
			$cfg->set('offline',1);
		}
		if (isset($_GET['on']) && $_GET['on']==1)
		{
			$cfg->set('offline',0);
		}
		
		if (isset($_POST['save']))
		{
			$cfg->set('offline',1,$_POST['param1'],$_POST['param2']);
		}

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		if ($cfg->get('offline')==1)
		{
			echo "<span style=\"color:#f90;\">Das Spiel ist offline!</span><br/><br/>
			Erlaubte IP's (deine ist ".$_SERVER['REMOTE_ADDR']."):<br/> <textarea name=\"param1\" rows=\"6\" cols=\"60\">".$cfg->p1('offline')."</textarea><br/>
			Nachricht: <br/><textarea name=\"param2\" rows=\"6\" cols=\"60\">".$cfg->p2('offline')."</textarea><br/><br/>
			<input type=\"submit\" value=\"&Auml;nderungen speichern\" name=\"save\" /> &nbsp; 
			<input type=\"button\" value=\"Spiel online stellen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;on=1'\" />";
			
		}
		else
		{
			echo "<span style=\"color:#0f0;\">Das Spiel ist online!</span><br/><br/>
			<input type=\"button\" value=\"Spiel offline nehmen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;off=1'\" />";
		}	
		echo "</form>";
	}	
	
	
	//
	// Rangliste
	//
	elseif ($sub=="stats")
	{
		require("home/stats.inc.php");
	}

	//
	// Backend
	//
	elseif ($sub=="daemon")
	{
		echo "<h1>Backend-Daemon</h1>";
		if (UNIX)
		{
			$frm = new Form("bustn","?page=$page&amp;sub=$sub");
			echo $frm->begin();
			
			tableStart("Daemon-Infos");
			$un=posix_uname();
			echo "<tr><th>System</th><td>".$un['sysname']." ".$un['release']." ".$un['version']."</td></tr>";
			echo "<tr><th>Daemon-ID</th><td>".$daemonId."</td></tr>";
			echo "<tr><th>Pfad</th><td>".$daemonExe."</td></tr>";
			echo "<tr><th>Logfile</th><td>".$daemonLogfile."</td></tr>";
			echo "<tr><th>Pidfile</th><td>".$daemonPidfile."</td></tr>";
			tableEnd();
			echo $frm->end();		
		
		
			echo "<h2>Log</h2>";
			// Warning: Open-Basedir restrictions may appply
			if (is_file($daemonLogfile))
			{
				echo "<div id=\"logtext\" style=\"border:1px solid white;background:black;padding:3px;overflow:scroll;height:400px\">";
				$lf = fopen($daemonLogfile,"r");
				while($l = fgets($lf))
				{
					if (stristr($l,"warning]"))
						echo "<span style=\"color:orange;\">";
					elseif (stristr($l,"err]"))
						echo "<span style=\"color:red;\">";
					elseif(stristr($l,"notice]"))
						echo "<span style=\"color:#afa;\">";
					else
						echo "<span>";
					echo $l."</span><br/>";
				}
				fclose($lf);
				echo "</div>";

				echo "<script type=\"text/javascript\">
				textareaelem = document.getElementById('logtext');
				textareaelem.scrollTop = textareaelem.scrollHeight;
				</script>";
			}
			else
			{
				echo "<div style=\"color:red;\">Die Logdatei ".$daemonLogfile." kann nicht geöffnet werden!</div>";
			}
		}
		else
		{
			echo "Der Backend-Daemon wird nur auf UNIX-Systemen unterstüzt!";
		}
	}	
	
	//
	// Statistiken
	//
	elseif ($sub=="gamestats")
	{	
		echo "<h1>Spielstatistiken</h1>";
		if (!@include(CACHE_ROOT."/out/gamestats.html"))
		{
			error_msg("Run scripts/gamestats.php periodically to update gamestats!",1);			
		}		
	}
		
	//
	// Admin Session-Log
	//
	elseif ($sub=="adminlog")
	{
		echo "<h1>Admin-Log</h1>";
		
		if (isset($_POST['logshow']) && $_POST['logshow']!="")
		{
			$ures=dbquery("SELECT
				user_nick
				FROM admin_users
				WHERE user_id=".$_POST['user_id'].";");
			if (mysql_num_rows($ures)>0)
			{
				$uarr=mysql_fetch_array($ures);
				echo "<h2>Session-Log f&uuml;r ".$uarr['user_nick']."</h2>";

				$sql = "SELECT
					l.*,
					u.user_nick
				FROM
					admin_user_sessionlog l
				INNER JOIN
					admin_users u
					ON l.user_id=u.user_id
					AND l.user_id=".$_POST['user_id']."
				ORDER BY
					time_action DESC;";
				$res=dbquery($sql);
				if (mysql_num_rows($res)>0)
				{
					echo "<table class=\"tb\">
					<tr>
						<th>Login</th>
						<th>Aktivität</th>
						<th>Logout</th>
						<th>Dauer</th>
						<th>IP</th>
						<th>Browser</th>
						<th>OS</th>
					</tr>";
					while ($arr = mysql_fetch_array($res))
					{
						echo "<tr>
							<td>".date("d.m.Y, H:i",$arr['time_login'])."</td>";
						echo "<td>";
						if ($arr['time_action']>0)
							echo date("d.m.Y H:i",$arr['time_action']);
						else
							echo "-";
						echo "</td>";
						echo "<td>";
						if ($arr['time_logout']>0)
							echo date("d.m.Y, H:i",$arr['time_logout']);
						else
							echo "-";
						echo "</td>";
						echo "<td>";
						if (max($arr['time_logout'],$arr['time_action'])-$arr['time_login']>0)
							echo tf(max($arr['time_logout'],$arr['time_action'])-$arr['time_login']);
						else
							echo "-";
						if ($arr['log_session_key']==$s->id)
							echo " <span style=\"color:#0f0\">aktiv</span>";
						echo "</td>";
						echo "<td title=\"".Net::getHost($arr['ip_addr'])."\">".$arr['ip_addr']."</td>";
						$browser = get_browser($arr['user_agent'], true);
						echo "<td title=\"".$arr['user_agent']."\">".$browser['parent']."</td>";
						echo "<td title=\"".$arr['user_agent']."\">".$browser['platform']."</td>";
						echo "</tr>";
					}
					echo "</table>";
				}
				else
					echo "<i>Keine Eintr&auml;ge vorhanden</i>";				

			}
			else
			{
				echo "<h2>Fehler</h2><i>User nicht vorhanden</i>";			
			}
			echo "<br/><br/><input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
		}
		else
		{
			$logDelTimespan = array(
				array(1296000,"15 Tage"),
				array(2592000,"30 Tage"),
				array(3888000,"45 Tage"),
				array(5184000,"60 Tage"),
			);

			if (isset($_GET['kick']) && $_GET['kick']>0)
			{
				if ($_GET['kick']!=$cu->id)
				{
					AdminSession::kick($_GET['kick']);
					add_log(8,$cu->nick." l&ouml;scht die Session des Administrators mit der ID ".$_GET['kick'],time());
				}
				else
					echo error_msg("Du kannst nicht dich selbst kicken!");
			}
			
			if (isset($_POST['delentrys']) && $_POST['delentrys']!="")
			{
				if (isset($logDelTimespan[$_POST['log_timestamp']]))
				{
					$td = $logDelTimespan[$_POST['log_timestamp']][0];
					$nr = AdminSession::cleanupLogs($td);
					echo "<p>".$nr." Eintr&auml;ge wurden gel&ouml;scht!</p>";
				}
			}			
			
			echo "<h2>Aktive Sessions</h2>";
			echo "Das Timeout betr&auml;gt ".tf($cfg->admin_timeout->v)."<br/><br/>";

			$res=dbquery("
				SELECT 
					s.user_id,
					s.ip_addr,
					s.user_agent,
					s.time_login,
					s.time_action,
					u.user_nick
				FROM
					admin_user_sessions s
				INNER JOIN 
					admin_users u
					ON s.user_id=u.user_id
				ORDER BY
					time_action DESC;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tb\">
				<tr>
					<th>Status</th>
					<th>Nick</th>
					<th>Login</th>
					<th>Aktivität</th>
					<th>Dauer</th>
					<th>IP</th>
					<th>Browser</th>
					<th>OS</th>
					<th>Kicken</th>
				</tr>";
				$t = time();
				while ($arr=mysql_fetch_array($res))
				{
					$browser = get_browser($arr['user_agent'], true);
					echo "<tr>
						<td ".($t - $cfg->admin_timeout->v < $arr['time_action'] ? 'style="color:#0f0;">Online': 'style="color:red;">Timeout')."</td>
						<td>".$arr['user_nick']."</td>
						<td>".date("d.m.Y H:i",$arr['time_login'])."</td>
						<td>".date("d.m.Y H:i",$arr['time_action'])."</td>
						<td>".tf($arr['time_action']-$arr['time_login'])."</td>
						<td title=\"".Net::getHost($arr['ip_addr'])."\">".$arr['ip_addr']."</td>
						<td title=\"".$arr['user_agent']."\">".$browser['parent']."</td>
						<td title=\"".$arr['user_agent']."\">".$browser['platform']."</td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;kick=".$arr['user_id']."\">Kick</a></td>
					</tr>";
				}			
				echo "</table>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden!</i>";
			
			echo "<h2>Session-Log</h2>";
			$res=dbquery("SELECT 
				user_nick,
				u.user_id,
				COUNT(*) as cnt
			FROM admin_users u
			INNER JOIN
				admin_user_sessionlog l
				ON l.user_id=u.user_id
			GROUP BY u.user_id ORDER BY u.user_nick;");
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "Benutzer w&auml;hlen: <select name=\"user_id\">";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']." (".$arr['cnt']." Sessions)</option>";
				}
				echo "</select> &nbsp; <input type=\"submit\" name=\"logshow\" value=\"Anzeigen\" /></form>";

				$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM admin_user_sessionlog;"));

				echo "<h2>Logs löschen</h2>";
				echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
				echo "Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>
					Eintr&auml;ge l&ouml;schen die &auml;lter als <select name=\"log_timestamp\">";
				foreach ($logDelTimespan as $k => $lts)
				{
					echo "<option value=\"".$k."\">".$lts[1]."</option>";
				}
				echo "</select> sind: <input type=\"submit\" name=\"delentrys\" value=\"Ausf&uuml;hren\" /></form>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden</i>";
		}
	}	
	
	//
	// Ingame-News
	//
	elseif ($sub=="ingamenews")
	{
		echo "<h1>Ingame-News</h1>";
		echo "<form action=\"?page=$page&sub=$sub#writer\" method=\"post\">";

		if (isset($_POST['save']))
		{
			$cfg->set("info",$_POST['config_value'],$_POST['enable']);
		}


		if ($cfg->param1("info")==1 && $cfg->value('info')!="")
		{
			echo "Diese News erscheinen auf der Startseite im Game:<br/><br/>";
			iBoxStart("Vorschau");
			echo text2html($cfg->value('info'));
			iBoxEnd();
		}

		echo "<a name=\"writer\"></a>";
		if (isset($_POST['save']))
		{
			success_msg("Nachricht geändert!");
		}

		echo "<input type=\"radio\" name=\"enable\" value=\"1\" ".($cfg->param1("info")==1 ? ' checked="checked"' :'')." /> Anzeigen
		<input type=\"radio\" name=\"enable\" value=\"0\" ".($cfg->param1("info")!=1 ? ' checked="checked"' :'')." /> Verstecken<br/><br/>";

		echo "<textarea name=\"config_value\" cols=\"120\" rows=\"20\">".$cfg->value('info')."</textarea><br/><br/>";
		echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />";
		echo "</form>";	
	}	
	

	//
	// System-Nachricht
	//
	elseif ($sub=="systemmessage")
	{
		echo "<h1>Systemnachricht</h1>";
		if (isset($_POST['save']))
		{
			$cfg->set("system_message",$_POST['config_value']);
			success_msg("Nachricht geändert!");
		}		
		if (isset($_POST['saveclear']))
		{
			$cfg->set("system_message","");
			success_msg("Nachricht gelöscht!");
		}		
		
    echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
		$res = dbquery("SELECT * FROM config WHERE config_name='system_message';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			echo "Diese Nachricht erscheint sofort auf jeder Seite im Spiel:<br/><br/>";
			if ($arr['config_value']!="")
			{
				iBoxStart("Vorschau");
				echo text2html($arr['config_value']);
				iBoxEnd();
			}
			echo "<textarea name=\"config_value\" cols=\"100\" rows=\"15\">".$arr['config_value']."</textarea><br/><br/>";
			echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" /> &nbsp; 
			<input type=\"submit\" name=\"saveclear\" value=\"Löschen\" />";
		}
		else
		{
			echo "Es ist kein Datensatz vorhanden!";
		}
		echo "</form>";	
	}
		
	
	//
	// Admin-News
	//
	elseif ($sub=="adminnews")
	{
		if (isset($_POST['save']))
		{
				dbquery("UPDATE config SET config_value='".$_POST['config_value']."' WHERE config_name='admininfo';");
		}
		echo "<h1>Ingame-News</h1>";
		echo "Diese News erscheinen auf der Startseite des Adminmodus:<br/><br/>";
    echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
		$res = dbquery("SELECT * FROM config WHERE config_name='admininfo';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			if ($arr['config_value']!="")
			{
				iBoxStart("Vorschau");
				echo text2html($arr['config_value']);
				iBoxEnd();
			}
			echo "<textarea name=\"config_value\" cols=\"100\" rows=\"15\">".$arr['config_value']."</textarea><br/><br/>";
			echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />";
		}
		else
			echo "Es ist kein Datensatz vorhanden!";
		echo "</form>";	
	}		
	
	//
	// User bearbeiten
	//
	elseif ($sub=="adminusers")
	{
		require("home/adminusers.inc.php");
	}
	
	//
	// User beobachten
	//
	elseif ($sub=="observed")
	{
		require("home/observed.inc.php");
	}	
	
	//
	// Ãœbersicht
	//
	else
	{
		echo "<h1>&Uuml;bersicht</h1>";


		if (!isset($s->home_visited))
		{
			echo "<p>Hallo <b>".$cu->nick."</b>, willkommen im Administrationsmodus! Dein Rang ist <b>".$cu->groupName.".</b><br/></p>";
			//echo "<span style=\"color:#0f0;\">Dein letzter Login war <b>".df($s['user_last_login'])."</b>, Host: <b>".Net::getHost($s['user_last_host'])."</b> (aktuell: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."), IP: <b>".$s['user_last_ip']."</b> (aktuell: ".$_SERVER['REMOTE_ADDR'].")</span><br/><br/>";
			$s->home_visited=true;
		}
		
		//
		// Universum generieren
		//
		$res = dbquery("SELECT COUNT(id) FROM cells;");
		$arr = mysql_fetch_row($res);
		if ($arr[0]==0)
		{
			echo "<h2>Universum existiert noch nicht!</h2>";
			echo "<div style=\"color:orange;font-weight:bold;\">Das Universum wurde noch nicht erschaffen!<br/><br/>
			<input type=\"button\" value=\"Weiter zum Urknall\" onclick=\"document.location='?page=setup&sub=uni'\" /></div><br/><br/>";
		}
		else
		{				
		
		if ($cu->forcePasswordChange)
		{
			iBoxStart("Passwort");   
			echo "<span style=\"color:#f90;\">Dein Passwort wurde seit der letzten automatischen Generierung noch nicht geändert. Bitte mache das jetzt <a href=\"?myprofile=1\">hier</a>!</span>";
			iBoxEnd();			
		}
		
		//
		// Admin-News
		//
		if ($conf['admininfo']['v']!="")
		{
			iBoxStart("Admin-News");   
			echo text2html($conf['admininfo']['v']);
			iBoxEnd();			
		}

		// Flottensperre aktiv
		if ($conf['flightban']['v']==1)
		{
			// PrÃƒÂ¼ft, ob die Sperre schon abgelaufen ist
			if($conf['flightban_time']['p1']<=time() && $conf['flightban_time']['p2']>=time())
			{
				$flightban_time_status = "<span style=\"color:#0f0\">Aktiv</span>";
			}
			elseif($conf['flightban_time']['p1']>time() && $conf['flightban_time']['p2']>time())
			{
				$flightban_time_status = "Ausstehend";
			}
			else
			{
				$flightban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
			}
			
			echo "<br/>";
			iBoxStart("Flottensperre aktiviert");
			echo "Die Flottensperre ist aktiviert. Es kÃƒÂ¶nnen keine FlÃƒÂ¼ge gestartet werden!<br><br><b>Status:</b> ".$flightban_time_status."<br><b>Zeit:</b> ".date("d.m.Y H:i",$conf['flightban_time']['p1'])." - ".date("d.m.Y H:i",$conf['flightban_time']['p2'])."<br><b>Grund:</b> ".$conf['flightban']['p1']."<br><br>";
			echo "Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
			iBoxEnd();
		}
		
		// Kampfsperre aktiv
		if ($conf['battleban']['v']==1)
		{
			// PrÃƒÂ¼ft, ob die Sperre schon abgelaufen ist
			if($conf['battleban_time']['p1']<=time() && $conf['battleban_time']['p2']>=time())
			{
				$battleban_time_status = "<span style=\"color:#0f0\">Aktiv</span>";
			}
			elseif($conf['battleban_time']['p1']>time() && $conf['battleban_time']['p2']>time())
			{
				$battleban_time_status = "Ausstehend";
			}
			else
			{
				$battleban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
			}
			
			echo "<br/>";
			iBoxStart("Kampfsperre aktiviert");
			echo "Die Kampfsperre ist aktiviert. Es kÃƒÂ¶nnen keine Angriffe geflogen werden!<br><br><b>Status:</b> ".$battleban_time_status."<br><b>Zeit:</b> ".date("d.m.Y H:i",$conf['battleban_time']['p1'])." - ".date("d.m.Y H:i",$conf['battleban_time']['p2'])."<br><b>Grund:</b> ".$conf['battleban']['p1']."<br><br>";
			echo "Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
			iBoxEnd();
		}

		if ($conf['system_message']['v']!="")
		{
			echo "<br/>";
			iBoxStart("<span style=\"color:red;\">Folgende Systemnachricht ist zurzeit aktiviert (<a href=\"?page=$page&amp;sub=systemmessage\">Bearbeiten/Deaktivieren</a>):</span>");
			echo text2html($conf['system_message']['v']);
			iBoxEnd();			
		}
		
		if ($cfg->value('register_key')!="")
		{
			iBoxStart("Schutz der öffentlichen Seiten");
			echo "Die öffentlichen Seiten (Anmeldung, Statistiken etc) sind durch den Schlüssel <span style=\"font-weight:bold;color:#f90\">".$cfg->value('register_key')."</span> geschützt!";
			iBoxEnd();				
		}
		
		
		if ($cfg->value('offline')==1)
		{
			echo "<br/>";
			iBoxStart("<span style=\"color:red;\">Spiel offline</span>");
			echo $cfg->value('p1')." &nbsp; [<a href=\"?page=$page&amp;sub=offline\">&Auml;ndern</a>]";
			iBoxEnd();			
		}
		
		//
		// Schnellsuche
		//
		$_SESSION['planets']['query']=Null;
		$_SESSION['admin']['user_query']="";
		$_SESSION['admin']['queries']['alliances']="";

		tableStart("Schnellsuche", 800);
		echo "<form action=\"?page=user&amp;action=search\" method=\"post\"><tr><th class=\"tbltitle\">Nick:</th>";
		echo "<td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" size=\"40\" /> <input type=\"hidden\" name=\"qmode[user_nick]\" value=\"LIKE '%\" /><input type=\"submit\" name=\"user_search\" value=\"Suchen\" /></td></tr></form>";

		echo "<form action=\"?page=galaxy&amp;action=searchresults\" method=\"post\"><tr><th class=\"tbltitle\">Planet:</th>";
		echo "<td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" size=\"40\" /> <input type=\"hidden\" name=\"qmode[planet_name]\" value=\"%\" /> <input type=\"submit\" name=\"search_submit\" value=\"Suchen\" /></td></tr></form>";

		echo "<form action=\"?page=galaxy&amp;action=searchresults\" method=\"post\"><tr><th class=\"tbltitle\">Planet-Besitzer:</th>";
		echo "<td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" size=\"40\" /> <input type=\"hidden\" name=\"qmode[user_nick]\" value=\"%\" /> <input type=\"submit\" name=\"search_submit\" value=\"Suchen\" /></td></tr></form>";

		echo "<form action=\"?page=alliances&amp;action=search\" method=\"post\"><tr><th class=\"tbltitle\">Allianz-Tag:</th>";
		echo "<td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" size=\"40\" /> <input type=\"hidden\" name=\"qmode[alliance_tag]\" value=\"LIKE '%\" /><input type=\"submit\" name=\"alliance_search\" value=\"Suchen\" /></td></tr></form>";
		tableEnd();
		echo "<script>document.forms[1].elements[0].focus()</script>";
		
	
		tableStart("Spieler-Tools", 800);
		

		// Tickets
		$tnew = Ticket::countNew();
		$tass = Ticket::countAssigned($cu->id);

		echo "<tr><th class=\"tbltitle\">Ticket-System:</th>";
		echo "<td class=\"tbldata\">
		".popupLink("tickets",$tnew." neue Tickets",($tnew>0) ? "font-weight:bold;color:#f90;":"")."
		vorhanden";
		if ($tass>0)
		{
			echo ", ".popupLink("tickets",$tass." offene Tickets",($tass>0) ? "font-weight:bold;color:#f90;":"")." vorhanden";
		}
		echo "</td></tr>";
		
		// Beobachter
		$res = dbquery("
		SELECT
			COUNT(user_id)
		FROM 
			users
		WHERE 
			user_observe!=''
		");
		$arr = mysql_fetch_row($res);
		echo "<tr><th class=\"tbltitle\">Beobachter:</th>";
		echo "<td class=\"tbldata\"";
		echo "><a href=\"?page=user&amp;sub=observed\"";
		if ($arr[0]>0) echo " style=\"font-weight:bold;color:#f90;\"";
		echo ">".$arr[0]." User</a> stehen unter Beobachtung</td></tr>";

			
		$res = dbquery("SELECT
			COUNT(user_id)
		FROM
			users
		WHERE
			user_profile_img_check=1;");
		$arr=mysql_fetch_row($res);
		if ($arr[0]>0)
		{
			echo "<tr><th class=\"tbltitle\">Profil-Bilder:</th>";
			echo "<td class=\"tbldata\">";
			echo "<a href=\"?page=user&amp;sub=imagecheck\" style=\"font-weight:bold;color:#f90;\">".$arr[0]." Spieler-Profilbilder</a> wurden noch nicht verifiziert. Gewisse Bilder könnten gegen die Regeln verstossen. <a href=\"?page=user&amp;sub=imagecheck\">Jetzt prüfen</a>";
			echo "</td></tr>";
		}

		$res = dbquery("SELECT
			COUNT(alliance_id)
		FROM
			alliances
		WHERE
			alliance_img_check=1;");
		$arr=mysql_fetch_row($res);
		if ($arr[0]>0)
		{
			echo "<tr><th class=\"tbltitle\">Profil-Bilder:</th>";
			echo "<td class=\"tbldata\">";
			echo "<a href=\"?page=alliances&amp;sub=imagecheck\" style=\"font-weight:bold;color:#f90;\">".$arr[0]." Allianz-Profilbilder</a> wurden noch nicht verifiziert. Gewisse Bilder könnten gegen die Regeln verstossen. <a href=\"?page=alliances&amp;sub=imagecheck\">Jetzt prüfen</a>";
			echo "</td></tr>";
		}

		tableEnd();		
		
		if (UNIX && !checkDaemonRunning($daemonPidfile))
		{
			err_msg("Der Backend-Dienst scheint nicht zu laufen!");
		}
		
		// Online

		$ures=dbquery("SELECT count(*) FROM users;");
		$uarr=mysql_fetch_row($ures);
		$up=$uarr[0]/$conf['enable_register']['p2'];
		$p1res=dbquery("SELECT count(*) FROM planets WHERE planet_user_id>0;");
		$p1arr=mysql_fetch_row($p1res);
		$p2res=dbquery("SELECT count(*) FROM planets;");
		$p2arr=mysql_fetch_row($p2res);
		if ($p2arr[0]>0)
			$pp=$p1arr[0]/$p2arr[0];
		else
			$pp=0;
		$s1res=dbquery("SELECT count(entities.cell_id) FROM entities,planets WHERE planets.id=entities.id AND planet_user_id>0 GROUP BY entities.cell_id;");
		$s1arr=mysql_num_rows($s1res);
		$s2res=dbquery("SELECT count(*) FROM entities WHERE code='s';");
		$s2arr=mysql_fetch_row($s2res);
		if ($s2arr[0]>0)
			$sp=$s1arr/$s2arr[0];
		else
			$sp=0;




		$gres=dbquery("SELECT COUNT(*) FROM user_sessions WHERE time_action>".(time() - $cfg->user_timeout->v).";");
		$garr=mysql_fetch_row($gres);
		if ($uarr[0]>0)
			$gp=$garr[0]/$uarr[0]*100;
		else
			$gp=0;
		$a1res=dbquery("SELECT COUNT(*)  FROM admin_user_sessions WHERE time_action>".(time() - $cfg->admin_timeout->v).";");
		$a1arr=mysql_fetch_row($a1res);
		$a2res=dbquery("SELECT COUNT(*)  FROM admin_users;");
		$a2arr=mysql_fetch_row($a2res);
		if ($a2arr[0]>0)
			$ap=$a1arr[0]/$a2arr[0]*100;
		else
			$ap=0;

		echo "<table class=\"tb\" style=\"width:auto;float:left;margin-right:20px;\">";
		echo "<tr><th colspan=\"3\">Online</th></tr>";
		if (UNIX)
		{
			echo "<tr><th><a href=\"?page=overview&amp;sub=daemon\">Backend:</a></th>";
			if ($pid = checkDaemonRunning($daemonPidfile))
				echo "<td colspan=\"2\" style=\"color:#0f0;\">Online, PID $pid</td>";
			else
				echo "<td colspan=\"2\" style=\"color:red;\">LÄUFT NICHT!</td>";
			echo "</tr>";
		}
		echo "<tr><th><a href=\"?page=user&amp;sub=sessions\">User:</a></th><td>".$garr[0]." / ".$uarr[0]."</td><td>".round($gp,1)."%</td></tr>";
		echo "<tr><th><a href=\"?page=overview&amp;sub=adminlog\">Admins:</a></th><td>".$a1arr[0]." / ".$a2arr[0]."</td><td>".round($ap,1)."%</td></tr>";
		echo "</table>";

		//
		// Auslastung
		//
		$g_style=" style=\"color:#0f0\"";
		$y_style=" style=\"color:#ff0\"";
		$o_style=" style=\"color:#fa0\"";
		$r_style=" style=\"color:#f55\"";

		echo "<div>";
		
		echo "<table class=\"tb\" style=\"width:auto;float:left;margin-right:20px;\">";
		echo "<tr><th colspan=\"3\">User-Statisik</th></tr>";
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

		echo "<table class=\"tb\" style=\"width:400px;float:left;margin-right:20px;\">";
		echo "<tr><th colspan=\"3\">System</th></tr>";
		if (UNIX)
		{
			$un=posix_uname();
			echo "<tr><th>System:</th><td>".$un['sysname']." ".$un['release']." ".$un['version']."</td></tr>";
		}
		echo "<tr><th>PHP:</th><td>".substr(phpversion(),0,10)."</td></tr>
		<tr><th>MySQL:</th><td>".mysql_get_client_info()."</td></tr>
		</table>";		
		echo "<br style=\"clear:both;\" /></div>";
	
		}
}
?>
