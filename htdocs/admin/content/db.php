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
	// Datenbanktabellen optimieren
	//
	if ($sub=="optimize")
	{
		echo '<h2>Optimierungsbericht</h2>';
		echo '<input type="button" value="Zur Übersicht" onclick="document.location=\'?page='.$page.'\'" /><br/><br/>';
		$ores = DBManager::getInstance()->optimizeTables(true);
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
		$ores = DBManager::getInstance()->repairTables(true);
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
		$ores = DBManager::getInstance()->analyzeTables(true);
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
		$ores = DBManager::getInstance()->checkTables(true);
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
	// Error log
	//
	elseif ($sub=="errorlog")
	{
		$tpl->setView('errorlog');
		$tpl->assign('subtitle', 'Datenbankfehler');

		if (isset($_POST['purgelog_submit'])) {
			file_put_contents(DBERROR_LOGFILE, '');
			forward('?page='.$page.'&sub='.$sub);
		}
		
		if (is_file(DBERROR_LOGFILE)) {
			$tpl->assign('logfile', file_get_contents(DBERROR_LOGFILE));
		}
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
		echo "<p>W&auml;hle in der rechten Spalte eine Option aus!<br/> Achtung: Einige Operationen können die 
		Datenbank stark belasten und es dauert eine Weile bis die geforderte Seite geladen ist.</p>";

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
		echo '<p><input type="button" value="Optimieren" onclick="document.location=\'?page='.$page.'&amp;sub=optimize\';" /> &nbsp; 
		Sortiert Indizes und defragmentiert Daten.</p>';
		echo '<p><input type="button" value="Reparieren" onclick="document.location=\'?page='.$page.'&amp;sub=repair\';" /> &nbsp; 
		Repariert möglicherweise defekte Tabellen.</p>';
		echo '<p><input type="button" value="Überprüfen" onclick="document.location=\'?page='.$page.'&amp;sub=check\';" /> &nbsp; 
		Prüft Tabellen auf Fehler.</p>';
		echo '<p><input type="button" value="Analysieren" onclick="document.location=\'?page='.$page.'&amp;sub=analyze\';" /> &nbsp; 
		Analysiert die Schlüsselverteilung der Tabellen.</p>';

		echo "<h2>Serverstatistiken</h2>";
		echo 'Der Server läuft seit <b>'.tf($uts).'</b><br/>und wurde am <b>'.df(time()-$uts).'</b> Uhr gestartet.<br/><br/>';
		echo '<table style="width:450px;" class="tb">';
		echo '<tr><th colspan="2">Traffic</th><th>ø pro Stunde</th></tr>';
		echo '<tr><td style="width:120px;">Empfangen</td><td>'.byte_format($st['bytes_received']).'</td><td>'.byte_format($uth > 0 ? $st['bytes_received']/$uth : 0).'</td></tr>';
		echo '<tr><td>Gesendet</td><td>'.byte_format($st['bytes_sent']).'</td><td>'.byte_format($uth > 0 ? $st['bytes_sent']/$uth : 0).'</td></tr>';
		echo '<tr><td>Total</td><td>'.byte_format($st['bytes_received']+$st['bytes_sent']).'</td><td>'.byte_format($uth > 0 ? ($st['bytes_received']+$st['bytes_sent'])/$uth : 0).'</td></tr>';
		echo '</table><br/>';
		echo '<table style="width:450px;" class="tb">';
		echo '<tr><th colspan="2">Verbindungen</th><th>ø pro Stunde</th></tr>';
		echo '<tr><td>max. gleichz. Verbindungen</td><td>'.nf($st['max_used_connections']).'</td><td>-</td></tr>';
		echo '<tr><td>Fehlgeschlagen</td><td>'.nf($st['aborted_connects']).'</td><td>'.nf($uth > 0 ? $st['aborted_connects']/$uth : 0).'</td></tr>';
		echo '<tr><td>Abgebrochen</td><td>'.nf($st['aborted_clients']).'</td><td>'.nf($uth > 0 ? ($st['aborted_clients'])/$uth : 0).'</td></tr>';
		echo '<tr><td>Insgesamt</td><td>'.nf($st['connections']).'</td><td>'.nf($uth > 0 ? ($st['connections'])/$uth : 0).'</td></tr>';
		echo '</table><br/>';
		echo '<table style="width:450px;" class="tb">';
		echo '<tr><th colspan="2">Abfragen</th></tr>';
		echo '<tr><td style="width:120px;">Insgesamt</td><td>'.nf($st['questions']).'</td></tr>';
		echo '<tr><td>ø pro Tag</td><td>'.nf($uth > 0 ? $st['questions']/$uth*24 : 0).'</td></tr>';
		echo '<tr><td>ø pro Stunde</td><td>'.nf($uth > 0 ?  $st['questions']/$uth : 0).'</td></tr>';
		echo '<tr><td>ø pro Minute</td><td>'.nf($utm > 0 ? $st['questions']/$utm : 0).'</td></tr>';
		echo '<tr><td>ø pro Sekunde</td><td>'.nf($uts > 0 ? $st['questions']/$uts : 0).'</td></tr>';
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
		echo '<div style="float:left;margin-left:50px"><h2>Datenbankstatistiken</h2>';		
		echo "Die Datenbank <b>".DBManager::getInstance()->getDbName()."</b> hat <b>".nf($rows)."</b> Zeilen<br/>und eine 
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

