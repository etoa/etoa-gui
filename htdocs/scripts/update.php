#! /usr/bin/php -q
<?PHP
	//////////////////////////////////////////////////
	//			 ____	 __			  ______				//
	//			/\	_`\ /\ \__		 /\	 _	\				//
	//			\ \ \L\_\ \ ,_\	  ___\ \ \L\ \				//
	//			 \ \  _\L\ \ \/	 / __`\ \  __ \				//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \			//
	//			 \ \____/\ \__\ \____/\ \_\ \_\				//
	//				\/___/	\/__/\/___/	 \/_/\/_/			//
	//														//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	//	Dateiname: update_minute.php
	//	Topic: Minütliche Updates
	//	Autor: Nicolas Perrenoud alias MrCage
	//	Erstellt: 29.11.2006
	//	Bearbeitet von: Nicolas Perrenoud alias MrCage
	//	Bearbeitet am: 07.12.2006
	//	Kommentar: Diese Datei führt Aktionen aus die einmal pro Minute erledigt werden müssen
	//	Die Datei wird auf einer Shell aufgerufen (via Cron-Job realisiert)
	//	Sie wird jede Stunde aufgerufen

	// Gamepfad feststellen
	$grd = chdir(realpath(dirname(__FILE__)."/../"));

	try {
		
		// Initialisieren
		if (include("inc/bootstrap.inc.php"))
		{
			include("inc/update.inc.php");
			
			$mode = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;
			
			// Prüfen ob Updates eingeschaltet sind
			if ($cfg->update_enabled->v==1)
			{
				// Mutex holen
				$tmr = timerStart();
				$log = " Warte auf Mutex...";
				$mtx = new Mutex();
				$mtx->acquire();
				$log .= " Mutex erhalten in ".timerStop($tmr)."s, beginne Update...\n\n";
				
				// Starte Zeitmessung
				$tmr = timerStart();
				
				$forceLog = false;
				
				// Monates-Update (1. des Monates 05:13)
				if ($mode=="month" || (date("H")=="05" && date("i")=="13" && date("j")=="1"))
				{
					$logt = "[b]Monates-Update ".date("d.m.Y, H:i")."[/b]\n";
					$log .= update_minute();
					$log .= update_month();
					
					$forceLog = true;
				}
				
				// Tages-Update (03:13)
				elseif ($mode=="day" || (date("H")=="03" && date("i")=="13"))
				{
					$logt = "[b]Tages-Update ".date("d.m.Y, H:i")."[/b]\n";
					$log .= update_minute();
					$log .= update_day();
			
					$forceLog = true;
				}
				
				// Stunden-Update
				elseif ($mode=="hour" || date("i")=="00")
				{
					$logt = "[b]Stunden-Update ".date("H:i")."[/b]\n";
					$log .= update_minute();
					$log .= update_5minute();
					$log .= update_30minute();
					$log .= update_hour();
					
					$forceLog = true;
				}

				// 30-Minuten-Update
				elseif ($mode=="30min" ||  date("i")=="30")
				{
					$logt = "[b]30-Minuten-Update ".date("H:i")."[/b]\n";
					$log .= update_minute();
					$log .= update_5minute();
					$log .= update_30minute();
				}

				// 5-Minuten-Update
				elseif ($mode=="5min" || date("i")%5==0 && date("i")!=30)
				{
					$logt = "[b]5-Minuten-Update ".date("H:i")."[/b]\n";
					$log .= update_minute();
					$log .= update_5minute();
				}

				// Minuten-Update
				else
				{
					$logt = "[b]Minuten-Update ".date("H:i")."[/b]\n";
					$log .= update_minute();
				}

				// 3-nach Update
				if (date("i")=="3") 
				{
					// Statistiken generieren und speichern
					Gamestats::generateAndSave();
				}
				
				// Log schreiben
				$t = timerStop($tmr);
				if (LOG_UPDATES || $t > LOG_UPDATES_THRESHOLD)
				{
					Log::add(Log::F_UPDATES, Log::WARNING, $logt."Gesamtdauer: ".$t."\n\n".$log);
				}
				else if ($forceLog)
				{
					Log::add(Log::F_UPDATES, Log::INFO, $logt."Gesamtdauer: ".$t."\n\n".$log);
				} 
				else 
				{
					// Wird nur geloggt wenn Debug Modus aktiv
					Log::add(Log::F_UPDATES, Log::DEBUG, $logt."Gesamtdauer: ".$t."\n\n".$log);
				}
				
				//Löscht Arrays (gibt Speicher wieder frei)
				unset($log);

				// Mutex freigeben
				$mtx->release();
				
				// Backup erstellen
				// ACHTUNG: Die create()-Funktion aquiriert selbst wieder das Mutes-Token. 
				// Deshalb muss diese Funktion nach mtx->release() stehen
				if ($cfg->get('backup_time_interval') > 0 && (date("h")-$cfg->get('backup_time_hour'))%$cfg->get('backup_time_interval')==0 && date("i")==$cfg->get('backup_time_minute')) 
				{
					DBManager::getInstance()->backupDB();
				}
			}

			// DB schliessen
			dbclose();
		}
		else
		{
			throw new EException("Could not load bootstrap file ".getcwd()."/inc/bootstrap.inc.php\n");
		}
	} catch (DBException $ex) {
		echo $ex;
		exit(1);
	}
?>