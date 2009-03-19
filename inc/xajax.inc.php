<?PHP
 /**
 * XAJAX definitions, creates xajax-object and includes page specific xajax functions
 *
 * @author MrCage mrcage@etoa.ch
 * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
 */		

	require_once(XAJAX_DIR."/xajax_core/xajax.inc.php");
	$xajax = new xajax();
	$xajax->setFlag('debug',false);

	require_once("inc/xajax/global.xajax.php");
	
	if (isset($page))
	{
		if ($page=="haven")
		{
			require_once("inc/xajax/haven.xajax.php");
		}
		elseif ($page=="stats")
		{
			require_once("inc/xajax/stats.xajax.php");
		}
		elseif ($page=="alliance")
		{
			require_once("inc/xajax/alliance.xajax.php");
		}
		elseif ($page=="messages")
		{
			require_once("inc/xajax/messages.xajax.php");
		}
		elseif ($page=="userconfig")
		{
			require_once("inc/xajax/userconfig.xajax.php");
		}
		elseif ($page=="cell")
		{
			require_once("inc/xajax/cell.xajax.php");
		}
		elseif ($page=="market")
		{
			require_once("inc/xajax/market.xajax.php");
		}
		elseif ($page=="techtree" || $page=="help")
		{
			require_once("inc/xajax/techtree.xajax.php");
		}
		elseif ($page=="bookmarks")
		{
			require_once("inc/xajax/bookmarks.xajax.php");
		}
	}	
		
	if (isset($index))
	{
		if ($index=="register")
		{
			require_once("inc/xajax/register.xajax.php");
		}
		elseif ($index=="ladder" || $index=="stats")
		{
			require_once("inc/xajax/ladder.xajax.php");
		}
	}	
	

		
	$xajax->processRequest();
?>