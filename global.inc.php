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
	// 	File: global.inc.php
	// 	Created: 07.5.2007
	// 	Last edited: 06.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Defines some global constants which should not be changed
	*
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/
	
	//Fehler ausgabe definiert
  ini_set('display_errors', 1);
	ini_set('arg_separator.output',  '&amp;');

	// OS-Version feststellen
	if (defined('POSIX_F_OK'))
	{
		define('UNIX',true);
		define('WINDOWS',false);
		define('UNIX_USER',"etoa");
		define('UNIX_GROUP',"apache");
	}
	else
	{
		define('UNIX',false);
		define('WINDOWS',true);
	}

	// Path to the relative root of the game
	if (!defined('RELATIVE_ROOT'))										
		define('RELATIVE_ROOT','');	

	// Cache directory
	if (!defined('CACHE_ROOT')) 											
		define('CACHE_ROOT',RELATIVE_ROOT.'cache');

	// Class directory
	if (!defined('CLASS_ROOT'))												
		define('CLASS_ROOT',RELATIVE_ROOT.'classes');		

	// Data file directory
	if (!defined('DATA_DIR'))													
		define('DATA_DIR',RELATIVE_ROOT."data");

	// Image directory
	if (!defined('IMAGE_DIR'))												
		define('IMAGE_DIR',RELATIVE_ROOT."images");

	
	if (!defined('ADMIN_MODE'))												
		define('ADMIN_MODE',false);	

	if (!defined('USE_HTML'));
		define('USE_HTML',true);

	
	define('ERROR_LOGFILE',CACHE_ROOT."/errors.txt");
	define('DBERROR_LOGFILE',CACHE_ROOT."/dberrors.txt");


?>