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
		$task = new CalculateRankingTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";
		
		// Crete user banners
		$tmr = timerStart();
		$task = new CreateUserBannerTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";

		// Update user titles
		$tmr = timerStart();
		$task = new UpdateUserTitlesTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";
		
		// Schiffsteile berechnen
		$tmr = timerStart();
		$task = new AllianceShipPointsUpdateTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";

		// Wurmlöcher vertauschen
		$tmr = timerStart();
		$task = new PermuteWormholesTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";

		return $log;
	}

	function update_30minute()
	{
		$log = "";

		// Admins über einkommende Nachrichten Informieren
		$tmr = timerStart();
		$task = new AdminMessageNotificationTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";

		// Update market resource rates
		$tmr = timerStart();
		$task = new MarketrateUpdateTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";

		return $log;
	}


	function update_5minute()
	{
		$log = '';
		
		// Update user statistics
		$tmr = timerStart();
		$task = new GenerateUserStatisticsTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";
		
		// Chat-Cleanup
		$tmr = timerStart();
		$task = new RemoveOldChatMessagesTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";
		
		// Cleanup sessions
		$tmr = timerStart();
		$task = new SessionCleanupTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";
		
		// Check Backend
		$tmr = timerStart();
		$task = new BackendCheckTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";
		
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
		$task = new RemoveInactiveChatUsersTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";

		// Process log messages
		$tmr = timerStart();
		$task = new ProcessLogMessagesTask();
		$log.= $task->run()." (".timerStop($tmr)." sec)\n";

		return $log;
	}
?>