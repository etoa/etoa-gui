<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

ob_start();

require("inc/includer.inc.php");

try {

// Create template object
$tpl = new TemplateEngine('admin/tpl');

$tpl->setLayout("default/popup");
$tpl->setView("default");

$tpl->assign("css_theme", (!isset($themePath) || !is_file(RELATIVE_ROOT."/web/css/themes/admin/".$themePath."css")) ? "default" : $themePath);
$tpl->assign("page_title", getGameIdentifier()." Administration");
$tpl->assign("ajax_js", $xajax->getJavascript(XAJAX_DIR));

$tpl->assign("bodyTopStuff", getInitTT());

if ($s->user_id)
{
	$cu = new AdminUser($s->user_id);
	
	if (preg_match('/^[a-z\_]+$/',$page)  && strlen($page)<=50)
	{
		if (!include("content/".$page.".php"))
			cms_err_msg("Die Seite $page wurde nicht gefunden!");
	}
	else {
		echo "<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br><br><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
	}	
}
else {
	cms_err_msg("Die Seite wurde nicht gefunden!");
}

$tpl->assign("content_overflow", ob_get_clean());
$tpl->render();
} catch (DBException $ex) {
	ob_clean();
	$tpl->setLayout("default/popup");
	$tpl->assign("content_overflow", $ex);
	$tpl->render();
}
	
?>