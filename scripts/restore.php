#! /usr/bin/php -q
<?PHP

	/////////////////////////////////////////////////
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
	// 	Dateiname: backup.php
	// 	Topic: Datenbank-Wiederherstellung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.03.2006
	// 	Kommentar: 	Diese Date erstellt ein Backup einer Datenbank mit dem Datum im Dateinamen

	if ($_SERVER['argv'][2]!="")
		define(GAME_ROOT_DIR,$_SERVER['argv'][1]);
	else
	{
		$c=strrpos($_SERVER["SCRIPT_FILENAME"],"scripts/");
		if (stristr($_SERVER["SCRIPT_FILENAME"],"./")&&$c==0)
			define(GAME_ROOT_DIR,"../");
		elseif ($c==0)
			define(GAME_ROOT_DIR,".");
		else
			define(GAME_ROOT_DIR,substr($_SERVER["SCRIPT_FILENAME"],0,$c-1));
	}
	chdir(GAME_ROOT_DIR);

	// Initialisieren
	require("bootstrap.inc.php");
	if (require("functions.php"))
	{	
		require("conf.inc.php");               
		dbconnect(); 	
		require(GAME_ROOT_DIR."/def.inc.php");
	
		if ($_SERVER['argv'][1]!="")
		{
			Backup::restore($_SERVER['argv'][1]);
		}
		else
			echo "Usage: ".$_SERVER['argv'][0]." date\n";	
	}
	else
		echo "Error: Could not include function file ".GAME_ROOT_DIR."/functions.php\n";	
?>
