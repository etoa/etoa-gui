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

	//Fehler ausgabe definiert
	ini_set('display_errors', 1);
	ini_set('arg_separator.output',  '&amp;');

	// Path to the relative root of the game
	if (!defined('RELATIVE_ROOT')) {
		define('RELATIVE_ROOT', '');
	}

	// Load constants
	require_once __DIR__ . '/const.inc.php';

	// Load functions
	require_once __DIR__ . '/functions.inc.php';

	// Load specific admin functions
	if (ADMIN_MODE) {
		require __DIR__ . '/../admin/inc/admin_functions.inc.php';
	}

	// Set timezone
	date_default_timezone_set(TIMEZONE);

	// Enable debug error reporting
	if (isDebugEnabled()) {
		error_reporting(E_ALL);
	} else {
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
	}

	// Include db config
	$cbConfigFile = DBManager::getInstance()->getConfigFile();
	if (!configFileExists($cbConfigFile))
	{
		if (isCLI()) {
			echo "Database configuration file $cbConfigFile does not exist!";
			exit(1);
		} else {
			if (ADMIN_MODE) {
				forward(RELATIVE_ROOT);
			}
			require(RELATIVE_ROOT."inc/install.inc.php");
			exit();
		}
	}

?>
