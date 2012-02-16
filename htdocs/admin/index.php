<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
// $Id$
//////////////////////////////////////////////////////

ob_start();

require("inc/includer.inc.php");

ini_set('display_errors', 1);

$tpl->assign("theme_path",(!isset($themePath) || !is_file("themes/".$themePath)) ? "default.css" : $themePath);
$tpl->assign("page_title",$conf['game_name']['v'].' '.$conf['game_name']['p1'].' Administration - '.Config::getInstance()->roundname->v);
$tpl->assign("axaj_js",$xajax->printJavascript(XAJAX_DIR));

$tpl->assign("round_name",Config::getInstance()->roundname->v);

initTT();

$view = "admin/default";

// Login if requested
if (isset($_POST['login_submit']))
{
	if (! $s->login($_POST))
	{
		include("inc/admin_login.inc.php");
		$tpl->display("layouts/admin/default_login.html");
		exit;		
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
	$tpl->display("layouts/admin/default_login.html");
	exit;		
}
else
{
	// Load admin user data
	$cu = new AdminUser($s->user_id);

	// Monitor admin's actions
	// $s->monitor();

	// Zwischenablage
	if (isset($_GET['cbclose']))
	{
		$s->clipboard = null;
	}
	$cb = isset ($s->clipboard) && $s->clipboard==1 ? true : false;
	
	$tpl->assign("search_query",(isset($_POST['search_query']) ? $_POST['search_query'] : '' ));
	$tpl->assign("user_level",$cu->level);

	$menuFile = "../config/admin-menu.conf";
	$navmenu = json_decode(file_get_contents($menuFile),true);
	$tpl->assign("navmenu",$navmenu);
	
	$tpl->assign("page",$page);
	$tpl->assign("sub",$sub);
	$tpl->assign("time",time());

	$nres = dbquery("select COUNT(*) from admin_notes where admin_id='".$s->user_id."'");
	$narr = mysql_fetch_row($nres);
	$tpl->assign("num_notes",$narr[0]);
	$tpl->assign("num_new_tickets",Ticket::countAssigned($s->user_id) + Ticket::countNew());

	$tpl->assign("current_user_nick",$cu->nick);	
	
					
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
		// Activate update system
		if (isset($_GET['activateupdate']) && $_GET['activateupdate']==1)
		{
			Config::getInstance()->set("update_enabled",1);
		}

		if (Config::getInstance()->update_enabled->v !=1 )
		{
			echo "<br/>";
			iBoxStart("Updates deaktiviert");
			echo "Die Updates sind momentan deaktiviert!";
			echo " <a href=\"?page=$page&amp;activateupdate=1\">Aktivieren</a>";
			iBoxEnd();
		}

		// Check permissions
		$allow_inc=false;
		$rank="";
		foreach ($navmenu as $cat=> $item)
		{
			foreach ($item['children'] as $title=> $data)
			{
				if ($item['page']==$page && $data['sub']==$sub)
				{
					$rank=$data['level'];
					if ($data['level'] <= $cu->level)
						$allow_inc=true;
				}
			}
		}
		
		if ($allow_inc || $rank=="")
		{
			if (preg_match('^[a-z\_]+$^',$page)  && strlen($page)<=50)
			{
				$contentFile = "content/".$page.".php";
				if (is_file($contentFile))
				{
					include($contentFile);
					logAccess($page,"admin",$sub);
				}
				else
				{
					cms_err_msg("Die Seite $page wurde nicht gefunden!");
				}
			}
			else
				echo "<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br><br><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
		}
		else
		{
			echo "<h1>Kein Zugriff</h1> Du hast keinen Zugriff auf diese Seite!<br/><br/> Erwartet: <b>".$adminlevel[$rank]." ($rank)</b>, du bist <b>".$_SESSION[SESSION_NAME]['group_name']." (".$_SESSION[SESSION_NAME]['group_level'].")</b>.";
		}
	}
	
	// Write all changes of $s to the session variable
	$_SESSION[SESSION_NAME]=$s;
	dbclose();

	$tpl->assign("content_for_layout", $tpl->fetch("views/".$view.".html"));
	$tpl->assign("content_overflow", ob_get_clean());

	$render_time = explode(" ",microtime());
	$tpl->assign("render_time",round($render_time[1]+$render_time[0]-$render_starttime,3));

	$tpl->display("layouts/admin/default_main.html");
	exit;
}
?>


