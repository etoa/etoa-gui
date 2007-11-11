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
	// 	File: conf.inc.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.09.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//
	/**
	* Database access and ftp data
	*
	* @package etoa_gameserver
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	///////////////////////////////////////////////////////////////
	//////////////       MYSQL - Connection Data          /////////
	///////////////////////////////////////////////////////////////

	define('DB_SERVER','test.etoa.net');
	define('DB_USER','etoatest');
	define('DB_PASSWORD','etoatest');
	define('DB_DATABASE','etoatest');	
	define("PASSWORD_SALT","wokife63wigire64reyodi69");
	
	///////////////////////////////////////////////////////////////
	//////////////            FTP-Backup			            /////////
	///////////////////////////////////////////////////////////////

	define('BACKUP_REMOTE_IP',"88.198.42.35");
	define('BACKUP_REMOTE_USER',"13274");
	define('BACKUP_REMOTE_PASSWORD',"NZnYtarU");
	define('BACKUP_REMOTE_PATH',"/sql_test");	

?>
