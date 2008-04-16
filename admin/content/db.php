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
	// 	Dateiname: db.php
	// 	Topic: Datebank-Administration
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//
	
	echo "<h1>Datenbank</h1>";

	//
	// Updates
	//
	if($sub=='updates')
	{
		echo '<h2>Updates</h2>';
		
		// Ressourcen
		if ($_GET['action']=="resources")
		{
			if ($conf['updating']['v']==0)
			{
        // Update-Flag setzen
        dbquery("UPDATE ".$db_table['config']." SET config_value=1,config_param2=".time()." WHERE config_name='updating';");
        $num = updateAllEconomy();
        echo "Ressourcen und Speicher wurden auf ".$num." Planeten aktualisiert!<br/><br/>";
				// Update-Flag löschen
				dbquery("UPDATE ".$db_table['config']." SET config_value=0 WHERE config_name='updating';");              
      }
      else
      {
       	echo "Es kann momentan kein Update getätigt werden, da der Server zurzeit andere Updates am abarbeiten ist!<br>";
      }
		}
		
		// Felder
		elseif ($_GET['action']=="fields")
		{
			if ($conf['updating']['v']==0)
			{
	      // Update-Flag setzen
	      dbquery("UPDATE ".$db_table['config']." SET config_value=1,config_param2=".time()." WHERE config_name='updating';");
	      $num = fieldupdate();
	      echo "Geb&auml;ude-Feldinformationen wurden verarbeitet.<br/>$num[0] Planeten aktuallisiert!<br/><br/>";
	      echo "Verteidigungs-Feldinformationen wurden verarbeitet.<br/>$num[1] Planeten aktualisiert!<br/><br/>";
	      $d = round($num[2]/($num[1]+$num[0]),2);
	      echo "Es sind durchschnittlich $d Felder belegt!";
				// Update-Flag löschen
				dbquery("UPDATE ".$db_table['config']." SET config_value=0 WHERE config_name='updating';");            
			}
      else
      {
      	echo "Es kann momentan kein Update getätigt werden, da der Server zurzeit andere Updates am abarbeiten ist!<br>";
      }	
		}

		// Speicher
		elseif ($_GET['action']=="store")
		{
			if ($conf['updating']['v']==0)
			{
	      // Update-Flag setzen
	      dbquery("UPDATE ".$db_table['config']." SET config_value=1,config_param2=".time()." WHERE config_name='updating';");
				$num = storeupdate(true);
				echo "Speicherinformationen von $num[1] Geb&auml;uden wurden auf $num[0] Planeten aktualisiert!<br/><br/>";
				// Update-Flag löschen
				dbquery("UPDATE ".$db_table['config']." SET config_value=0 WHERE config_name='updating';");            
     	}
      else
      {
      	echo "Es kann momentan kein Update getätigt werden, da der Server zurzeit andere Updates am abarbeiten ist!<br>";
      }			
		}
		
		// Punkte aktualisieren
		elseif ($_GET['action']=="points")
		{
			if ($conf['updating']['v']==0)
			{
	    	// Update-Flag setzen
	    	dbquery("UPDATE ".$db_table['config']." SET config_value=1,config_param2=".time()." WHERE config_name='updating';");
	    	$num = Ranking::calc(true);
	    	Ranking::calcTitles();
	    	echo "Die Punkte von ".$num[0]." Spielern wurden aktualisiert!<br/>";
	    	$d = $num[1]/$num[0];
	    	echo "Ein Spieler hat durchschnittlich ".nf($d)." Punkte!<br/><br/>";
	    	echo "<a href=\"?page=home&amp;sub=stats\">Resultat</a><br/><br/>";
				// Update-Flag löschen
				dbquery("UPDATE ".$db_table['config']." SET config_value=0 WHERE config_name='updating';");            
	    }
	    else
	    {
	    	echo "Es kann momentan kein Update getätigt werden, da der Server zurzeit andere Updates am abarbeiten ist!<br/><br/>";
	  	}		
		}

		// Markt aktualisieren
		elseif ($_GET['action']=="market")
		{
			if ($conf['updating']['v']==0)
			{
	      // Update-Flag setzen
	      dbquery("UPDATE ".$db_table['config']." SET config_value=1,config_param2=".time()." WHERE config_name='updating';");
	      market_update();
	      echo "Die Marktangebote wurden Aktualisiert und ausstehende Warenversendungen wurden gemacht!<br/><br/>";
				// Update-Flag löschen
				dbquery("UPDATE ".$db_table['config']." SET config_value=0 WHERE config_name='updating';");            
       }
       else
       {
       	echo "Es kann momentan kein Update getätigt werden, da der Server zurzeit andere Updates am abarbeiten ist!<br>";
    	}		
		}
			
		echo '<b>Punkte updaten:</b> 
		Die Punkte aller Spieler aktualisieren 
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=points\'" /><br/><br/>';
		echo '<b>Markt updaten:</b> 
		Fertige Auktionen und Angebote abschliessen
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=market\'" /><br/><br/>';
		echo '<b>Felder updaten:</b> 
		Felder der Planeten neu berechnen
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=fields\'" /><br/><br/>';
		echo '<b>Lager updaten:</b> 
		Lagerkapazitäten neu berechnen
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=store\'" /><br/><br/>';
		echo '<b>Ressourcen updaten:</b> 
		Ressourcen auf allen Planeten neu berechnen.
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=resources\'" /><br/><br/>';
		
	}

	//
	// Datenbanktabellen optimieren
	//
	elseif ($sub=="optimize")
	{
		echo '<h2>Optimierungsbericht</h2>';
		echo '<input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" /><br/><br/>';
		$ores = optimize_tables(true);
		db_show_result($ores);
		echo '<br/><input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" />';
	}
	
	//
	// Datenbanktabellen reparieren
	//
	elseif ($sub=="repair")
	{
		echo '<h2>Reparaturbericht</h2>';
		echo '<input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" /><br/><br/>';
		$ores = repair_tables(true);
		db_show_result($ores);
		echo '<br/><input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" />';
	}

	//
	// Datenbanktabellen reparieren
	//
	elseif ($sub=="analyze")
	{
		echo '<h2>Analysebericht</h2>';
		echo '<input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" /><br/><br/>';
		$ores = analyze_tables(true);
		db_show_result($ores);
		echo '<br/><input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" />';
	}
	
	//
	// Datenbanktabellen reparieren
	//
	elseif ($sub=="check")
	{
		echo '<h2>Überprüfungsbericht</h2>';
		echo '<input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" /><br/><br/>';
		$ores = check_tables(true);
		db_show_result($ores);
		echo '<br/><input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" />';
	}			
	

	//
	// Backups anzeigen
	//
	elseif ($sub=="backup")
	{
		require("db/backup.inc.php");
	}

	//
	// Clean-Up
	//
	elseif($sub=='cleanup')
	{
		echo '<h2>Clean-Up</h2>';

		/* Proces actions */
		if (isset($_POST['submit_cleanup_selected']) || isset($_POST['submit_cleanup_all']))
		{
			echo "Clean-Up wird durchgeführt...<br/>";
			$all = isset($_POST['submit_cleanup_all']) ? true : false;

			/* Message cleanup */	
			if ((isset($_POST['cl_msg']) && $_POST['cl_msg']==1) || $all)
			{
				if ($_POST['msg_type']=="all")
				{
					$tstamp = time()-$_POST['message_timestamp'];
					dbquery("
					DELETE FROM 
						messages
					WHERE 
						AND message_timestamp<$tstamp
					;");
					echo mysql_affected_rows()." Nachrichten wurden gelöscht!<br/>";
				}
				elseif ($_POST['msg_type']=="del")
				{
					$tstamp = time()-$_POST['message_timestamp'];
					dbquery("
					DELETE FROM 
						messages
					WHERE 
						message_deleted=1 
						AND message_timestamp<$tstamp
					;");
					echo mysql_affected_rows()." 'gelöschte' Nachtichten wurden endgültig gelöscht!<br/>";
				}				
			}

			/* Session-Log cleanup */
			if ((isset($_POST['cl_sesslog']) && $_POST['cl_sesslog']==1) || $all)
			{
				$tstamp = time()-$_POST['sess_log_timestamp'];
				dbquery("DELETE FROM ".$db_table['user_log']." WHERE log_logintime<$tstamp;");
				echo mysql_affected_rows()." Session-Logs wurden gelöscht!<br/>";
			}
			
			
			/* points */
			if ((isset($_POST['cl_points']) && $_POST['cl_points']==1) || $all)
			{
				$time_diff=time()-$_POST['del_user_points'];
				dbquery("DELETE FROM ".$db_table['user_points']." WHERE point_timestamp<".$time_diff.";");
				echo mysql_affected_rows()." Punkte-Daten wurden gelöscht!<br/>";
			}

			/* inactive */
			if ((isset($_POST['cl_inactive']) && $_POST['cl_inactive']==1) || $all)
			{
				$time_diff=time()-$_POST['del_user_points'];
				$num = remove_inactive(true);
				echo $num." inaktive User wurden gelöscht!<br/>";
			}			

			/* object lists */
			if ((isset($_POST['cl_objlist']) && $_POST['cl_objlist']==1) || $all)
			{
				dbquery("
				DELETE FROM 
					".$db_table['shiplist']."
				WHERE 
					shiplist_count =0
					AND shiplist_build_count =0
					AND shiplist_special_ship=0
				;");	
				echo mysql_affected_rows()." leere Schiffdaten wurden gelöscht!<br/>";
				dbquery("
				DELETE FROM 
					".$db_table['deflist']."
				WHERE 
					deflist_count =0
					AND deflist_build_count =0
				;");
				echo mysql_affected_rows()." leere Verteidigungsdaten wurden gelöscht!<br/>";
				dbquery("
				DELETE FROM 
					".$db_table['buildlist']."
				WHERE 
					buildlist_current_level=0
					AND buildlist_build_start_time=0
					AND buildlist_build_end_time=0
				;");		
				echo mysql_affected_rows()." leere Gebäudedaten wurden gelöscht!<br/>";
				dbquery("
				DELETE FROM 
					".$db_table['techlist']."
				WHERE 
					techlist_current_level=0
					AND techlist_build_start_time=0
					AND techlist_build_end_time=0
				;");		
				echo mysql_affected_rows()." leere Forschungsdaten wurden gelöscht!<br/>";
			}
			
			echo "Clean-Up fertig!<br/><br/>";
		}


/* log
		if ($_POST['delentrys']!="")
		{
			$tstamp = time()-$_POST['log_timestamp'];
			dbquery("DELETE FROM ".$db_table['logs']." WHERE log_cat=".$_POST['log_cat']." AND log_timestamp<$tstamp;");
			echo mysql_affected_rows()." Eintr&auml;ge wurden gelöscht!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
		}

					
		*/

		echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";

		/* Messages */		
		echo '<fieldset><legend><input type="checkbox" value="1" name="cl_msg" /> Nachrichten</legend>';
		$tblcnt = mysql_fetch_row(dbquery("
		SELECT 
			COUNT(message_id) 
		FROM 
			messages
		;"));
		echo '<input type="radio" name="msg_type" value="all" /><b>Nachrichten löschen:</b> ';
		echo "Älter als <select name=\"message_timestamp\">";
		echo "<option value=\"604800\" selected=\"selected\">1 Woche</option>";
		echo "<option value=\"1209600\">2 Wochen</option>";
		echo "<option value=\"2419200\">4 Wochen</option>";
		echo "</select> (".nf($tblcnt[0])." total).<br/>";

		$tblcnt = mysql_fetch_row(dbquery("
		SELECT 
			COUNT(message_id) 
		FROM 
			messages
		WHERE 
			message_deleted=1
		;"));
		echo '<input type="radio" name="msg_type" value="del" checked="checked" /> <b>Nur \'gelöschte\' Nachrichten löschen:</b> ';
		echo 'Älter als <select name="message_timestamp">';
		echo "<option value=\"604800\" selected=\"selected\">1 Woche</option>";
		echo "<option value=\"1209600\">2 Wochen</option>";
		echo "<option value=\"2419200\">4 Wochen</option>";
		echo "</select> (".nf($tblcnt[0])." total).";
		echo '</fieldset><br/>';

		/* Logs */
		echo '<fieldset><legend><input type="checkbox" value="1" name="cl_log" /> Logs (noch nicht implementiert)</legend>';
		$tblcnt = mysql_fetch_row(dbquery("
		SELECT 
			count(log_id) 
		FROM 
			".$db_table['logs']."
		;"));
		echo "<b>Logs löschen:</b> In Kategorie <select name=\"log_cat\">";
		echo "<option value=\"0\">(alle)</option>";
		$res=dbquery("
		SELECT 
			cat_id,
			cat_name,
			COUNT(log_id) as cnt 
		FROM 
			".$db_table['log_cat']."
		INNER JOIN
			".$db_table['logs']." 
			ON log_cat=cat_id 
		GROUP BY cat_id;");
		while ($arr=mysql_fetch_array($res))
		{
			echo "<option value=\"".$arr['cat_id']."\">".$arr['cat_name']." (".$arr['cnt'].")</option>";
		}
		echo "</select> welche &auml;lter als <select name=\"log_timestamp\">";
		echo "<option value=\"432000\">5 Tage</option>";
		echo "<option value=\"604800\" selected=\"selected\">1 Woche</option>";
		echo "<option value=\"1209600\">2 Wochen</option>";
		echo "<option value=\"2419200\">4 Wochen</option>";
		echo "</select> sind (".nf($tblcnt[0])." total).";
		echo '</fieldset><br/>';
		
		/* User-Sessions */
		echo '<fieldset><legend><input type="checkbox" value="1" name="cl_sesslog" /> Session-Logs</legend>';
		$tblcnt = mysql_fetch_row(dbquery("
		SELECT 
			COUNT(*) 
		FROM 
			".$db_table['user_log']."
		;"));
		echo "<b>Session-Logs löschen:</b> ";
		echo "Eintr&auml;ge löschen die &auml;lter als <select name=\"sess_log_timestamp\">";
		echo "<option value=\"604800\" selected=\"selected\">1 Woche</option>";
		echo "<option value=\"1209600\">2 Wochen</option>";
		echo "<option value=\"2419200\">4 Wochen</option>";
		echo "</select> sind (".nf($tblcnt[0])." total).";
		echo '</fieldset><br/>';

		/* User-Points */
		echo '<fieldset><legend><input type="checkbox" value="1" name="cl_points" /> Punkteverlauf</legend>';
		$tblcnt = mysql_fetch_row(dbquery("
		SELECT 
			COUNT(*) 
		FROM 
			".$db_table['user_points']."
		;"));
		echo "<b>Punkteverläufe löschen:</b> Eintr&auml;ge löschen die &auml;lter als <select name=\"del_user_points\">";
		echo "<option value=\"172800\">2 Tage</option>";
		echo "<option value=\"432000\">5 Tage</option>";
		echo "<option value=\"604800\" selected=\"selected\">1 Woche</option>";
		echo "<option value=\"1209600\">2 Wochen</option>";
		echo "</select> sind (".nf($tblcnt[0])." total).";
		echo '</fieldset><br/>';

		/* Inactive */
		echo '<fieldset><legend><input type="checkbox" value="1" name="cl_inactive" /> Inaktive User</legend>';
		$register_time = time()-(24*3600*$conf['user_inactive_days']['p2']);		// Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat
		$online_time = time()-(24*3600*$conf['user_inactive_days']['p1']);	// Zeit nach der ein User normalerweise gelöscht wird
		$res =	dbquery("
			SELECT
				COUNT(user_id)
			FROM
				".$db_table['users']."
			WHERE
				user_show_stats='1'
				AND (user_registered<'".$register_time."' AND user_points='0')
				OR (user_last_online<'".$online_time."' AND user_hmode_from='0');
		;");		
		$tblcnt = mysql_fetch_row($res);
		echo nf($tblcnt[0])." inaktive Benutzer löschen (".$conf['user_inactive_days']['p2']." Tage seit der Registration ohne Login 
		oder ".$conf['user_inactive_days']['p1']." Tage nicht mehr eingeloggt)";
		echo '</fieldset><br/>';
		
		/* Object lists */
		echo '<fieldset><legend><input type="checkbox" value="1" name="cl_objlist" /> Objektlisten</legend>';
		$res =	dbquery("
		SELECT
			 COUNT( shiplist_id )
		FROM 
			".$db_table['shiplist']."
		WHERE 
			shiplist_count =0
			AND shiplist_build_count =0
			AND shiplist_special_ship=0
		;");		
		$scnt = mysql_fetch_row($res);
		$res =	dbquery("
		SELECT
			 COUNT( shiplist_id )
		FROM 
			".$db_table['shiplist']."
		;");		
		$stcnt = mysql_fetch_row($res);		
		
		$res =	dbquery("
		SELECT
			 COUNT( deflist_id )
		FROM 
			".$db_table['deflist']."
		WHERE 
			deflist_count =0
			AND deflist_build_count =0;
		;");		
		$dcnt = mysql_fetch_row($res);
		$res =	dbquery("
		SELECT
			 COUNT( deflist_id )
		FROM 
			".$db_table['deflist']."
		;");		
		$dtcnt = mysql_fetch_row($res);
				
		$res =	dbquery("
		SELECT
			 COUNT( buildlist_id )
		FROM 
			".$db_table['buildlist']."
		WHERE 
			buildlist_current_level=0
			AND buildlist_build_start_time=0
			AND buildlist_build_end_time=0
		;");		
		$bcnt = mysql_fetch_row($res);
		$res =	dbquery("
		SELECT
			 COUNT( buildlist_id )
		FROM 
			".$db_table['buildlist']."
		;");		
		$btcnt = mysql_fetch_row($res);		
		
		$res =	dbquery("
		SELECT
			 COUNT( techlist_id )
		FROM 
			".$db_table['techlist']."
		WHERE 
			techlist_current_level=0
			AND techlist_build_start_time=0
			AND techlist_build_end_time=0
		;");		
		$tcnt = mysql_fetch_row($res);
		$res =	dbquery("
		SELECT
			 COUNT( techlist_id )
		FROM 
			".$db_table['techlist']."
		;");		
		$ttcnt = mysql_fetch_row($res);

		echo "<b>Leere Schiffdatensätze:</b> ".nf($scnt[0])." vorhanden (".nf($stcnt[0])." total).<br/>";
		echo "<b>Leere Verteidigungsdatensätze:</b> ".nf($dcnt[0])." vorhanden (".nf($dtcnt[0])." total).<br/>";
		echo "<b>Leere Gebäudedatensätze:</b> ".nf($bcnt[0])." vorhanden (".nf($btcnt[0])." total).<br/>";
		echo "<b>Leere Forschungsdatensätze:</b> ".nf($tcnt[0])." vorhanden (".nf($ttcnt[0])." total).<br/>";
		echo '</fieldset><br/>';	
		
		echo '<input type="submit" name="submit_cleanup_selected" value="Selektiere ausführen" /> &nbsp; ';
		echo '<input type="submit" name="submit_cleanup_all" value="Alle ausführen" />';
		
		echo '</form>';
	}	
	
	//
	// Übersicht
	//
	else
	{
		echo "W&auml;hle in der rechten Spalte eine Option aus! Achtung: Einige Operationen können die 
		Datenbank stark belasten und es dauert eine Weile bis die geforderte Seite geladen ist.";

		$st = array();
		$res=dbquery("SHOW GLOBAL STATUS;");
		while ($arr=mysql_fetch_array($res))
		{
			$st[strtolower($arr['Variable_name'])]=$arr['Value'];
		}
		$uts = $st['uptime'];
		$utm = round($uts/60);
		$uth = round($uts/3600);
		echo '<div style="float:left;">';

		echo '<h2>Datenbank-Pflege</h2>';
		echo '<input type="button" value="Optimieren" onclick="document.location=\'?page='.$page.'&amp;sub=optimize\';" />
		Sortiert Indizes und defragmentiert Daten.<br/>';
		echo '<input type="button" value="Reparieren" onclick="document.location=\'?page='.$page.'&amp;sub=repair\';" />
		Repariert möglicherweise defekte Tabellen.<br/>';
		echo '<input type="button" value="Überprüfen" onclick="document.location=\'?page='.$page.'&amp;sub=check\';" />
		Prüft Tabellen auf Fehler.<br/>';
		echo '<input type="button" value="Analysieren" onclick="document.location=\'?page='.$page.'&amp;sub=analyze\';" />
		Analysiert die Schlüsselverteilung der Tabellen.<br/>';

		echo "<h2>Serverstatistiken</h2>";
		echo 'Der Server läuft seit <b>'.tf($uts).'</b><br/>und wurde am <b>'.df(time()-$uts).'</b> Uhr gestartet.<br/><br/>';
		echo '<table style="width:450px;" class="tb">';
		echo '<tr><th colspan="2">Traffic</th><th>ø pro Stunde</th></tr>';
		echo '<tr><td style="width:120px;">Empfangen</td><td>'.byte_format($st['bytes_received']).'</td><td>'.byte_format($st['bytes_received']/$uth).'</td></tr>';
		echo '<tr><td>Gesendet</td><td>'.byte_format($st['bytes_sent']).'</td><td>'.byte_format($st['bytes_sent']/$uth).'</td></tr>';
		echo '<tr><td>Total</td><td>'.byte_format($st['bytes_received']+$st['bytes_sent']).'</td><td>'.byte_format(($st['bytes_received']+$st['bytes_sent'])/$uth).'</td></tr>';
		echo '</table><br/>';
		echo '<table style="width:450px;" class="tb">';
		echo '<tr><th colspan="2">Verbindungen</th><th>ø pro Stunde</th></tr>';
		echo '<tr><td>max. gleichz. Verbindungen</td><td>'.nf($st['max_used_connections']).'</td><td>-</td></tr>';
		echo '<tr><td>Fehlgeschlagen</td><td>'.nf($st['aborted_connects']).'</td><td>'.nf($st['aborted_connects']/$uth).'</td></tr>';
		echo '<tr><td>Abgebrochen</td><td>'.nf($st['aborted_clients']).'</td><td>'.nf(($st['aborted_clients'])/$uth).'</td></tr>';
		echo '<tr><td>Insgesamt</td><td>'.nf($st['connections']).'</td><td>'.nf(($st['connections'])/$uth).'</td></tr>';
		echo '</table><br/>';
		echo '<table style="width:450px;" class="tb">';
		echo '<tr><th colspan="2">Abfragen</th></tr>';
		echo '<tr><td style="width:120px;">Insgesamt</td><td>'.nf($st['questions']).'</td></tr>';
		echo '<tr><td>ø pro Tag</td><td>'.nf($st['questions']/$uth*24).'</td></tr>';
		echo '<tr><td>ø pro Stunde</td><td>'.nf($st['questions']/$uth).'</td></tr>';
		echo '<tr><td>ø pro Minute</td><td>'.nf($st['questions']/$utm).'</td></tr>';
		echo '<tr><td>ø pro Sekunde</td><td>'.nf($st['questions']/$uts).'</td></tr>';
		echo '</table><br/>';
		echo '<table style="width:450px;" class="tb">';
		echo '<tr><th colspan="2">Sonstiges</th></tr>';
		echo '<tr><td style="width:280px;">Langsame Abfragen</td><td>'.nf($st['slow_queries']).'</td></tr>';
		echo '<tr><td>Erstellte Temorärtabellen auf der Festplatte</td><td>'.nf($st['created_tmp_disk_tables']).'</td></tr>';
		echo '<tr><td>Offene Tabellen</td><td>'.nf($st['open_tables']).'</td></tr>';
		echo '<tr><td>Geöffnete Tabellen</td><td>'.nf($st['opened_tables']).'</td></tr>';
		echo '</table><br/>';
		echo '</div>';		

		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'size';
		$tr = array();
		$ts = array();
		$tn = array();
		$res=dbquery("SHOW TABLE STATUS FROM ". DB_DATABASE.";");
		while ($arr=mysql_fetch_array($res))
		{
			$rows+=$arr['Rows'];
			$datal+=$arr['Data_length']+$arr['Index_length'];
			$tr[$arr['Name']]=$arr['Rows'];
			$ts[$arr['Name']]=$arr['Data_length']+$arr['Index_length'];
			$tn[$arr['Name']]=$arr['Name'];
		}          
		echo '<div style="float:right;"><h2>Datenbankstatistiken</h2>';		
		echo "Die Datenbank <b>".DB_DATABASE."</b> hat <b>".nf($rows)."</b> Zeilen<br/>und eine 
		Gesamtgrösse von <b>".byte_format($datal)."</b><br/><br/>";
		echo '<table style="width:300px;" class="tb">';
		echo '<tr><th colspan="3">Datenbanktabellen</th></tr>';
		echo '<tr>
			<th><a href="?page='.$page.'&amp;sort=name">Name</th>
			<th><a href="?page='.$page.'&amp;sort=size">Grösse</th>
			<th><a href="?page='.$page.'&amp;sort=rows">Einträge</th>
		</tr>';
		if ($sort=='rows')
		{
			arsort ($tr);
			foreach ($tr as $k=>$v)
			{
				echo '<tr><td>'.$tn[$k].'</td><td>'.byte_format($ts[$k]).'</td><td>'.nf($tr[$k]).'</td></tr>';
			}		
		}
		if ($sort=='name')
		{
			asort ($tn);
			foreach ($tn as $k=>$v)
			{
				echo '<tr><td>'.$tn[$k].'</td><td>'.byte_format($ts[$k]).'</td><td>'.nf($tr[$k]).'</td></tr>';
			}		
		}
		else
		{
			arsort ($ts);
			foreach ($ts as $k=>$v)
			{
				echo '<tr><td>'.$tn[$k].'</td><td>'.byte_format($ts[$k]).'</td><td>'.nf($tr[$k]).'</td></tr>';
			}		
		}
		echo '</table>';
		echo '</div>';		
		
		echo '<br style="clear:both" />';
		
	}
?>

