#! /usr/bin/php -q
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
	// 	Dateiname: update_minute.php
	// 	Topic: Minütliche Updates
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 29.11.2006
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.12.2006
	// 	Kommentar: Diese Datei führt Aktionen aus die einmal pro Minute erledigt werden müssen
	// 	Die Datei wird auf einer Shell aufgerufen (via Cron-Job realisiert)
	//	Sie wird jede Stunde aufgerufen

	define(USE_HTML,false);

	// Gamepfad feststellen
	if ($_SERVER['argv'][1]!="")
	{
		$grd = $_SERVER['argv'][1];
	}
	else
	{
		$c=strrpos($_SERVER["SCRIPT_FILENAME"],"scripts/");
		if (stristr($_SERVER["SCRIPT_FILENAME"],"./")&&$c==0)
			$grd = "../";
		elseif ($c==0)
			$grd = ".";
		else
			$grd = substr($_SERVER["SCRIPT_FILENAME"],0,$c-1);
	}
	
	define("GAME_ROOT_DIR",$grd);
	chdir($grd);

	// Initialisieren
	if (include("conf.inc.php"))
	{
		include("global.inc.php");
		include("functions.php");
		include("inc/update.inc.php");

		dbconnect();
		$conf = get_all_config();
		include("def.inc.php");

		// Prüfen ob Updates eingeschaltet sind
		if ($conf['update_enabled']['v']==1)
		{
			// Mutex holen
			$log = " Warte auf Mutex...";
			$mtx = new Mutex();
			$mtx->acquire();
			$log .= " Mutex erhalten in ".timerStop($tmr)."s, beginne Update...\n\n";
			
			// Starte Zeitmessung
			$tmr = timerStart();

			// Tages-Update (03:13)
			if (date("H")=="03" && date("i")=="13")
			{
				$logt = "[b]Tages-Update ".date("d.m.Y, H:i")."[/b]\n";
				$log .= update_minute();
				$log .= update_day();
			}

			// Stunden-Update
			elseif (date("i")=="00")
			{
				$logt = "[b]Stunden-Update ".date("H:i")."[/b]\n";
				$log .= update_minute();
				$log .= update_5minute();
				$log .= update_30minute();
				$log .= update_hour();
			}

			// 30-Minuten-Update
			elseif (date("i")=="30")
			{
				$logt = "[b]30-Minuten-Update ".date("H:i")."[/b]\n";
				$log .= update_minute();
				$log .= update_5minute();
				$log .= update_30minute();
			}

			// 5-Minuten-Update
			elseif (date("i")%5==0 && date("i")!=30)
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
				Gamestats::generateAndSave(GAME_ROOT_DIR."/".GAMESTATS_FILE);					
			}
			
			// Backup
			if ((date("h")-$cfg->p1("backup_time"))%$cfg->get("backup_time")==0 && date("i")==$cfg->p2("backup_time")) 
			{
				Backup::create();				
			}			

			// Log schreiben
			$t = timerStop($tmr);
			if (LOG_UPDATES || $t > LOG_UPDATES_THRESHOLD)
			{
				add_log (15,$logt."Gesamtdauer: ".$t."\n\n".$log);
			}
			//Löscht Arrays (gibt Speicher wieder frei)
			unset($log);

			// Mutex freigeben
			$mtx->release();
		}

		// DB schliessen
		dbclose();
	}
	else
	{
		throw new EException("Could not include config file ".$grd."/conf.inc.php\n");
	}
		

?>
