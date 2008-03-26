<?PHP
 /**
 * XAJAX definitions, creates xajax-object and includes page specific xajax functions
 *
 * @package etoa_game_admin
 * @author MrCage mrcage@etoa.ch
 * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
 */		

	require_once("../".XAJAX_DIR."/xajax_core/xajax.inc.php");
	$xajax = new xajax();
	$xajax->setFlag('debug',false);

	
	require_once("inc/xajax/global.xajax.php");
	//require_once("inc/xajax/misc.xajax.php");

	if (isset($page) && $page=="alliances")
	{
		require_once("inc/xajax/alliances.xajax.php");
	}

	if (isset($page) && $page=="user")
	{
		require_once("inc/xajax/user.xajax.php");
	}

	if (isset($page) && $page=="ships")
	{
		require_once("inc/xajax/ships.xajax.php");
	}


	
	if (isset($page) && $page=="logs")
	{
		require_once("inc/xajax/logs.xajax.php");
	}	
	$xajax->processRequest();
?>