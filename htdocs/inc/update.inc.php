<?PHP
	function update_month()
	{
		// Urlaubstage aktualisieren
		$tmr = timerStart();
		Users::addSittingDays();
		$log = "Sittertage aller User wurden aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";
		
		return $log;
	}
	function update_day()
	{
		global $conf;

	 	// Inaktive User l�schen
		$tmr = timerStart();
		$ui = Users::removeInactive();
		$ud = Users::removeDeleted();
		$log = "Inaktive und als gelöscht markierte User gel�scht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Alte Benuterpunkte-Logs löschen
		$tmr = timerStart();
		$nr = Users::cleanUpPoints();
		$log.= "$nr alte Userpunkte-Logs gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";
		
		// Alte Allianzpunkte-Logs löschen
		$tmr = timerStart();
		$nr = Alliance::cleanUpPoints();
		$log.= "$nr alte Allianzpunkte-Logs gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		$tmr = timerStart();
		Users::resetSpyattacks();
		$log.= "Spionageangriffscounter auf 0 gesetzt.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Alte Session-Logs
		$tmr = timerStart();
		$nr = UserSession::cleanupLogs();
		$log.= "$nr alte Session-Logs gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		$tmr = timerStart();
		$nr = AdminSession::cleanupLogs();
		$log.= "$nr alte Session-Logs gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Alte Logs löschen
		$tmr = timerStart();
		$nr = Log::removeOld();
		$log.= "$nr alte Logs gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Alte Nachrichten löschen
		$tmr = timerStart();
		Message::removeOld();
		$log.= "Alte Nachrichten gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";
		
		// Alte Berichte löschen
		$tmr = timerStart();
		Report::removeOld();
		$log.= "Alte Berichte gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Abgelaufene Sperren löschen
		$tmr = timerStart();
		Users::removeOldBanns();
		$log.= "Abgelaufene Sperren gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";
		
		//Alte Baudatensätze löschen
		$tmr = timerStart();
		$nr = Shiplist::cleanUp();
		$log.= "$nr alte Schiffseinträge gelöscht.\n";
		$nr = Deflist::cleanUp();
		$log.= "$nr alte Verteidigungseinträge gelöscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Tabellen optimieren
		$tmr = timerStart();
		DBManager::getInstance()->optimizeTables();
		$log.= "Tabellen optimiert.\nDauer: ".timerStop($tmr)." sec\n\n";
		$tmr = timerStart();
		DBManager::getInstance()->analyzeTables();
		$log.= "Tabellen analysiert.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Remove old ip-hostname combos from cache
		Net::clearCache();

		// Close open tickets that are answered by an admin and are inactive
		Ticket::closeAssigedInactive();
		
		return $log;
	}


	function update_hour()
	{
		global $conf;

		// Punkteberechnung
		$tmr = timerStart();
		Ranking::calc();
		if (ENABLE_USERTITLES==1)
		{
			Ranking::calcTitles();
		}
		$log = "\nPunkte aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";
		
		//Schiffsteile berechnen
		Alliance::allianceShipPointsUpdate();

		// Wurml�cher vertauschen
		$tmr = timerStart();
		Wormhole::randomize();
		$log.= "Wurml&ouml;cher vertauscht.\nDauer: ".timerStop($tmr)." sec\n\n";

		// Closes all open tables, forces all tables in use to be closed, and flushes the query cache.
		// DEPRECATED: One should not rely on that, it should work otherwise. Needs global admin privilege of mysql
		//dbquery("FLUSH TABLES");

		return $log;
	}

	function update_30minute()
	{
		global $conf;
		$log = "";

		//Admins �ber einkommende Nachrichten Informieren
		$ares = dbquery("SELECT user_nick, user_email, player_id FROM admin_users WHERE player_id>0");
		if (mysql_num_rows($ares)>0)
		{
			while ($arow = mysql_fetch_row($ares))
			{
				$mres = dbquery("SELECT message_data.subject, message_data.text, users.user_nick FROM messages INNER JOIN `message_data` ON messages.message_id=message_data.id AND messages.message_user_to='".$arow[2]."' AND messages.message_mailed=0 AND messages.message_read=0 LEFT JOIN users ON messages.message_user_from=users.user_id");
				
				if (mysql_num_rows($mres)>0)
				{
					$count = 1;
					$email_text = "Hallo ".$arow[0].",\n\nDu hast ".mysql_num_rows($mres)." neue Nachricht(en) erhalten.\n\n";
					while ($mrow = mysql_fetch_row($mres))
					{
						if ($mrow[2]=="") 
						{
							$email_text .= "#".$count." Von System mit dem Betreff '".$mrow[0]."'\n\n\n";
						}
						else
						{
							$email_text .= "#".$count." Von ".$mrow[2]." mit dem Betreff '".$mrow[0]."'\n\n".substr($mrow[1], 0, 500)."\n\n\n";
						}
						$count++;
						
					}
					$mail = new Mail("Neue private Nachricht in EtoA - Admin",$email_text);
					$mail->send($arow[1]);
					dbquery("UPDATE messages SET messages.message_mailed=1 WHERE messages.message_user_to='".$arow[2]."';");
				}
			}
			$log = "\nAdmin-Mailqueue wurde abgearbeitet.";
		}

		// Update market resource rates
		MarketHandler::updateRates();

		return $log;
	}


	function update_5minute()
	{
		global $conf;
		$cfg = Config::getInstance();

		// User Statistik speichern
		$rres=dbquery("SELECT COUNT(user_id) FROM users;");
		$rarr=mysql_fetch_row($rres);
		$gres=dbquery("SELECT COUNT(user_id) FROM user_sessions;");
		$garr=mysql_fetch_row($gres);
		dbquery("INSERT INTO user_onlinestats (stats_timestamp,stats_count,stats_regcount) VALUES (".time().",".$garr[0].",".$rarr[0].");");
		$log = "\nUser-Statistik: ".$garr[0]." User online, ".$rarr[0]." User registriert\n\n";

		
		// Krieg-Frieden-Update
		$tmr = timerStart();
		$nr = warpeace_update();
		$log.= "$nr Krieg und Frieden aktualisiert.\nDauer: ".timerStop($tmr)." sec\n\n";		
		
		// Chat-Cleanup
		$res = dbquery("SELECT id FROM chat ORDER BY id DESC LIMIT 200,1");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_row($res);
			dbquery("DELETE FROM chat WHERE id < ".$arr[0]);		
		}

		// Userstats
		UserStats::generateImage(USERSTATS_OUTFILE);
		UserStats::generateXml(XML_INFO_FILE);

		// Cleanup session
		UserSession::cleanup();
		AdminSession::cleanup();
		
		//Check Backend
		$backend = checkDaemonRunning($cfg->daemon_pidfile)>0 ? true : false;
		if ($cfg->value("backend_status") != $backend)
		{
			$mailText = $cfg->value("backend_status") == 0 ? "Funktioniert wieder" : $cfg->value("backend_offline_message");
			$status = $cfg->value("backend_status") == 0 ? 1 : 0;
			$cfg->set("backend_status", $status);
			
			$mail = new Mail("EtoA-Backend", $mailText);
			$sendTo = explode(";",$cfg->value("backend_offline_mail"));
			foreach ($sendTo as $sendMail)	{
				$mail->send($sendMail);
			}
		}

		return $log;
	}

	function update_minute()
	{
		global $conf;

		// Zufalls-Event ausl�sen
		//PlanetEventHandler::doEvent(RANDOM_EVENTS_PER_UPDATE);

		$log= "Krieg/Frieden aktualisieren...\n";
		$nr = warpeace_update();
		
		$log.= "Raketen berechnen...\n";
		check_missiles();
      
		$log.= "Inaktive Chat-User löschen...\n";
		chatUserCleanUp();

		// Process log messages
		$nr = Log::processQueue();
		$nr+= GameLog::processQueue();
		$nr+= BattleLog::processQueue();
		$nr+= FleetLog::processQueue();
		$log.= "$nr Log Nachrichten verarbeitet\n";

		return $log;
	}
?>