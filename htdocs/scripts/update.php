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
	//	Dateiname: update.php
	//	Topic: Periodische Tasks
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
			// Prüfen ob Updates eingeschaltet sind
			if ($cfg->update_enabled->v==1)
			{
				$time = time();
				
				// Execute tasks
				$tr = new PeriodicTaskRunner();
				foreach (PeriodicTaskRunner::getScheduleFromConfig() as $tc) {
					if (PeriodicTaskRunner::shouldRun($tc['schedule'], $time)) {
						$log.= $tc['name'].': '.$tr->runTask($tc['name']);
					}
				}
				$log.= "\nTotal: ".$tr->getTotalDuration().' sec';
				
				// Write log
				if (LOG_UPDATES) {
					$severity = Log::INFO;
				} elseif ($tr->getTotalDuration() > LOG_UPDATES_THRESHOLD) {
					$severity = Log::WARNING;
				} else {
					$severity = Log::DEBUG;
				}
				$text = "Periodische Tasks (".date("d.m.Y H:i:s",$time)."):\n\n".$log;
				Log::add(Log::F_UPDATES, $severity, $text);
				
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