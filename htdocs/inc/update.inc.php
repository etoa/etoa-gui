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
		$log.= $tr->runTask('RemoveOldBannsTask');
		$log.= $tr->runTask('CleanupShiplistTask');
		$log.= $tr->runTask('CleanupDeflistTask');
		$log.= $tr->runTask('OptimizeTablesTask');
		$log.= $tr->runTask('AnalyzeTablesTask');
		$log.= $tr->runTask('ClearIPHostnameCacheTask');

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