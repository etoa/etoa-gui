<?PHP
 /**
 * XAJAX definitions, creates xajax-object and includes page specific xajax functions
 *
 * @package etoa_game_admin
 * @author MrCage mrcage@etoa.ch
 * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
 */		

	require_once("../xajax/xajax.inc.php");
	$xajax = new xajax();
	
	require_once("xajax_admin/global.xajax.php");

	if (isset($page) && $page=="alliances")
	{
		require_once("xajax_admin/alliances.xajax.php");
	}

	if (isset($page) && $page=="user")
	{
		require_once("xajax_admin/user.xajax.php");
	}

	
	if (isset($page) && $page=="logs")
	{
		require_once("xajax_admin/logs.xajax.php");
	}	
	
	$xajax->processRequests();
?>