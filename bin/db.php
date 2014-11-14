#! /usr/bin/php -q
<?PHP
	//////////////////////////////////////////////////
	//           ____    __           ______        //
	//          /\  _`\ /\ \__       /\  _  \       //
	//          \ \ \L\_\ \ ,_\   ___\ \ \L\ \      //
	//           \ \  _\L\ \ \/  / __`\ \  __ \     //
	//            \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \    //
	//             \ \____/\ \__\ \____/\ \_\ \_\   //
	//              \/___/  \/__/\/___/  \/_/\/_/   //
	//                                              //
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame            //
	// Ein Massive-Multiplayer-Online-Spiel         //
	// Programmiert von Nicolas Perrenoud           //
	// www.nicu.ch | mail@nicu.ch                   //
	// als Maturaarbeit '04 am Gymnasium Oberaargau //
	//////////////////////////////////////////////////
	// 
	// Topic: Database maintenance
	// Autor: Nicolas Perrenoud alias MrCage
	// Erstellt: 01.12.2004
	//

	function show_usage() {
		echo "\nUsage: ".basename($_SERVER['argv'][0])." [action]\n\n";
		echo "Actions:\n";
		echo "  migrate    Migrate schema updates\n";
		echo "  reset      Drop all tables and rebuild database from scratch\n";
		echo "  backup     Backup database\n";
		echo "  restore    Restore database from backup\n";
		exit(1);
	}
	
	// Gamepfad feststellen
	$grd = chdir(realpath(dirname(__FILE__)."/../htdocs/"));

	// Initialisieren
	try {
		if (include("inc/bootstrap.inc.php"))
		{
			$args = array_splice($_SERVER['argv'], 1);
			$action = array_shift($args);
		
			if (empty($action))
			{
				show_usage();
			}
		
			$verbose = in_array("-v", $args);
		
			//
			// Migrate schema updates
			//
			if ($action == "migrate" || $action == "reset")
			{
				try
				{
					// Acquire mutex
					$mtx = new Mutex();
					$mtx->acquire();
					
					if ($action == "reset") {
						echo "Dropping all tables:\n";
						DBManager::getInstance()->dropAllTables();
					}
					
					echo "Migrate database:\n";
					$cnt = DBManager::getInstance()->migrate();
					if ($cnt == 0) {
						echo "Database is up-to-date\n";
					}

					// Release mutex
					$mtx->release();

					exit(0);
				}
				catch (Exception $e) 
				{
					// Release mutex
					$mtx->release();

					// Show output
					echo "Fehler: ".$e->getMessage();
					
					// Return code
					exit(1);
				}
			}

			//
			// Backup database
			//
			else if ($action == "backup")
			{
				$dir = DBManager::getBackupDir();
				$gzip = Config::getInstance()->backup_use_gzip=="1";
				
				try
				{
					// Acquire mutex
					$mtx = new Mutex();
					$mtx->acquire();
					
					// Restore database
					$log = DBManager::getInstance()->backupDB($dir, $gzip);

					// Release mutex
					$mtx->release();
					
					// Write log
					Log::add(Log::F_SYSTEM, Log::INFO, "[b]Datenbank-Backup Skript[/b]\n".$log);

					// Show output
					if ($verbose) {
						echo $log;
					}
					
					exit(0);
				}
				catch (Exception $e) 
				{
					// Release mutex
					$mtx->release();

					// Write log
					Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Backup Skript[/b]\nDie Datenbank konnte nicht in das Verzeichnis [b]".$dir."[/b] gesichert werden: ".$e->getMessage());
					
					// Show output
					echo "Fehler: ".$e->getMessage();
					
					// Return code
					exit(1);
				}
			}

			//
			// Restore database
			//
			else if ($action == "restore")
			{
				$dir = DBManager::getBackupDir();
				
				// Check if restore point specified
				if (!empty($args[0]))
				{
					$restorePoint = $args[0];
					try
					{
						// Acquire mutex
						$mtx = new Mutex();
						$mtx->acquire();
						
						// Restore database
						$log = DBManager::getInstance()->restoreDB($dir, $restorePoint);

						// Release mutex
						$mtx->release();
						
						// Write log
						Log::add(Log::F_SYSTEM, Log::INFO, "[b]Datenbank-Restore Skript[/b]\n".$log);
					
						// Show output
						if ($verbose) {
							echo $log;
						}
					
						exit(0);
					}
					catch (Exception $e) 
					{
						// Release mutex
						$mtx->release();

						// Write log
						Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Restore Skript[/b]\nDie Datenbank konnte nicht vom Backup [b]".$restorePoint."[/b] aus dem Verzeichnis [b]".$dir."[/b] wiederhergestellt werden: ".$e->getMessage());
						
						// Show output
						echo "Fehler: ".$e->getMessage();
						
						// Return code
						exit(1);
					}
				}
				else
				{
					echo "\nUsage: ".$_SERVER['argv'][0]." ".$action." [restorepoint]\n\n";
					echo "Available restorepoints:\n\n";
					$dates = DBManager::getInstance()->getBackupImages($dir);
					foreach ($dates as $f)
					{
						echo "$f\n";
					}
					exit(1);
				}
			}

			//
			// Any other action
			//
			else
			{
				echo "\nUnknown action!\n";
				show_usage();
			}
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
