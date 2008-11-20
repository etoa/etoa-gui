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
		
		if ($_GET['off']==1)
		{
			$cfg->set('offline',1);
		}
		if ($_GET['on']==1)
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
	// Tipps
	//
	elseif ($sub=="tipps")
	{
		advanced_form("tipps");
	}
	
	
	//
	// Filesharing
	//
	elseif ($sub=="filesharing")
	{
		$root = ADMIN_FILESHARING_DIR; 
	
	echo "<h2>Filesharing</h2>";
	
	if (isset($_GET['action']) && $_GET['action']=="rename")
	{
		$f = base64_decode($_GET['file']);
		if (md5($f) == $_GET['h'])
		{
			echo "<h2>Umbenennen</h2>
			<form action=\"?page=$page&sub=$sub\" method=\"post\">";
			echo "Dateiname: 
			<input type=\"text\" name=\"rename\" value=\"".$f."\" /> 
			<input type=\"hidden\" name=\"rename_old\" value=\"".$f."\" /> 
			&nbsp; <input type=\"submit\" name=\"rename_submit\" value=\"Umbenennen\" /> &nbsp; 
			</form>";
		}
		else
		{
			echo "Fehler im Dateinamen!";
		}		
	}
	else
	{
		if (isset($_FILES["datei"])) 
		{
		 	if(move_uploaded_file($_FILES["datei"]['tmp_name'],$root."/".$_FILES["datei"]['name']))
		 	{
	  		echo "Die Datei <b>".$_FILES["datei"]['name']."</b> wurde heraufgeladen!<br/><br/>";
	  	}
	  	else
	  	{
	  		echo "Fehler beim Upload!<br/><br/>";
	  	}
	  }
	  
	  if (isset($_POST['rename_submit']) && $_POST['rename']!="")
	  {
	  	rename($root."/".$_POST['rename_old'],$root."/".$_POST['rename']);
	  	echo "Datei wurde umbenannt!<br/><br/>";
	  }	  
		
		if (isset($_GET['action']) && $_GET['action']=="delete")
		{
			$f = base64_decode($_GET['file']);
			if (md5($f) == $_GET['h'])
			{
		  	@unlink($root."/".$f);
		  	echo "Datei wurde gelöscht!<br/><br/>";
			}
			else
			{
				echo "Fehler im Dateinamen!";
			}				
		}
		
		if ($d = opendir($root))
		{
			$cnt = 0;
			echo "<table class=\"tb\">
			<tr>
				<th>Datei</th>
				<th>Grösse</th>
				<th>Datum</th>
				<th style=\"width:150px;\">Optionen</th>
			</tr>";
			while ($f = readdir($d))
			{
				$file = $root."/".$f;
				if (is_file($file) && substr($f,0,1)!=".")
				{
					$dlink = "path=".base64_encode($file)."&hash=".md5($file);
					$link = "file=".base64_encode($f)."&h=".md5($f);
					echo "<tr>
						<td><a href=\"dl.php?".$dlink."\">$f</a></td>
						<td>".byte_format(filesize($file))."</td>
						<td>".df(filemtime($file))."</td>
						<td>
							<a href=\"?page=$page&amp;sub=$sub&amp;action=rename&".$link."\">Umbenennen</a>
							<a href=\"?page=$page&amp;sub=$sub&amp;action=delete&".$link."\" onclick=\"return confirm('Soll diese Datei wirklich gelöscht werden?')\">Löschen</a>
						</td>
					</tr>";				
					$cnt++;
				}			
			}
			if ($cnt==0)
			{
				echo "<tr><td colspan=\"4\"><i>Keine Dateien vorhanden!</i></td></tr>";
			}
			echo "</table>";
			closedir($d);
			
			echo "<h2>Upload</h2>
			<form method=\"post\" action=\"?page=$page&sub=$sub\" enctype=\"multipart/form-data\">
	    	<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"10000000\" />
	  		<input type=\"file\" name=\"datei\" size=\"40\" maxlength=\"10000000\" />
	  		<input type=\"submit\" name=\"submit\" value=\"Datei heraufladen\" />
			</form>		
			";		
		}
		else
		{
			echo "Verzeichnis $root kann nicht gefunden werden!";
		}
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
						echo "<td class=\"tbldata\">".resolveIp($arr['log_hostname'])."</td>";
						echo "<td class=\"tbldata\">";
						if (max($arr['log_logouttime'],$arr['log_acttime'])-$arr['log_logintime']>0)
							echo tf(max($arr['log_logouttime'],$arr['log_acttime'])-$arr['log_logintime']);
						else
							echo "-";
						if ($arr['log_session_key']==$s['key'])
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
		
			if (isset($_GET['kick']) && $_GET['kick']>0 && $_GET['kick']!=$s['user_id'])
			{
				dbquery("UPDATE admin_users SET user_session_key='' WHERE user_id=".$_GET['kick'].";");	
				add_log(8,$s['user_nick']." l&ouml;scht die Session des Administrators mit der ID ".$_GET['kick'],time());
			}
			
			if (isset($_POST['delentrys']) && $_POST['delentrys']!="")
			{
				$tstamp = time()-$_POST['log_timestamp'];
				dbquery("DELETE FROM admin_user_log WHERE log_logintime<$tstamp;");
				echo mysql_affected_rows()." Eintr&auml;ge wurden gel&ouml;scht!<br/><br/>";
				add_log(8,$s['user_nick']." l&ouml;scht ".mysql_affected_rows()." Eintr&auml;ge des Admin-Session-Logs",time());
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
						if ($arr['user_id']!=$s['user_id'])
							echo " [<a href=\"?page=$page&amp;sub=$sub&amp;kick=".$arr['user_id']."\">kick</a>]</td>";
					}
					else
						echo "<td class=\"tbldata\" style=\"color:#f72\">offline</td>";
					echo "<td class=\"tbldata\">".$arr['user_ip']."</td>";
					echo "<td class=\"tbldata\">".resolveIp($arr['user_ip'])."</td>";
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
		echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />";
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
    echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
		$res = dbquery("SELECT * FROM config WHERE config_name='system_message';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			echo "Erscheint sofort auf jeder Seite im Spiel:<br/><br/>";
			if ($arr['config_value']!="")
			{
				iBoxStart("Vorschau");
				echo text2html($arr['config_value']);
				iBoxEnd();
			}
			echo "<textarea name=\"config_value\" cols=\"100\" rows=\"15\">".$arr['config_value']."</textarea><br/><br/>";
			echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />";
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
			echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />";
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
		
		if (!isset($s['home_visited']))
		{
			echo "Hallo <b>".$s['user_nick']."</b>, willkommen im Administrationsmodus! Dein Rang ist <b>".$s['group_name']."</b><br/>";
			echo "<span style=\"color:#0f0;\">Dein letzter Login war <b>".df($s['user_last_login'])."</b>, Host: <b>".resolveIp($s['user_last_host'])."</b> (aktuell: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."), IP: <b>".$s['user_last_ip']."</b> (aktuell: ".$_SERVER['REMOTE_ADDR'].")</span><br/><br/>";
			$s['home_visited']=true;
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
			<input type=\"button\" value=\"Weiter zum Urknall\" onclick=\"document.location='?page=config&sub=uni'\" /></div><br/><br/>";
		}
		else
		{				
		
		
		$res = dbquery("
		SELECT 
			user_force_pwchange 
		FROM 
			admin_users
		WHERE
			user_id=".$s['user_id'].";");
		$arr = mysql_fetch_row($res);
		if ($arr[0]==1)
		{
			iBoxStart("Passwort");   
			echo "<span style=\"color:#f90;\">Dein Passwort wurde seit der letzten automatischen Generierung noch nicht geÃ¤ndert. Bitte mache das jetzt <a href=\"?myprofile=1\">hier</a>!</span>";
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
		
		/*
		// ÃƒÆ’Ã¢â‚¬Å¾nderungsanfragen
		$res=dbquery("
		SELECT 
			COUNT(request_id)
		FROM 
			user_requests
		WHERE 
			request_handled=0
		ORDER BY 
			request_timestamp ASC;");
		$arr=mysql_fetch_row($res);
		echo "<tr><th class=\"tbltitle\" style=\"width:200px;\">&Auml;nderungsanfragen:</th>";
		echo "<td class=\"tbldata\"";
		echo "><a href=\"?page=user&amp;sub=requests\"";
		if ($arr[0]>0) echo " style=\"font-weight:bold;color:#f90;\"";	
		echo ">".$arr[0]." Anfragen</a> vorhanden</td></tr>";
		*/
		
		// Tickets		
		$res = dbquery("
		SELECT		
			COUNT(id)
		FROM
			tickets
		WHERE
			status=0	
		;");
		$arr=mysql_fetch_row($res);
		$res2 = dbquery("
		SELECT		
			COUNT(id)
		FROM
			tickets
		WHERE
			status=1
			AND admin_id=".$s['user_id']."	
		;");
		$arr2=mysql_fetch_row($res2);		
		echo "<tr><th class=\"tbltitle\">Ticket-System:</th>";
		echo "<td class=\"tbldata\"";
		echo ">
		<a href=\"?page=user&amp;sub=tickets\"";
		if ($arr[0]>0) echo " style=\"font-weight:bold;color:#f90;\"";			
		echo " onclick=\"window.open('popup.php?page=tickets','Tickets','width=700, height=600, status=no, scrollbars=yes')\"
		>".$arr[0]." neue Tickets</a> vorhanden";
		if ($arr2[0]>0) echo ", <a href=\"?page=user&amp;sub=tickets\">".$arr2[0]." offene Tickets</a> vorhanden";
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

		tableEnd();		
		
		Cache::checkPerm();
		
		}
}
?>
