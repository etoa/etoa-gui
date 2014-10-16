<?PHP
	function update_month()
	{
		$log = "";
	
		// Urlaubstage aktualisieren
		$tmr = timerStart();
		Users::addSittingDays();
		$log.= "Sittertage aller User wurden aktualisiert (".timerStop($tmr)." sec)\n";
		
		return $log;
	}
	function update_day()
	{
		$log = '';
	
		// Inaktive User löschen
		$tmr = timerStart();
		$nr = Users::removeInactive();
		$log.= "$nr inaktive User gelöscht (".timerStop($tmr)." sec)\n";

		$tmr = timerStart();
		$nr = Users::removeDeleted();
		$log.= "$nr als gelöscht markierte User endgültig gelöscht (".timerStop($tmr)." sec)\n";
	
		// Alte Benuterpunkte-Logs löschen
		$tmr = timerStart();
		$nr = Users::cleanUpPoints();
		$log.= "$nr alte Userpunkte-Logs gelöscht (".timerStop($tmr)." sec)\n";
		
		// Benutzer aus Urlaub inaktiv setzen
		if (Config::getInstance()->p2('hmode_days'))
		{
			$tmr = timerStart();
			$nr = Users::setUmodToInactive();
			$log.= "$nr User aus Urlaubsmodus in Inaktivität gesetzt (".timerStop($tmr)." sec)\n";
		}
		
		// Alte Allianzpunkte-Logs löschen
		$tmr = timerStart();
		$nr = Alliance::cleanUpPoints();
		$log.= "$nr alte Allianzpunkte-Logs gelöscht (".timerStop($tmr)." sec)\n";

		// Alte Session-Logs
		$tmr = timerStart();
		$nr = UserSession::cleanupLogs();
		$log.= "$nr alte Session-Logs gelöscht (".timerStop($tmr)." sec)\n";

		$tmr = timerStart();
		$nr = AdminSession::cleanupLogs();
		$log.= "$nr alte Session-Logs gelöscht (".timerStop($tmr)." sec)\n";

		// Alte Logs löschen
		$tmr = timerStart();
		$nr = Log::removeOld();
		$log.= "$nr alte Logs gelöscht (".timerStop($tmr)." sec)\n";

		// Alte Nachrichten löschen
		$tmr = timerStart();
		Message::removeOld();
		$log.= "Alte Nachrichten gelöscht (".timerStop($tmr)." sec)\n";
		
		// Alte Berichte löschen
		$tmr = timerStart();
		Report::removeOld();
		$log.= "Alte Berichte gelöscht (".timerStop($tmr)." sec)\n";

		// Abgelaufene Sperren löschen
		$tmr = timerStart();
		Users::removeOldBanns();
		$log.= "Abgelaufene Sperren gelöscht (".timerStop($tmr)." sec)\n";
		
		// Alte Baudatensätze löschen
		$tmr = timerStart();
		$nr = Shiplist::cleanUp();
		$log.= "$nr alte Schiffseinträge gelöscht (".timerStop($tmr)." sec)\n";
		
		$tmr = timerStart();
		$nr = Deflist::cleanUp();
		$log.= "$nr alte Verteidigungseinträge gelöscht (".timerStop($tmr)." sec)\n";

		// Tabellen optimieren
		$tmr = timerStart();
		DBManager::getInstance()->optimizeTables();
		$log.= "Tabellen optimiert (".timerStop($tmr)." sec)\n";
		$tmr = timerStart();
		DBManager::getInstance()->analyzeTables();
		$log.= "Tabellen analysiert (".timerStop($tmr)." sec)\n";

		// Remove old ip-hostname combos from cache
		$tmr = timerStart();
		Net::clearCache();
		$log.= "IP/Hostname Cache gelöscht (".timerStop($tmr)." sec)\n";

		// Close open tickets that are answered by an admin and are inactive
		$tmr = timerStart();
		Ticket::closeAssigedInactive();
		$log.= "Inaktive Tickes geschlossen(".timerStop($tmr)." sec)\n";
		
		return $log;
	}

	function update_hour()
	{
		$log = "";
		
		// Punkteberechnung
		$tmr = timerStart();
		Ranking::calc();
		$log.= "Punkte aktualisiert (".timerStop($tmr)." sec)\n";
		
		$tmr = timerStart();
		Ranking::createUserBanner();
		$log.= "User Banner erstellt (".timerStop($tmr)." sec)\n";

		if (ENABLE_USERTITLES==1) {
			$tmr = timerStart();
			Ranking::calcTitles();
			$log.= "User Titel aktualisiert (".timerStop($tmr)." sec)\n";
		}
		
		// Schiffsteile berechnen
		$tmr = timerStart();
		Alliance::allianceShipPointsUpdate();
		$log.= "Allianz-Schiffsteile berechnet (".timerStop($tmr)." sec)\n";

		// Wurmlöcher vertauschen
		$tmr = timerStart();
		Wormhole::randomize();
		$log.= "Wurml&ouml;cher vertauscht (".timerStop($tmr)." sec)\n";

		return $log;
	}

	function update_30minute()
	{
		$log = "";

		// Admins über einkommende Nachrichten Informieren
		$tmr = timerStart();
		$ares = dbquery("SELECT user_nick, user_email, player_id FROM admin_users WHERE player_id>0");
		if (mysql_num_rows($ares)>0)
		{
			while ($arow = mysql_fetch_row($ares))
			{
				$mres = dbquery("
					SELECT 
						message_data.subject, 
						message_data.text, 
						users.user_nick 
					FROM 
						messages 
					INNER JOIN 
						`message_data` 
					ON messages.message_id=message_data.id 
					AND messages.message_user_to='".$arow[2]."' 
					AND messages.message_mailed=0 
					AND messages.message_read=0 
					LEFT JOIN 
						users 
					ON messages.message_user_from=users.user_id");
				
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
		}
		$log.= "Admin-Mailqueue wurde abgearbeitet (".timerStop($tmr)." sec)\n";

		// Update market resource rates
		$tmr = timerStart();
		MarketHandler::updateRates();
		$log.= "Markt-Raten aktualisiert (".timerStop($tmr)." sec)\n";

		return $log;
	}


	function update_5minute()
	{
		$cfg = Config::getInstance();

		$log = '';
		
		// User Statistik speichern
		$tmr = timerStart();
		$rres = dbquery("SELECT COUNT(user_id) FROM users;");
		$rarr = mysql_fetch_row($rres);
		$gres = dbquery("SELECT COUNT(user_id) FROM user_sessions;");
		$garr = mysql_fetch_row($gres);
		dbquery("INSERT INTO user_onlinestats (stats_timestamp,stats_count,stats_regcount) VALUES (".time().",".$garr[0].",".$rarr[0].");");
		UserStats::generateImage(USERSTATS_OUTFILE);
		UserStats::generateXml(XML_INFO_FILE);
		$log.= "User-Statistik: ".$garr[0]." User online, ".$rarr[0]." User registriert (".timerStop($tmr)." sec)\n";
		
		// Chat-Cleanup
		$tmr = timerStart();
		$nr = ChatManager::cleanUpMessages();
		$log.= "$nr alte Chat Nachrichten gelöscht (".timerStop($tmr)." sec)\n";
		
		// Cleanup session
		$tmr = timerStart();
		UserSession::cleanup();
		AdminSession::cleanup();
		$log.= "Session cleanup (".timerStop($tmr)." sec)\n";
		
		// Check Backend
		$tmr = timerStart();
		$backend = checkDaemonRunning($cfg->daemon_pidfile)>0 ? true : false;
		$change = $cfg->value("backend_status") != $backend;
		if ($change)
		{
			$status = $cfg->value("backend_status") == 0 ? 1 : 0;
			$cfg->set("backend_status", $status);

			$tm = new TextManager();
			$infoText = $tm->getText('backend_offline_message');
			$mailText = $cfg->value("backend_status") == 0 ? "Funktioniert wieder" : $infoText->content;
			$mail = new Mail("EtoA-Backend", $mailText);
			$sendTo = explode(";",$cfg->value("backend_offline_mail"));
			foreach ($sendTo as $sendMail)	{
				$mail->send($sendMail);
			}
		}
		$log.= "Backend Check: ".($backend ? 'gestartet' : 'gestoppt')." (".($change ? 'geändert' : 'keine Änderung').") (".timerStop($tmr)." sec)\n";
		
		return $log;
	}

	function update_minute()
	{
		$log = '';
	
		// War/peace
		$tmr = timerStart();
		$task = new WarPeaceUpdateTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";
		
		// Missiles
		$tmr = timerStart();
		$task = new CheckMissilesTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";
	  
		// Remove inactive chat users
		$tmr = timerStart();
		$nr = ChatManager::cleanUpUsers();
		$log.= "$nr inaktive Chat-User gelöscht (".timerStop($tmr)." sec)\n";

		// Process log messages
		$tmr = timerStart();
		$nr = Log::processQueue();
		$nr+= GameLog::processQueue();
		$nr+= BattleLog::processQueue();
		$nr+= FleetLog::processQueue();
		$log.= "$nr Log Nachrichten verarbeitet (".timerStop($tmr)." sec)\n";

		return $log;
	}
?>