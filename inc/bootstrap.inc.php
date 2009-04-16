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
	// 	File: bootstrap.inc.php
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

	// Path to the relative root of the game
	if (!defined('RELATIVE_ROOT'))
		define('RELATIVE_ROOT','');

	// Load constants
	require_once(RELATIVE_ROOT."inc/const.inc.php");

	// Load functions
	require_once(RELATIVE_ROOT."inc/functions.inc.php");

	// Include db config
	if (!@include_once(RELATIVE_ROOT."config/db.config.php"))
	{
		require(RELATIVE_ROOT."inc/install.inc.php");
		exit();
	}

	// Fehlermeldungs-Level feststellen
	if (ETOA_DEBUG==1)
		error_reporting(E_ALL);
	else
		error_reporting(E_ERROR | E_WARNING | E_PARSE);

	// Init session
	if (ADMIN_MODE)
		$s = AdminSession::getInstance();
	else
		$s = Session::getInstance();

	// Connect to database
	dbconnect();

	// Load config
	$cfg = Config::getInstance();
	$conf = $cfg->getArray();

	// Load default values
	require_once(RELATIVE_ROOT."inc/def.inc.php");

	// Set default page / action variables
	$page = (isset($_GET['page']) && $_GET['page']!="") ? $_GET['page'] : DEFAULT_PAGE;
	$sub = isset($_GET['sub']) ? $_GET['sub'] : null;
	$index = isset($_GET['index']) ? $_GET['index'] : null;
	$info = isset($_GET['info']) ? $_GET['info'] : null;
	$mode = isset($_GET['mode']) ? $_GET['mode'] : null;

	// Load template engine
	require_once(RELATIVE_ROOT."inc/template.inc.php");

	// Initialize XAJAX and load functions
	if (ADMIN_MODE)
		require_once(RELATIVE_ROOT."/admin/inc/xajax_admin.inc.php");
	else
		require_once(RELATIVE_ROOT."inc/xajax.inc.php");

	// Set popup identifiert to false
	$popup = false;


?>