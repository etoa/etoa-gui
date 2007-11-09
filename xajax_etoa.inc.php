<?PHP
 /**
 * XAJAX definitions, creates xajax-object and includes page specific xajax functions
 *
 * @package etoa_game
 * @author MrCage mrcage@etoa.ch
 * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
 */		

	require_once("xajax/xajax.inc.php");
	$objAjax = new xajax();
	//$objAjax-> debugOn();

	
	require_once("xajax_etoa/global.xajax.php");

	if (stristr($_SERVER["PHP_SELF"],"chatbox.php") || (isset($page) && $page=="chat"))
	{
		require_once("xajax_etoa/chat.xajax.php");
	}
	
	if (isset($page) && $page=="haven")
	{
		require_once("xajax_etoa/haven.xajax.php");
	}
	if (isset($page) && $page=="messages")
	{
		require_once("xajax_etoa/messages.xajax.php");
	}
	if (isset($page) && $page=="stats")
	{
		require_once("xajax_etoa/stats.xajax.php");
	}
	if (isset($page) && $page=="userconfig")
	{
		require_once("xajax_etoa/userconfig.xajax.php");
	}
	if (isset($page) && $page=="solsys")
	{
		require_once("xajax_etoa/solsys.xajax.php");
	}
	if (isset($index) && $index=="register")
	{
		require_once("xajax_etoa/registration.xajax.php");
	}
	/*
	if (isset($page) && $page=="havenb")
	{
		require_once("xajax_etoa/haven.xajax.php");
	}*/
	if (isset($page) && $page=="market")
	{
		require_once("xajax_etoa/market.xajax.php");
	}
	if (isset($page) && $page=="alliance")
	{
		require_once("xajax_etoa/alliance.xajax.php");
	}
	
		
	$objAjax->processRequests();
?>