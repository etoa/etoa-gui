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
	// RSS
	//
	if ($sub=="rss")
	{
		echo "<h1>RSS-Feeds</h1>";
	
		if (isset($_GET['action']) && $_GET['action']=="gen_townhall")
		{
			Townhall::genRss();
			success_msg("RSS erstellt!");
		}

		echo "<h2>Feeds (neu) generieren</h2>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;action=gen_townhall\">Rathaus-Feed generieren</a>";

	
		echo "<br/><br/><h2>Feedliste</h2>";
		infobox_start("Vorhandene Feeds",1);
		Rss::showOverview();
		infobox_end(1);
		
	}
	
	//
	// Rangliste
	//
	elseif ($sub=="stats")
	{
		require("home/stats.inc.php");
	}
	
	//
	// Filesharing
	//
	elseif ($sub=="filesharing")
	{
		$root = "../".ADMIN_FILESHARING_DIR; 
	
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
				if (is_file($file) && $file==".htaccess")
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
			$ures=dbquery("SELECT user_nick FROM ".$db_table['admin_users']." WHERE user_id=".$_POST['user_id'].";");
			if (mysql_num_rows($ures)>0)
			{
				$uarr=mysql_fetch_array($ures);
				echo "<h2>Session-Log f&uuml;r ".$uarr['user_nick']."</h2>";

				$res=dbquery("SELECT * FROM ".$db_table['admin_user_log']." WHERE log_user_id=".$_POST['user_id']." ORDER BY log_id DESC;");
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
						echo "<td class=\"tbldata\">".gethostbyaddr($arr['log_hostname'])."</td>";
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
				dbquery("UPDATE ".$db_table['admin_users']." SET user_session_key='' WHERE user_id=".$_GET['kick'].";");	
				add_log(8,$s['user_nick']." l&ouml;scht die Session des Administrators mit der ID ".$_GET['kick'],time());
			}
			
			if (isset($_POST['delentrys']) && $_POST['delentrys']!="")
			{
				$tstamp = time()-$_POST['log_timestamp'];
				dbquery("DELETE FROM ".$db_table['admin_user_log']." WHERE log_logintime<$tstamp;");
				echo mysql_affected_rows()." Eintr&auml;ge wurden gel&ouml;scht!<br/><br/>";
				add_log(8,$s['user_nick']." l&ouml;scht ".mysql_affected_rows()." Eintr&auml;ge des Admin-Session-Logs",time());
			}			
			
			echo "<h2>Aktive Sessions / Zuletzt aktiv</h2>";
			echo "Das Timeout betr&auml;gt ".TIMEOUT." Sekunden<br/><br/>";
			$res=dbquery("SELECT * FROM ".$db_table['admin_users']." WHERE user_acttime>0 ORDER BY user_acttime DESC;");
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
					echo "<td class=\"tbldata\">".gethostbyaddr($arr['user_ip'])."</td>";
					echo "</tr>";
					
				}			
				echo "</table>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden!</i>";
			
			echo "<h2>Session-Log</h2>";
			$res=dbquery("SELECT user_nick,user_id,COUNT(*) as cnt FROM ".$db_table['admin_users'].",".$db_table['admin_user_log']." WHERE log_user_id=user_id GROUP BY user_id ORDER BY user_nick;");
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "Benutzer w&auml;hlen: <select name=\"user_id\">";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']." (".$arr['cnt']." Sessions)</option>";
				}
				echo "</select> &nbsp; <input type=\"submit\" name=\"logshow\" value=\"Anzeigen\" /></form>";
				$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['admin_user_log'].";"));
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
		if (isset($_POST['save']))
		{
				dbquery("UPDATE ".$db_table['config']." SET config_value='".$_POST['config_value']."' WHERE config_name='info';");
		}
		echo "<h1>Ingame-News</h1>";
		
    echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
		$res = dbquery("SELECT * FROM ".$db_table['config']." WHERE config_name='info';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			echo "Diese News erscheinen auf der Startseite im Game:<br/><br/>";
			if ($arr['config_value']!="")
			{
				infobox_start("Vorschau");
				echo text2html($arr['config_value']);
				infobox_end();
			}
			echo "<textarea name=\"config_value\" cols=\"100\" rows=\"15\">".$arr['config_value']."</textarea><br/><br/>";
			echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />";
		}
		else
			echo "Es ist kein Datensatz vorhanden!";
		echo "</form>";	
	}	
	

	//
	// System-Nachricht
	//
	elseif ($sub=="systemmessage")
	{
		if (isset($_POST['save']))
		{
				dbquery("UPDATE ".$db_table['config']." SET config_value='".$_POST['config_value']."' WHERE config_name='system_message';");
		}
		echo "<h1>Systemnachricht</h1>";
		
    echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
		$res = dbquery("SELECT * FROM ".$db_table['config']." WHERE config_name='system_message';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			echo "Erscheint sofort auf jeder Seite im Spiel:<br/><br/>";
			if ($arr['config_value']!="")
			{
				infobox_start("Vorschau");
				echo text2html($arr['config_value']);
				infobox_end();
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
				dbquery("UPDATE ".$db_table['config']." SET config_value='".$_POST['config_value']."' WHERE config_name='admininfo';");
		}
		echo "<h1>Ingame-News</h1>";
		echo "Diese News erscheinen auf der Startseite des Adminmodus:<br/><br/>";
    echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
		$res = dbquery("SELECT * FROM ".$db_table['config']." WHERE config_name='admininfo';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			if ($arr['config_value']!="")
			{
				infobox_start("Vorschau");
				echo text2html($arr['config_value']);
				infobox_end();
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
		
		if (!$s['home_visited'])
		{
			echo "Hallo <b>".$s['user_nick']."</b>, willkommen im Administrationsmodus! Dein Rang ist <b>".$s['group_name']."</b><br/>";
			echo "<span style=\"color:#0f0;\">Dein letzter Login war <b>".df($s['user_last_login'])."</b>, Host: <b>".gethostbyaddr($s['user_last_host'])."</b> (aktuell: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."), IP: <b>".$s['user_last_ip']."</b> (aktuell: ".$_SERVER['REMOTE_ADDR'].")</span><br/><br/>";
			$s['home_visited']=true;
		}
		
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
			infobox_start("Passwort");   
			echo "<span style=\"color:#f90;\">Dein Passwort wurde seit der letzten automatischen Generierung noch nicht geÃ¤ndert. Bitte mache das jetzt <a href=\"?myprofile=1\">hier</a>!</span>";
			infobox_end();			
		}
		
		
		//
		// Admin-News
		//
		if ($conf['admininfo']['v']!="")
		{
			infobox_start("Admin-News");   
			echo text2html($conf['admininfo']['v']);
			infobox_end();			
		}

		// Warnung falls User-Bilder schon lange nicht mehr geprueft wurden
		if (intval($conf['profileimagecheck_done']['v']) < time()-(24*3600*7))
		{
			infobox_start("Spieler-Profilbilder");
			echo "Die Spieler-Profilbilder wurden schon lange nicht mehr geprüft! <a href=\"?page=user&amp;sub=imagecheck\">Jetzt prüfen</a>";
			infobox_end();
			
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
			infobox_start("Flottensperre aktiviert");
			echo "Die Flottensperre ist aktiviert. Es kÃƒÂ¶nnen keine FlÃƒÂ¼ge gestartet werden!<br><br><b>Status:</b> ".$flightban_time_status."<br><b>Zeit:</b> ".date("d.m.Y H:i",$conf['flightban_time']['p1'])." - ".date("d.m.Y H:i",$conf['flightban_time']['p2'])."<br><b>Grund:</b> ".$conf['flightban']['p1']."<br><br>";
			echo "Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
			infobox_end();
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
			infobox_start("Kampfsperre aktiviert");
			echo "Die Kampfsperre ist aktiviert. Es kÃƒÂ¶nnen keine Angriffe geflogen werden!<br><br><b>Status:</b> ".$battleban_time_status."<br><b>Zeit:</b> ".date("d.m.Y H:i",$conf['battleban_time']['p1'])." - ".date("d.m.Y H:i",$conf['battleban_time']['p2'])."<br><b>Grund:</b> ".$conf['battleban']['p1']."<br><br>";
			echo "Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
			infobox_end();
		}

		if ($conf['system_message']['v']!="")
		{
			echo "<br/>";
			infobox_start("<span style=\"color:red;\">Folgende Systemnachricht ist zurzeit aktiviert (<a href=\"?page=$page&amp;sub=systemmessage\">Bearbeiten/Deaktivieren</a>):</span>");
			echo text2html($conf['system_message']['v']);
			infobox_end();			
		}

		//
		// Universum generieren
		//
		if (mysql_num_rows(dbquery("SELECT cell_id FROM ".$db_table['space_cells']." LIMIT 1;"))==0)
		{
			echo "<h2>Universum existiert noch nicht!</h2>";
			echo "<div style=\"color:red;\">Das Universum wurde noch nicht <a href=\"?page=config\">generiert</a>!</div><br/><br/>";
		}			
		
		//
		// Schnellsuche
		//
		$_SESSION['planets']['query']=Null;
		$_SESSION['admin']['user_query']="";
		$_SESSION['admin']['queries']['alliances']="";

		infobox_start("Schnellsuche",1);
		echo "<form action=\"?page=user&amp;action=search\" method=\"post\"><tr><th class=\"tbltitle\">Nick:</th>";
		echo "<td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" size=\"40\" /> <input type=\"hidden\" name=\"qmode[user_nick]\" value=\"LIKE '%\" /><input type=\"submit\" name=\"user_search\" value=\"Suchen\" /></td></tr></form>";

		echo "<form action=\"?page=galaxy&amp;action=searchresults\" method=\"post\"><tr><th class=\"tbltitle\">Planet:</th>";
		echo "<td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" size=\"40\" /> <input type=\"hidden\" name=\"qmode[planet_name]\" value=\"LIKE '%\" /><input type=\"hidden\" name=\"planet_user_main\" value=\"2\" /><input type=\"hidden\" name=\"planet_wf\" value=\"2\" /><input type=\"submit\" name=\"planet_search\" value=\"Suchen\" /></td></tr></form>";

		echo "<form action=\"?page=galaxy&amp;action=searchresults\" method=\"post\"><tr><th class=\"tbltitle\">Planet-Besitzer:</th>";
		echo "<td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" size=\"40\" /> <input type=\"hidden\" name=\"qmode[user_nick]\" value=\"LIKE '%\" /><input type=\"hidden\" name=\"planet_user_main\" value=\"2\" /><input type=\"hidden\" name=\"planet_wf\" value=\"2\" /><input type=\"submit\" name=\"planet_search\" value=\"Suchen\" /></td></tr></form>";

		echo "<form action=\"?page=alliances&amp;action=search\" method=\"post\"><tr><th class=\"tbltitle\">Allianz-Tag:</th>";
		echo "<td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" size=\"40\" /> <input type=\"hidden\" name=\"qmode[alliance_tag]\" value=\"LIKE '%\" /><input type=\"submit\" name=\"alliance_search\" value=\"Suchen\" /></td></tr></form>";
		infobox_end(1);
		echo "<script>document.forms[1].elements[0].focus()</script>";

	
		infobox_start("Spieler-Tools",1);
		// ÃƒÆ’Ã¢â‚¬Å¾nderungsanfragen
		$res=dbquery("
		SELECT 
			COUNT(request_id)
		FROM 
			".$db_table['user_requests']."
		WHERE 
			request_handled=0
		ORDER BY 
			request_timestamp ASC;");
		$arr=mysql_fetch_row($res);
		echo "<tr><th class=\"tbltitle\" style=\"width:200px;\">&Auml;nderungsanfragen:</th>";
		echo "<td class=\"tbldata\"";
		if ($arr[0]>0) echo " style=\"background:#880;\"";	
		echo "><a href=\"?page=user&amp;sub=requests\">".$arr[0]." Anfragen</a> vorhanden</td></tr>";
		
		// Tickets		
		$res = dbquery("
		SELECT		
			COUNT(abuse_id)
		FROM
			abuses
		WHERE
			abuse_status=0	
		;");
		$arr=mysql_fetch_row($res);
		$res2 = dbquery("
		SELECT		
			COUNT(abuse_id)
		FROM
			abuses
		WHERE
			abuse_status=1
			AND abuse_admin_id=".$s['user_id']."	
		;");
		$arr2=mysql_fetch_row($res2);		
		echo "<tr><th class=\"tbltitle\">Ticket-System:</th>";
		echo "<td class=\"tbldata\"";
		if ($arr[0]>0) echo " style=\"background:#880;\"";			
		echo "><a href=\"?page=user&amp;sub=tickets\">".$arr[0]." neue Tickets</a> vorhanden";
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
		if ($arr[0]>0) echo " style=\"background:#880;\"";
		echo "><a href=\"?page=user&amp;sub=observed\">".$arr[0]." User stehen unter Beobachtung</a></td></tr>";
		infobox_end(1);		
		
		cache::checkPerm();
		
			
}
?>
