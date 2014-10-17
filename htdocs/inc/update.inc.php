<?PHP
	function update_month()
	{
		$log = '';
		$tr = new PeriodicTaskRunner();
	
		// Urlaubstage aktualisieren
		$tmr = timerStart();
		Users::addSittingDays();
		$log.= "Sittertage aller User wurden aktualisiert (".timerStop($tmr)." sec)\n";
		
		$log.= "\nTotal: ".$tr->getTotalDuration().' sec';
		return $log;
	}
	function update_day()
	{
		$log = '';
		$tr = new PeriodicTaskRunner();
		$log.= $tr->runTask('RemoveInactiveUsersTask');
		$log.= $tr->runTask('RemoveDeletedUsersTask');
		$log.= $tr->runTask('RemoveOldUserPointLogsTask');
		$log.= $tr->runTask('SetHolydayModeUsersInactiveTask');
		$log.= $tr->runTask('RemoveOldAlliancePointLogsTask');
		$log.= $tr->runTask('CleanupSessionLogsTask');
		$log.= $tr->runTask('RemoveOldLogsTask');
		$log.= $tr->runTask('RemoveOldMessagesTask');
		$log.= $tr->runTask('RemoveOldReportsTask');

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
		
		$log.= "\nTotal: ".$tr->getTotalDuration().' sec';
		return $log;
	}

	function update_hour()
	{
		$log = '';
		$tr = new PeriodicTaskRunner();
		$log.= $tr->runTask('CalculateRankingTask');
		$log.= $tr->runTask('CreateUserBannerTask');
		$log.= $tr->runTask('UpdateUserTitlesTask');
		$log.= $tr->runTask('AllianceShipPointsUpdateTask');
		$log.= $tr->runTask('PermuteWormholesTask');
		$log.= "\nTotal: ".$tr->getTotalDuration().' sec';
		return $log;
	}

	function update_30minute()
	{
		$log = '';
		$tr = new PeriodicTaskRunner();
		$log.= $tr->runTask('AdminMessageNotificationTask');
		$log.= $tr->runTask('MarketrateUpdateTask');
		$log.= "\nTotal: ".$tr->getTotalDuration().' sec';
		return $log;
	}


	function update_5minute()
	{
		$log = '';
		$tr = new PeriodicTaskRunner();
		$log.= $tr->runTask('GenerateUserStatisticsTask');
		$log.= $tr->runTask('RemoveOldChatMessagesTask');
		$log.= $tr->runTask('SessionCleanupTask');
		$log.= $tr->runTask('BackendCheckTask');
		$log.= "\nTotal: ".$tr->getTotalDuration().' sec';
		return $log;
	}

	function update_minute()
	{
		$log = '';
		$tr = new PeriodicTaskRunner();	
		$log.= $tr->runTask('WarPeaceUpdateTask');
		$log.= $tr->runTask('CheckMissilesTask');
		$log.= $tr->runTask('RemoveInactiveChatUsersTask');
		$log.= $tr->runTask('ProcessLogMessagesTask');
		$log.= "\nTotal: ".$tr->getTotalDuration().' sec';
		return $log;
	}
?>