#! /usr/bin/php -q
<?php
/*
#
#	//////////////////////////////////////////////////
#	//		 	 ____    __           ______       			//
#	//			/\  _`\ /\ \__       /\  _  \      			//
#	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
#	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
#	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
#	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
#	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
#	//																					 		//
#	//////////////////////////////////////////////////
#	// The Andromeda-Project-Browsergame				 		//
#	// Ein Massive-Multiplayer-Online-Spiel			 		//
#	// Programmiert von Nicolas Perrenoud				 		//
#	// www.nicu.ch | mail@nicu.ch								 		//
#	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
#	//////////////////////////////////////////////////
#
#	Dateiname: backup.php
#	Topic: Datenbank-Wiederherstellung
#	Autor: Nicolas Perrenoud alias MrCage
#	Erstellt: 01.12.2004
#	Bearbeitet von: Nicolas Perrenoud alias MrCage
#	Bearbeitet am: 07.03.2006
#	Kommentar: 	Diese Date erstellt ein Backup einer Datenbank mit dem Datum im Dateinamen
#*/

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
		require(GAME_ROOT_DIR."/../conf.inc.php");               
		dbconnect(); 	
		$conf = get_all_config();
		require(GAME_ROOT_DIR."/def.inc.php");
	
		chdir(GAME_ROOT_DIR);

	 	// Alte Backups löschen
	 	$cmd = "find ".BACKUP_DIR." -name *.sql.gz -mtime +".$conf['backup']['p1']." -exec rm {} \;";
	 	echo $cmd;
		passthru($cmd);

		$file = BACKUP_DIR."/".DB_DATABASE."-".date("Y-m-d-H-i");
		$file_wo_path = DB_DATABASE."-".date("Y-m-d-H-i");
		$result = shell_exec("mysqldump -u".DB_USER." -h".DB_SERVER." -p".DB_PASSWORD." ".DB_DATABASE." > ".$file.".sql");
		if ($result=="")
		{
			$result = shell_exec("gzip -9 --best ".$file.".sql");
			if ($result=="")
			{
				$ftp_con = ftp_connect(BACKUP_REMOTE_IP,21);
				if ($ftp_con)
				{
					$ftp_login = ftp_login($ftp_con, BACKUP_REMOTE_USER, BACKUP_REMOTE_PASSWORD); 
					if ($ftp_login)				
					{
						$ftp_up = ftp_put($ftp_con, BACKUP_REMOTE_PATH."/".$file_wo_path.".sql.gz", $file.".sql.gz", FTP_BINARY); 					
						if (!$ftp_up)
						{
							echo "Could not upload ".$file.".sql.gz to ftp server!\n";
						}
					}
					else
					{
						echo "Could not login to FTP server ".BACKUP_REMOTE_IP." with user ".BACKUP_REMOTE_USER."!\n";
					}				
				}
				else
				{					
					echo "FTP connection to ".BACKUP_REMOTE_IP." failed!\n";
				}
			}
			else
				echo "Error while zipping Backup-Dump $file: $result\n";
		}
		else
			echo "Error while creating Backup-Dump $file: $result\n";
	}
	else
		echo "Error: Could not include function file ".GAME_ROOT_DIR."/functions.php\n";	
?>
