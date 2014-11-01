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
	// Topic: Datenbank-Wiederherstellung
	// Autor: Nicolas Perrenoud alias MrCage
	// Erstellt: 01.12.2004
	//
	
	// Gamepfad feststellen
	$grd = chdir(realpath(dirname(__FILE__)."/../"));

	// Initialisieren
	try {
		if (include("inc/bootstrap.inc.php"))
		{	
			$dir = DBManager::getBackupDir();
			
			if (!empty($_SERVER['argv'][1]))
			{
				$restorePoint = $_SERVER['argv'][1];
			
				$ret = 0;
			
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
					$ret = 1;
				}
				
				exit($ret);
			}
			else
			{
				echo "Usage: ".$_SERVER['argv'][0]." [restorepoint]\n\n";
				echo "Available restorepoints:\n\n";
				$dates = DBManager::getInstance()->getBackupImages($dir);
				foreach ($dates as $f)
				{
					echo "$f\n";
				}
				
				exit(1);
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
