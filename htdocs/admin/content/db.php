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

$twig->addGlobal('title', 'Datenbank');

//
// Database schema migrations
//
if ($sub === "migrations") {
    require("db/migrations.inc.php");
}

//
// Database reset
//
elseif ($sub === "reset") {
    require("db/reset.inc.php");
}

//
// Database maintenance
//
elseif ($sub === "maintenance") {
    require("db/maintenance.inc.php");
}

//
// Backups anzeigen
//
elseif ($sub === "backup") {
    require("db/backup.inc.php");
}

//
// Error log
//
elseif ($sub === "errorlog") {
    require("db/errorlog.inc.php");
}

//
// Clean-Up
//
elseif($sub === 'cleanup') {
    require("db/cleanup.inc.php");
}

//
// Übersicht
//
else {
    $st = [];
    $res = dbquery("SHOW GLOBAL STATUS;");
    while ($arr = mysql_fetch_array($res)) {
        $st[strtolower($arr['Variable_name'])] = $arr['Value'];
    }

    $uts = $st['uptime'];
    $utm = round($uts / 60);
    $uth = round($uts / 3600);

    $sort = $_GET['sort'] ?? 'size';
    $tr = [];
    $ts = [];
    $tn = [];
    $engines = [];
    $res = dbquery("SHOW TABLE STATUS FROM " . DBManager::getInstance()->getDbName() . ";");
    $rows = $datal = 0;
    while ($arr = mysql_fetch_array($res)) {
        $rows += $arr['Rows'];
        $datal += $arr['Data_length'] + $arr['Index_length'];
        $tr[$arr['Name']] = $arr['Rows'];
        $ts[$arr['Name']] = $arr['Data_length'] + $arr['Index_length'];
        $tn[$arr['Name']] = $arr['Name'];
        $engines[$arr['Name']] = $arr['Engine'];
    }

    $dbStats = [];
    if ($sort === 'rows') {
        arsort($tr);
        foreach ($tr as $k => $v) {
            $dbStats[] = [
                'name' => $tn[$k],
                'size' => byte_format($ts[$k]),
                'entries' => nf($tr[$k]),
                'engine' => $engines[$k],
            ];
        }
    } else if ($sort === 'name') {
        asort($tn);
        foreach ($tn as $k => $v) {
            $dbStats[] = [
                'name' => $tn[$k],
                'size' => byte_format($ts[$k]),
                'entries' => nf($tr[$k]),
                'engine' => $engines[$k],
            ];
        }
    } else if ($sort === 'engine') {
        asort($engines);
        foreach ($engines as $k => $v) {
            $dbStats[] = [
                'name' => $tn[$k],
                'size' => byte_format($ts[$k]),
                'entries' => nf($tr[$k]),
                'engine' => $engines[$k],
            ];
        }
    } else {
        arsort($ts);
        foreach ($ts as $k => $v) {
            $dbStats[] = [
                'name' => $tn[$k],
                'size' => byte_format($ts[$k]),
                'entries' => nf($tr[$k]),
                'engine' => $engines[$k],
            ];
        }
    }

    echo $twig->render('admin/database/database.html.twig', [
        'dbStats' => $dbStats,
        'dbName' => DBManager::getInstance()->getDbName(),
        'dbRows' => nf($rows),
        'dbSize' => byte_format($datal),
        'serverUptime' => tf($uts),
        'serverStarted' => df(time() - $uts),
        'bytesReceived' => byte_format($st['bytes_received']),
        'bytesReceivedHour' => byte_format($uth > 0 ? $st['bytes_received'] / $uth : 0),
        'bytesSent' => byte_format($st['bytes_sent']),
        'bytesSentHour' => byte_format($uth > 0 ? $st['bytes_sent'] / $uth : 0),
        'bytesTotal' => byte_format($st['bytes_received'] + $st['bytes_sent']),
        'bytesTotalHour' => byte_format($uth > 0 ? ($st['bytes_received'] + $st['bytes_sent']) / $uth : 0),
        'maxUsedConnections' => nf($st['max_used_connections']),
        'abortedConnections' => nf($st['aborted_connects']),
        'abortedConnectsHour' => nf($uth > 0 ? $st['aborted_connects'] / $uth : 0),
        'abortedClients' => nf($st['aborted_clients']),
        'abortedClientsHour' => nf($uth > 0 ? ($st['aborted_clients']) / $uth : 0),
        'connections' => nf($st['connections']),
        'connectionsHour' => nf($uth > 0 ? ($st['connections']) / $uth : 0),
        'questions' => nf($st['questions']),
        'avgQuestionsDay' => nf($uth > 0 ? $st['questions'] / $uth * 24 : 0),
        'avgQuestionsHour' => nf($uth > 0 ? $st['questions'] / $uth : 0),
        'avgQuestionsMinute' => nf($utm > 0 ? $st['questions'] / $utm : 0),
        'avgQuestionsSecond' => nf($uts > 0 ? $st['questions'] / $uts : 0),
        'slowQueries' => nf($st['slow_queries']),
        'createdTmpDiskTables' => nf($st['created_tmp_disk_tables']),
        'openTables' => nf($st['open_tables']),
        'openedTables' => nf($st['opened_tables']),
    ]);
    exit();
}
