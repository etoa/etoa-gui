<?PHP
 /**
 * XAJAX definitions, creates xajax-object and includes page specific xajax functions
 *
 * @author MrCage mrcage@etoa.ch
 * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
 */		

	require_once(XAJAX_DIR."/xajax_core/xajax.inc.php");
	$xajax = new xajax();
	$xajax->configure("javascript URI", XAJAX_DIR);
	$xajax->configure('debug', (defined('XAJAX_DEBUG') && XAJAX_DEBUG));

	require_once(RELATIVE_ROOT."inc/xajax/global.xajax.php");
	
	if (isset($page))
	{
		if ($page=="haven")
		{
			require_once(RELATIVE_ROOT."inc/xajax/haven.xajax.php");
		}
		elseif ($page=="stats")
		{
			require_once(RELATIVE_ROOT."inc/xajax/stats.xajax.php");
		}
		elseif ($page=="alliance")
		{
			require_once(RELATIVE_ROOT."inc/xajax/alliance.xajax.php");
		}
		elseif ($page=="messages")
		{
			require_once(RELATIVE_ROOT."inc/xajax/messages.xajax.php");
		}
		elseif ($page=="reports")
		{
			require_once(RELATIVE_ROOT."inc/xajax/reports.xajax.php");
		}
		elseif ($page=="userconfig")
		{
			require_once(RELATIVE_ROOT."inc/xajax/userconfig.xajax.php");
		}
		elseif ($page=="cell" || $page=="sector")
		{
			require_once(RELATIVE_ROOT."inc/xajax/cell.xajax.php");
		}
		elseif ($page=="market")
		{
			require_once(RELATIVE_ROOT."inc/xajax/market.xajax.php");
		}
		elseif ($page=="techtree" || $page=="help")
		{
			require_once(RELATIVE_ROOT."inc/xajax/techtree.xajax.php");
		}
		elseif ($page=="bookmarks")
		{
			require_once(RELATIVE_ROOT."inc/xajax/bookmarks.xajax.php");
		}
	}	
		
	if (isset($index))
	{
		if ($index=="register")
		{
			require_once(RELATIVE_ROOT."inc/xajax/register.xajax.php");
		}
	}	
	
	$xajax->processRequest();
?>