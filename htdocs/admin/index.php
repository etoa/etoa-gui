<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

ob_start();

try {
	require("inc/includer.inc.php");
} catch (DBException $ex) {
	ob_clean();
	echo $ex;
	exit();
}

// Create template object
$tpl = new TemplateEngine('admin/tpl');

$tpl->setLayout("default/default_main");
$tpl->setView("default");

$tpl->assign("css_theme", (!isset($themePath) || !is_file(RELATIVE_ROOT."/web/css/themes/admin/".$themePath."css")) ? "default" : $themePath);
$tpl->assign("page_title", getGameIdentifier()." Administration");
$tpl->assign("ajax_js", $xajax->printJavascript(XAJAX_DIR));

initTT();

try {

// Login if requested
if (isset($_POST['login_submit']))
{
	if (! $s->login($_POST))
	{
		include("inc/admin_login.inc.php");
	}
	if (!empty($_SERVER['QUERY_STRING'])) {
		forward("?".$_SERVER['QUERY_STRING']);
	} else {
		forward(".");
	}
}

// Perform logout if requested
if (isset($_GET['logout']) && $_GET['logout']!=null)
{
	$s->logout();
	forward('.',"Logout");
}

// Validate session
if (!$s->validate())
{
	include("inc/admin_login.inc.php");
}
else
{
	// Load admin user data
	$cu = new AdminUser($s->user_id);

	// Zwischenablage
	if (isset($_GET['cbclose']))
	{
		$s->clipboard = null;
	}
	$cb = isset ($s->clipboard) && $s->clipboard==1 ? true : false;
	
	
	$tpl->assign("search_query",(isset($_POST['search_query']) ? $_POST['search_query'] : '' ));

	$navmenu = fetchJsonConfig("admin-menu.conf");
	$tpl->assign("navmenu",$navmenu);
	
	$tpl->assign("page",$page);
	$tpl->assign("sub", $sub);
	$tpl->assign("time", time());

	$nres = dbquery("select COUNT(*) from admin_notes where admin_id='".$s->user_id."'");
	$narr = mysql_fetch_row($nres);
	$tpl->assign("num_notes", $narr[0]);
	$tpl->assign("num_tickets", Ticket::countAssigned($s->user_id) + Ticket::countNew());

	$tpl->assign("current_user_nick", $cu->nick);	
	$tpl->assign("user_roles", $cu->roles);	
	
	// Status widget
	$tpl->assign("is_unix", UNIX);
	if (UNIX) {
		$tpl->assign("eventhandler_pid", checkDaemonRunning($cfg->daemon_pidfile));
		intval(exec("cat /proc/cpuinfo | grep processor | wc -l", $out));
		$load = sys_getloadavg();
		$tpl->assign("sys_load", round($load[2]/intval($out[0])*100, 2) );
	}
	
	$ures=dbquery("SELECT count(*) FROM users;");
	$uarr=mysql_fetch_row($ures);

	$gres=dbquery("SELECT COUNT(*) FROM user_sessions WHERE time_action>".(time() - $cfg->user_timeout->v).";");
	$garr=mysql_fetch_row($gres);

	$a1res=dbquery("SELECT COUNT(*)  FROM admin_user_sessions WHERE time_action>".(time() - $cfg->admin_timeout->v).";");
	$a1arr=mysql_fetch_row($a1res);

	$tpl->assign("users_online", $garr[0]);
	$tpl->assign("users_count", $uarr[0]);
	$tpl->assign("users_allowed", $cfg->enable_register->p2);
	$tpl->assign("admins_online", $a1arr[0]);
	$tpl->assign("admins_count", AdminUser::countAll());
	$tpl->assign("db_size", DBManager::getInstance()->getDbSize());
	
	$tpl->assign("side_nav_widgets", $tpl->getChunk("status_widget"));
			
	// Inhalt einbinden
	if (isset($_GET['adminlist']))
	{
		require("inc/adminlist.inc.php");
	}
	elseif (isset($_GET['myprofile']))
	{
		require("inc/myprofile.inc.php");
	}
	else
	{
		// Check permissions
		$allow_inc=false;
		$found = false;
		$rm = new AdminRoleManager();
		
		foreach ($navmenu as $cat=> $item) 	{
			if ($item['page']==$page && $sub=="") {
				$found = true;
				if ($rm->checkAllowed($item['roles'], $cu->roles)) {
					$allow_inc = true;
					break;
				}
			} else if (isset($item['children'])) {
				foreach ($item['children'] as $title=> $data) {
					if ($item['page']==$page && $data['sub']==$sub) {
						$found = true;
						if ($rm->checkAllowed($data['roles'], $cu->roles)) {
							$allow_inc = true;
							break;
						}
					}
				}
			}
		}
		if ($allow_inc || !$found)	{
			if (preg_match('^[a-z\_]+$^',$page)  && strlen($page)<=50) {
				$contentFile = "content/".$page.".php";
				if (is_file($contentFile)) {
					include($contentFile);
					logAccess($page,"admin",$sub);
				} else {
					cms_err_msg("Die Seite $page wurde nicht gefunden!");
				}
			} else {
				echo "<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br><br><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
			}
		} else {
			echo "<h1>Kein Zugriff</h1> Du hast keinen Zugriff auf diese Seite!";
		}
	}
	
	// Write all changes of $s to the session variable
	$_SESSION[SESSION_NAME]=$s;
	dbclose();

	$tpl->assign("content_overflow", ob_get_clean());
	$render_time = explode(" ",microtime());

	$tpl->assign("render_time",round($render_time[1]+$render_time[0]-$render_starttime,3));

	$tpl->render();
}
} catch (DBException $ex) {
	ob_clean();
	$tpl->setLayout("default/default_popup");
	$tpl->assign("content_overflow", $ex);
	$tpl->render();
}
?>