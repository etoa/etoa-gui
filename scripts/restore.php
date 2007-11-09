#! /usr/bin/php -q
<?php
	/**
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
	*/

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

	// Initialisieren
	if (include(GAME_ROOT_DIR."/functions.php"))
	{	
		include(GAME_ROOT_DIR."/conf.inc.php");               
		dbconnect(); 	
		$conf = get_all_config();
		include(GAME_ROOT_DIR."/def.inc.php");
	
		define(BACKUP_PATH,GAME_ROOT_DIR."/backup");

		if ($_SERVER['argv'][1]!="")
		{
			$file = BACKUP_PATH."/".$db_access['db']."-".$_SERVER['argv'][1];
			if (file_exists($file.".sql.gz"))
			{
				$result = shell_exec("gunzip ".$file.".sql.gz");
				if ($result=="")
				{
					$result = shell_exec("mysql -u".$db_access['adminuser']." -p".$db_access['adminpw']." -h".$db_access['server']." ".$db_access['db']." < ".$file.".sql");
					if ($result!="")
						echo "Error while restoring backup: $result\n";
					shell_exec("gzip ".$file.".sql");
				}
				else
					echo "Error while unzipping Backup-Dump $file: $result\n";
			}
			else
				echo "Error: File $file not found!\n";	
		}
		else
			echo "Usage: ".$_SERVER['argv'][0]." date\n";	
	}
	else
		echo "Error: Could not include function file ".GAME_ROOT_DIR."/functions.php\n";	
?>
