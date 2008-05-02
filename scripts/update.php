#! /usr/bin/php -q
<?PHP
	/**
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
	// 	Dateiname: update_minute.php
	// 	Topic: Minütliche Updates
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 29.11.2006
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.12.2006
	// 	Kommentar: Diese Datei führt Aktionen aus die einmal pro Minute erledigt werden müssen
	// 	Die Datei wird auf einer Shell aufgerufen (via Cron-Job realisiert)
	//	Sie wird jede Stunde aufgerufen
	*/

	function update_day()
	{
		global $db_table, $conf;

	 	// Inaktive User löschen
		$tmr = timerStart();
		remove_inactive();
		remove_deleted_users();
		$log = "Inaktive gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Alte Logs löschen
		$tmr = timerStart();
		remove_logs();
		$log.= "Alte Logs gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Alte Nachrichten löschen
		$tmr = timerStart();
		Message::removeOld();
		$log.= "Alte Nachrichten gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Abgelaufene Sperren löschen
		$tmr = timerStart();
		remove_old_banns();
		$log.= "Abgelaufene Sperren gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Tabellen optimieren
		$tmr = timerStart();
		optimize_tables();
		$log.= "Tabellen optimiert.\nDauer: ".timerStop($tmr)." sec\n\n";

		return $log;
	}


	function update_hour()
	{
		global $db_table, $conf;

		// Punkteberechnung
		$tmr = timerStart();
		Ranking::calc();
		if (ENABLE_USERTITLES==1)
		{
			Ranking::calcTitles();
		}
		$log = "\nPunkte aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Wurmlöcher vertauschen
		$tmr = timerStart();
		Wormhole::randomize();
		$log.= "Wurml&ouml;cher vertauscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Closes all open tables, forces all tables in use to be closed, and flushes the query cache.
		dbquery("FLUSH TABLES");

		return $log;
	}


	function update_30minute()
	{
		global $db_table, $conf;

		// Objekt updaten
		//$tmr = timerStart();
		//$obu = updateAllObjects();
		//$log = "Objekte auf ".$obu." Planeten aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";

		return $log;
	}


	function update_5minute()
	{
		global $db_table, $conf;

		// User Statistik speichern
		$rres=dbquery("SELECT COUNT(user_id) FROM ".$db_table['users'].";");
		$rarr=mysql_fetch_row($rres);
		$gres=dbquery("SELECT COUNT(user_id) FROM ".$db_table['users']." WHERE user_acttime>".(time()-$conf['user_timeout']['v']).";");
		$garr=mysql_fetch_row($gres);
		dbquery("INSERT INTO user_onlinestats (stats_timestamp,stats_count,stats_regcount) VALUES (".time().",".$garr[0].",".$rarr[0].");");
		$log = "\nUser-Statistik: ".$garr[0]." User online, ".$rarr[0]." User registriert\n\n";

		// Ressourcen updaten
		//$tmr = timerStart();
		//$ecu = updateAllEconomy();
		//$log.= "Wirtschaft auf ".$ecu." Planeten aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Gasplaneten updaten
		//$tmr = timerStart();
		//$gu = updateGasPlanets();
		$log.= "Gasvorkommen auf ".$gu." Gasplaneten aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";
		
		//Marktupdate
		//$tmr = timerStart();
		//market_update();
		//$log.= "Markt aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";		
		
		// Krieg-Frieden-Update
		$tmr = timerStart();
		$nr = warpeace_update();
		$log.= "$nr Krieg und Frieden aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";		
		
		// Chat-Cleanup
		dbquery("DELETE FROM chat WHERE id < (SELECT id FROM chat ORDER BY id DESC LIMIT 200,1)");		

		return $log;
	}

	function update_minute()
	{
		global $db_table, $conf;

		$nr = warpeace_update();
		
		// Mailqueue abarbeiten
		$tmr = timerStart();
		$cnt = mail_queue_send($conf['mailqueue']['v']);
		$log = "Die E-Mail-Warteschlange wurde abgearbeitet, [b]".$cnt."[/b] Mails versendet!\nDauer: ".timerStop($tmr)." sec\n\n";

		// Flotten updaten
		$tmr = timerStart();

		check_missiles();
        
    // Update-Flag setzen
    dbquery("UPDATE ".$db_table['config']." SET config_value=1,config_param2=".time()." WHERE config_name='updating_fleet';");

    $fa = updateAllFleet();
    $log .= "Es wurden [b]".$fa[0]."[/b] Flotten aktualisiert!\n";
    $log .= "Dauer: ".timerStop($tmr)." sec\n";
    for ($i=0;$i<$fa[0];$i++)
    {
        $log .= "Flotte [b]".$fa[1][$i]."[/b]\n";
    }

    //Flag deaktivieren
    dbquery("UPDATE ".$db_table['config']." SET config_value=0 WHERE config_name='updating_fleet';");
		
		return $log;
	}



	// Gamepfad feststellen
	if ($_SERVER['argv'][1]!="")
	{
		$grd = $_SERVER['argv'][1];
	}
	else
	{
		$c=strrpos($_SERVER["SCRIPT_FILENAME"],"scripts/");
		if (stristr($_SERVER["SCRIPT_FILENAME"],"./")&&$c==0)
			$grd = "../";
		elseif ($c==0)
			$grd = ".";
		else
			$grd = substr($_SERVER["SCRIPT_FILENAME"],0,$c-1);
	}

	define("GAME_ROOT_DIR",$grd);

	// Initialisieren
	if (include(GAME_ROOT_DIR."/functions.php"))
	{
		include(GAME_ROOT_DIR."/conf.inc.php");
		dbconnect();
		$conf = get_all_config();
		include(GAME_ROOT_DIR."/def.inc.php");
		$nohtml=true;
	
		chdir(GAME_ROOT_DIR);


		// Prüfen ob Updates eingeschaltet sind
		if ($conf['update_enabled']['v']==1)
		{

      // Löst die Updates, falls diese +5 Minuten gesperrt sind
      //Allgemeinte Updates
      if ($conf['updating']['v']!=0 && ($conf['updating']['p2']=="" || $conf['updating']['p2']<time()-300))
      {
          dbquery("UPDATE config SET config_value=0 WHERE config_name='updating';");
      }

      // Flottenupdate
      if ($conf['updating_fleet']['v']!=0 && ($conf['updating_fleet']['p2']=="" || $conf['updating_fleet']['p2']<time()-300))
			{
          dbquery("UPDATE config SET config_value=0 WHERE config_name='updating_fleet';");
      }


			// Prüfen ob nicht bereits ein Update läuft
			if ($conf['updating']['v']==0)
			{
				// Update-Flag setzen
				dbquery("UPDATE ".$db_table['config']." SET config_value=1,config_param2=".time()." WHERE config_name='updating';");

				// Messung starten
				$tmr = timerStart();

				//
				// Tages-Update (03:13)
				//
				if (date("H")=="03" && date("i")=="13")
				{
					$logt = "[b]Tages-Update ".date("d.m.Y, H:i")."[/b]\n";
					$log = update_minute();
					$log .= update_day();
				}

				//
				// Stunden-Update
				//
				elseif (date("i")=="00")
				{
					$logt = "[b]Stunden-Update ".date("H:i")."[/b]\n";
					$log = update_minute();
					$log .= update_5minute();
					//$log .= update_minute();
					$log .= update_30minute();
					//$log .= update_minute();
					$log .= update_hour();
				}

				//
				// 30-Minuten-Update
				//
				elseif (date("i")=="30")
				{
					$logt = "[b]30-Minuten-Update ".date("H:i")."[/b]\n";
					$log = update_minute();
					$log .= update_5minute();
					//$log .= update_minute();
					$log .= update_30minute();
				}
				//
				// 5-Minuten-Update
				//
				elseif (date("i")%5==0 && date("i")!=30)
				{
					$logt = "[b]5-Minuten-Update ".date("H:i")."[/b]\n";
					$log = update_minute();
					$log .= update_5minute();
				}

				//
				// Minuten-Update
				//
				else
				{
					$logt = "[b]Minuten-Update ".date("H:i")."[/b]\n";
					$log = update_minute();
				}

				// Log schreiben
				add_log (15,$logt."Gesamtdauer: ".timerStop($tmr)."\n\n".$log,time());

				//Löscht Arrays (gibt Speicher wieder frei)
				unset($log);

				// Update-Flag löschen
				dbquery("UPDATE ".$db_table['config']." SET config_value=0 WHERE config_name='updating';");
			}
			else
			{
				$str= "Update-Überschneidung. Das Update vom ".date("d.m.Y, H:i")." wird nicht ausgeführt!";
				add_log (15,$str,time());
			}
		}

		// DB schliessen
		dbclose();
	}
	else
		echo "Error: Could not include function file ".GAME_ROOT_DIR."/functions.php\n";

?>