#! /usr/bin/php -q
<?PHP

require_once __DIR__ . '/../vendor/autoload.php';

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
	// Topic: Periodische Tasks
	// Autor: Nicolas Perrenoud alias MrCage
	// Erstellt: 29.11.2006
	// Kommentar: Diese Datei führt Aktionen aus die einmal pro Minute erledigt werden müssen
	// Die Datei wird auf einer Shell aufgerufen (via Cron-Job realisiert)
	// Sie wird jede Minute einmal aufgerufen
	//

	// Gamepfad feststellen
	$grd = chdir(realpath(__DIR__ ."/../htdocs/"));

	// Check for command line
	if (!isset($_SERVER['argv']))
	{
		echo "Script has to be executed on command line!";
		exit(1);
	}

	// Initialisieren
	$init = "inc/init.inc.php";
	if (!@include($init))
	{
		echo "Could not load bootstrap file ".getcwd()."/".($init)."\n";
		exit(1);
	}

	// Connect to database
	try {
		dbconnect();
	} catch (DBException $ex) {
		echo $ex;
		exit(1);
	}

	// Load default values
	require_once(RELATIVE_ROOT."inc/def.inc.php");

	if (!isset($app)) {
        $questSystemEnabled = (bool) Config::getInstance()->get('quest_system_enable');
        $app = require __DIR__ .'/../src/app.php';
        $app->boot();
    }

	$args = array_splice($_SERVER['argv'], 1);

	$verbose = in_array("-v", $args, true);

	try {

		// Prüfen ob Updates eingeschaltet sind
		if (Config::getInstance()->update_enabled->v==1)
		{
			$time = time();

			// Execute tasks
			$tr = new PeriodicTaskRunner($app);
			$log = '';
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

			if ($verbose) {
				echo $text;
			}
		}

		// DB schliessen
		dbclose();

	} catch (DBException $ex) {
		echo $ex;
		exit(1);
	}
