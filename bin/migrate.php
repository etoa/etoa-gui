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
	// Topic: Datenbank-Backup, erstellt ein Backup einer Datenbank mit dem Datum im Dateinamen
	// Autor: Nicolas Perrenoud alias MrCage
	// Erstellt: 01.12.2004
	//

	// Gamepfad feststellen
	$grd = chdir(realpath(dirname(__FILE__)."/../htdocs/"));

	// Initialisieren
	try {
		if (include("inc/bootstrap.inc.php"))
		{
			try
			{
				// Acquire mutex
				$mtx = new Mutex();
				$mtx->acquire();
				
				echo "Migrate database:\n";
				$cnt = DBManager::getInstance()->migrate();
				if ($cnt == 0) {
					echo "Database is up-to-date\n";
				}

				// Release mutex
				$mtx->release();
			}
			catch (Exception $e) 
			{
				// Release mutex
				$mtx->release();

				// Show output
				echo "Fehler: ".$e->getMessage();
				
				// Return code
				$ret = 1;
			}
			
			exit($ret);
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
