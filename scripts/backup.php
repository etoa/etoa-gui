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
	//  Dateiname: backup.php
	//  Topic: Datenbank-Wiederherstellung
	//  Autor: Nicolas Perrenoud alias MrCage
	//  Erstellt: 01.12.2004
	//  Bearbeitet von: Nicolas Perrenoud alias MrCage
	//  Bearbeitet am: 07.03.2006
	//  Kommentar: 	Diese Date erstellt ein Backup einer Datenbank mit dem Datum im Dateinamen

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

	// Initialisieren
	if (require(GAME_ROOT_DIR."/functions.php"))
	{	
		require(GAME_ROOT_DIR."/conf.inc.php");               
		dbconnect(); 	
		if (!defined('CLASS_ROOT'))	
			define('CLASS_ROOT',GAME_ROOT_DIR.'/classes');
		
		$conf = get_all_config();
		require(GAME_ROOT_DIR."/def.inc.php");
	
		chdir(GAME_ROOT_DIR);

		Backup::create();
	}
	else
		echo "Error: Could not include function file ".GAME_ROOT_DIR."/functions.php\n";	
?>
