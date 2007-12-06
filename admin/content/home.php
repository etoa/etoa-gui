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
	// Statistics-Graph
	//
	if ($sub=="statsgraphs")
	{
		echo "<h1>Statistikgrafiken</h1>";
		echo "<h2>Online / Registrierte User</h2>";
		echo "<img src=\"../cache/out/userstats.png\" alt=\"Userstats\" />";
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
					echo "<table><tr><th class=\"tbltitle\">Login</th><th class=\"tbltitle\">Letzte Aktivit&auml;t</th><th class=\"tbltitle\">Logout</th><th class=\"tbltitle\">IP</th><th class=\"tbltitle\">Hostname</th><th class=\"tbltitle\">Session-Dauer</th>";
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
						echo "<td class=\"tbldata\">".$arr['log_hostname']."</td>";
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
				<th class=tbltitle>Ort</th>
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
					$loc = geoip_record_by_name($arr['user_ip']);
					echo "<td class=tbldata>".$loc['city']."</td>";
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
	// Statistiken
	//
	elseif ($sub=="stats")
	{	
		include("content/stats.php");
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
		advanced_form("admin_users");
	}	
	
	//
	// Passwort Ãƒâ€žndern
	//
	elseif ($sub=="change_pass")
	{
		include("inc/changepass.inc.php");
	}	
	
	//
	// ÃƒÅ“bersicht
	//
	else
	{
		$loc = geoip_record_by_name($s['user_last_ip']);
		echo "<h1>&Uuml;bersicht</h1>";
		echo "Hallo <b>".$s['user_nick']."</b>, willkommen im Administrationsmodus! Dein Rang ist <b>".$s['group_name']."</b><br/><br/>";
		success_msg("Dein letzter Login war am ".df($s['user_last_login']).", Host ".gethostbyaddr($s['user_last_host']).", IP ".$s['user_last_ip'].", Ort ".$loc['city'],1);

		//
		// Admin-News
		//
		if ($conf['admininfo']['v']!="")
		{
			infobox_start("Admin-News");   
			echo text2html($conf['admininfo']['v']);
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
		// Userliste
		//
		/*
		infobox_start("Admin-User-Liste",1);                              
		$res = dbquery("SELECT * FROM ".$db_table['admin_users']." ORDER BY user_nick ASC;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_array($res))
			{
					if ($arr['user_id']==$s['user_id'])
						$logindate = "letzer Login: ".date("d.m.Y H:i",$s['user_last_login']);
					elseif ($arr['user_last_login']>0)
						$logindate = "letzer Login: ".date("d.m.Y H:i",$arr['user_last_login']);
					else
						$logindate = "noch nie eingeloggt!";
					echo "<tr><td class=\"tbldata\" style=\"width:200px;\">".$arr['user_nick']."";
					if ($arr['user_locked']==1) echo " <span style=\"color:red\">gesperrt!</span>";
					echo "</td>";
					echo "<td class=\"tbldata\">".$admingroup[$arr['user_admin_rank']]."</td>";
					if (time()-TIMEOUT< $arr['user_acttime'] && $arr['user_session_key']!="")
						echo "<td class=\"tbldata\" style=\"color:#0f0\">Online</td>";						
					else
						echo "<td class=\"tbldata\" style=\"color:#f72\">Offline</td>";						
					echo "<td class=\"tbldata\">$logindate</td></tr>";
			}
		}
		infobox_end(1); */
		
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
		// Änderungsanfragen
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
		
		cache::checkPerm("","../");
		
			
}
?>
