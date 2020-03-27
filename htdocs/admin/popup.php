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
    $twig->addGlobal('cssTheme', $css_theme);
    $twig->addGlobal('pageTitle', getGameIdentifier() . ' Administration');
    $twig->addGlobal('ajax_js', $xajax->getJavascript(XAJAX_DIR));
    $twig->addGlobal('bodyTopStuff', getInitTT());

    if ($s->user_id) {
        $cu = new AdminUser($s->user_id);

        if (preg_match('/^[a-z\_]+$/',$page)  && strlen($page) <= 50) {
            if (!include("content/".$page.".php"))
                echo "<h1>Fehler</h1>Die Seite $page wurde nicht gefunden!";
        } else {
            echo "<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br><br><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
        }
    } else {
        echo "<h1>Fehler</h1>Die Seite wurde nicht gefunden!";
    }

    echo $twig->render('admin/layout/popup.html.twig', [
        'content' => ob_get_clean(),
    ]);
} catch (DBException $ex) {
    require_once __DIR__ . '/../../src/minimalapp.php';
    echo $app['twig']->render('layout/empty.html.twig', [
        'content' => $ex,
    ]);
}
