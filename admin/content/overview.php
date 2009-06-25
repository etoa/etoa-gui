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
			/*if (isset($_GET['action']) && $_GET['action']=="daeomonrestart")
			{
				echo "<div><div style=\"background:#000;padding:6px;border:1px solid #fff\">";
				$cmd = $daemonExe." -r ".$cfg->daemonIdentifier->v." -k";
				echo $cmd."<br/>";
				passthru($cmd);
				echo "</div><br/>";
			}*/


			$frm = new Form("bustn","?page=$page&amp;sub=$sub");
			echo $frm->begin();
			
			tableStart("Daemon-Infos");
			$un=posix_uname();
			echo "<tr><th>System</th><td>".$un['sysname']." ".$un['release']." ".$un['version']."</td></tr>";
			echo "<tr><th>Daemon-ID</th><td>".$daemonId."</td></tr>";
			echo "<tr><th>Pfad</th><td>".$daemonExe."</td></tr>";
			echo "<tr><th>Logfile</th><td>".$daemonLogfile."</td></tr>";
			echo "<tr><th>Pidfile</th><td>".$daemonPidfile."</td></tr>";
			echo "<tr><th>Status</th><td>";			
			/*if ($pid = checkDaemonRunning($daemonPidfile))
			{
				echo "<div style=\"color:#0f0;\">Der Backend-Dienst läuft mit PID $pid!
				&nbsp; <input type=\"button\" value=\"Neu starten\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=daeomonrestart'\" />
				</div>";
			}	
			else
			{
				echo "<div style=\"color:red;\">Der Backend-Dienst scheint nicht zu laufen!
				&nbsp; <input type=\"button\" value=\"Neu starten\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=daeomonrestart'\" /></div>";
			}*/
			echo "</td></tr>";			
			tableEnd();
			echo $frm->close();		
		
		
			echo "<h2>Log</h2>";
			if (is_file($daemonLogfile))
			{
				echo "<textarea style=\"height:400px;width:100%\" id=\"logtextarea\" readonly=\"readonly\">";
				readfile($daemonLogfile);
				echo "</textarea>
				<script type=\"text/javascript\">
				textareaelem = document.getElementById('logtextarea');
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
			$ures=dbquery("SELECT user_nick FROM admin_users WHERE user_id=".$_POST['user_id'].";");
			if (mysql_num_rows($ures)>0)
			{
				$uarr=mysql_fetch_array($ures);
				echo "<h2>Session-Log f&uuml;r ".$uarr['user_nick']."</h2>";

				$res=dbquery("SELECT * FROM admin_user_log WHERE log_user_id=".$_POST['user_id']." ORDER BY log_id DESC;");
				if (mysql_num_rows($res)>0)
				{
					echo "<table><tr><th class=\"tbltitle\">Login</th><th class=\"tbltitle\">Letzte Aktivit&auml;t</th>
					<th class=\"tbltitle\">Logout</th>
					<th class=\"tbltitle\">IP</th>
					<th class=\"tbltitle\">Hostname</th>
					<th class=\"tbltitle\">Session-Dauer</th>";
					while ($arr=mysql_fetch_array($res))
					{
						echo "<tr><td class=\"tbldata\">".date("d.m.Y H:i",$arr['log_logintime'])."</td>";
						echo "<td class=\"tbldata\">";
						if ($arr['log_acttime']>0)
							echo date("d.m.Y H:i",$arr['log_acttime']);
						else
							echo "-";						
						echo "</td>";
						echo "<td class=\"tbldata\">";						
						if ($arr['log_logouttime']>0)
							echo date("d.m.Y H:i",$arr['log_logouttime']);
						else
							echo "-";						
						echo "</td>";
						echo "<td class=\"tbldata\">".$arr['log_ip']."</td>";
						echo "<td class=\"tbldata\">".Net::getHost($arr['log_hostname'])."</td>";
						echo "<td class=\"tbldata\">";
						if (max($arr['log_logouttime'],$arr['log_acttime'])-$arr['log_logintime']>0)
							echo tf(max($arr['log_logouttime'],$arr['log_acttime'])-$arr['log_logintime']);
						else
							echo "-";
						if ($arr['log_session_key']==$s->id)
							echo " <span style=\"color:#0f0\">aktiv</span>";
						echo "</td></tr>";
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
		
			if (isset($_GET['kick']) && $_GET['kick']>0 && $_GET['kick']!=$cu->id)
			{
				dbquery("UPDATE admin_users SET user_session_key='' WHERE user_id=".$_GET['kick'].";");	
				add_log(8,$cu->nick." l&ouml;scht die Session des Administrators mit der ID ".$_GET['kick'],time());
			}
			
			if (isset($_POST['delentrys']) && $_POST['delentrys']!="")
			{
				$tstamp = time()-$_POST['log_timestamp'];
				dbquery("DELETE FROM admin_user_log WHERE log_logintime<$tstamp;");
				echo mysql_affected_rows()." Eintr&auml;ge wurden gel&ouml;scht!<br/><br/>";
				add_log(8,$cu->nick." l&ouml;scht ".mysql_affected_rows()." Eintr&auml;ge des Admin-Session-Logs",time());
			}			
			
			echo "<h2>Aktive Sessions / Zuletzt aktiv</h2>";
			echo "Das Timeout betr&auml;gt ".TIMEOUT." Sekunden<br/><br/>";
			$res=dbquery("SELECT * FROM admin_users WHERE user_acttime>0 ORDER BY user_acttime DESC;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table><tr><th class=\"tbltitle\">Nick</th>
				<th class=\"tbltitle\">Login</th>
				<th class=\"tbltitle\">Letzte Aktion</th>
				<th class=\"tbltitle\">Status</th>
				<th class=\"tbltitle\">IP</th>
				<th class=\"tbltitle\">Hostname</th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".$arr['user_nick']."</td>
					<td class=\"tbldata\">".date("d.m.Y H:i",$arr['user_last_login'])."</td><td class=\"tbldata\">".date("d.m.Y  H:i",$arr['user_acttime'])."</td>";
					if (time()-TIMEOUT< $arr['user_acttime'] && $arr['user_session_key']!="")
					{
						echo "<td class=\"tbldata\" style=\"color:#0f0\">Online";
						if ($arr['user_id']!=$cu->id)
							echo " [<a href=\"?page=$page&amp;sub=$sub&amp;kick=".$arr['user_id']."\">kick</a>]</td>";
					}
					else
						echo "<td class=\"tbldata\" style=\"color:#f72\">offline</td>";
					echo "<td class=\"tbldata\">".$arr['user_ip']."</td>";
					echo "<td class=\"tbldata\">".Net::getHost($arr['user_ip'])."</td>";
					echo "</tr>";
					
				}			
				echo "</table>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden!</i>";
			
			echo "<h2>Session-Log</h2>";
			$res=dbquery("SELECT user_nick,user_id,COUNT(*) as cnt FROM admin_users,admin_user_log WHERE log_user_id=user_id GROUP BY user_id ORDER BY user_nick;");
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "Benutzer w&auml;hlen: <select name=\"user_id\">";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']." (".$arr['cnt']." Sessions)</option>";
				}
				echo "</select> &nbsp; <input type=\"submit\" name=\"logshow\" value=\"Anzeigen\" /></form>";
				$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM admin_user_log;"));
				echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";					
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden</i>";		
				
			echo "<h2>Logs l&ouml;schen</h2>";
			echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
			echo "Eintr&auml;ge l&ouml;schen die &auml;lter als <select name=\"log_timestamp\">";
			echo "<option value=\"604800\" selected=\"selected\">1 Woche</option>";
			echo "<option value=\"1209600\">2 Wochen</option>";
			echo "<option value=\"2419200\">4 Wochen</option>";
			echo "</select> sind: <input type=\"submit\" name=\"delentrys\" value=\"Ausf&uuml;hren\" /></form>";				
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
		
		
		$res = dbquery("
		SELECT 
			user_force_pwchange 
		FROM 
			admin_users
		WHERE
			user_id=".$cu->id.";");
		$arr = mysql_fetch_row($res);
		if ($arr[0]==1)
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

		tableStart("Schnellsuche");
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

	
		tableStart("Spieler-Tools");
		

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
		
	
		}
}
?>
