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
	
	$tpl->assign('title', 'Datenbank');

	//
	// Database schema migrations
	//
	if ($sub == "migrations")
	{
		require("db/migrations.inc.php");
	}
	
	//
	// Database reset
	//
	elseif ($sub == "reset")
	{
		require("db/reset.inc.php");
	}
	
	//
	// Database maintenance
	//
	elseif ($sub == "maintenance")
	{
		require("db/maintenance.inc.php");
	}

	//
	// Backups anzeigen
	//
	elseif ($sub=="backup")
	{
		require("db/backup.inc.php");
	}

	//
	// Error log
	//
	elseif ($sub=="errorlog")
	{
		require("db/errorlog.inc.php");
	}

	//
	// Clean-Up
	//
	elseif($sub=='cleanup')
	{
		require("db/cleanup.inc.php");
	}	
	
	//
	// Übersicht
	//
	else
	{
		$tpl->setView('db');

		$st = array();
		$res=dbquery("SHOW GLOBAL STATUS;");
		while ($arr=mysql_fetch_array($res))
		{
			$st[strtolower($arr['Variable_name'])]=$arr['Value'];
		}
		$uts = $st['uptime'];
		$utm = round($uts/60);
		$uth = round($uts/3600);

		$tpl->assign('serverUptime', tf($uts));
		$tpl->assign('serverStarted', df(time()-$uts));
		
		$tpl->assign('bytesReceived', byte_format($st['bytes_received']));
		$tpl->assign('bytesReceivedHour', byte_format($uth > 0 ? $st['bytes_received']/$uth : 0));
		$tpl->assign('bytesSent', byte_format($st['bytes_sent']));
		$tpl->assign('bytesSentHour', byte_format($uth > 0 ? $st['bytes_sent']/$uth : 0));
		$tpl->assign('bytesTotal', byte_format($st['bytes_received']+$st['bytes_sent']));
		$tpl->assign('bytesTotalHour', byte_format($uth > 0 ? ($st['bytes_received']+$st['bytes_sent'])/$uth : 0));

		$tpl->assign('maxUsedConnections', nf($st['max_used_connections']));
		$tpl->assign('abortedConnections', nf($st['aborted_connects']));
		$tpl->assign('abortedConnectsHour', nf($uth > 0 ? $st['aborted_connects']/$uth : 0));
		$tpl->assign('abortedClients', nf($st['aborted_clients']));
		$tpl->assign('abortedClientsHour', nf($uth > 0 ? ($st['aborted_clients'])/$uth : 0));
		$tpl->assign('connections', nf($st['connections']));
		$tpl->assign('connectionsHour', nf($uth > 0 ? ($st['connections'])/$uth : 0));

		$tpl->assign('questions', nf($st['questions']));
		$tpl->assign('avgQuestionsDay', nf($uth > 0 ? $st['questions']/$uth*24 : 0));
		$tpl->assign('avgQuestionsHour', nf($uth > 0 ?  $st['questions']/$uth : 0));
		$tpl->assign('avgQuestionsMinute', nf($utm > 0 ? $st['questions']/$utm : 0));
		$tpl->assign('avgQuestionsSecond', nf($uts > 0 ? $st['questions']/$uts : 0));

		$tpl->assign('slowQueries', nf($st['slow_queries']));
		$tpl->assign('createdTmpDiskTables', nf($st['created_tmp_disk_tables']));
		$tpl->assign('openTables', nf($st['open_tables']));
		$tpl->assign('openedTables', nf($st['opened_tables']));

		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'size';
		$tr = array();
		$ts = array();
		$tn = array();
		$res=dbquery("SHOW TABLE STATUS FROM ". DBManager::getInstance()->getDbName().";");
		$rows=$datal=0;
		while ($arr=mysql_fetch_array($res))
		{
			$rows+=$arr['Rows'];
			$datal+=$arr['Data_length']+$arr['Index_length'];
			$tr[$arr['Name']]=$arr['Rows'];
			$ts[$arr['Name']]=$arr['Data_length']+$arr['Index_length'];
			$tn[$arr['Name']]=$arr['Name'];
		} 
		
		$tpl->assign('dbName', DBManager::getInstance()->getDbName());
		$tpl->assign('dbRows', nf($rows));
		$tpl->assign('dbSize', byte_format($datal));
		
		$dbStats = array();
		if ($sort=='rows')
		{
			arsort ($tr);
			foreach ($tr as $k=>$v)
			{
				$dbStats[] = array(
					'name' => $tn[$k],
					'size' => byte_format($ts[$k]),
					'entries' => nf($tr[$k])
				);
			}		
		}
		if ($sort=='name')
		{
			asort ($tn);
			foreach ($tn as $k=>$v)
			{
				$dbStats[] = array(
					'name' => $tn[$k],
					'size' => byte_format($ts[$k]),
					'entries' => nf($tr[$k])
				);
			}		
		}
		else
		{
			arsort ($ts);
			foreach ($ts as $k=>$v)
			{
				$dbStats[] = array(
					'name' => $tn[$k],
					'size' => byte_format($ts[$k]),
					'entries' => nf($tr[$k])
				);
			}		
		}
		$tpl->assign('dbStats', $dbStats);
	}
?>