<?PHP

use EtoA\User\UserRepository;

ob_start();

require("inc/includer.inc.php");

$adminUserRepo = $app['etoa.admin.user.repository'];
$userRepo = $app['etoa.user.repository'];

$twig->addGlobal('cssTheme', $css_theme);
$twig->addGlobal('pageTitle', getGameIdentifier() . " Administration");
$twig->addGlobal('ajaxJs', $xajax->getJavascript(XAJAX_DIR));

if ($s->user_id) {
    clipboardIndex($userRepo);
} else {
    echo "Nicht eingeloggt!";
}
echo $twig->render('admin/layout/popup.html.twig', [
    'content' => ob_get_clean(),
]);

function clipboardIndex(UserRepository $userRepo)
{
    echo "<h1>Zwischenablage</h1>";

    if (isset($_GET['add_user']) && $_GET['add_user'] > 0) {
        $_SESSION['cp_users'][$_GET['add_user']] = $_GET['add_user'];
    }
    if (isset($_GET['rem_user']) && $_GET['rem_user'] > 0) {
        $_SESSION['cp_users'][$_GET['rem_user']] = null;
    }

    echo "<h2>Benutzer [<a href=\"index.php?page=overview&amp;sub=stats\" target=\"main\">alle</a>]</h2>";
    if (isset($_SESSION['cp_users']) && count($_SESSION['cp_users']) > 0) {
        echo "<ul style=\"list-style-type:none;margin-left:-20px;\">";
        foreach ($_SESSION['cp_users'] as $uid) {
            if ($uid > 0) {
                $userNick = $userRepo->getNick($uid);
                if ($userNick != null) {
                    echo "<div id=\"ttuser" . $uid . "\" style=\"display:none;\">
                    <a href=\"index.php?page=user&amp;sub=edit&amp;id=" . $uid . "\" target=\"main\">Daten anzeigen</a><br/>
                    " . popupLink("sendmessage", "Nachricht senden", "", "id=" . $uid) . "<br/>
                    <a href=\"?rem_user=" . $uid . "\" target=\"_self\">Entfernen</a>
                    </div>";

                    echo "<li><a href=\"index.php?page=user&amp;sub=edit&amp;user_id=" . $uid . "\" target=\"main\" " . cTT($userNick, "ttuser" . $uid) . ">
                    " . $userNick . "</a></li>";
                }
            }
        }
        echo "</ul>";
    } else {
        echo "<i>Nichts vorhanden!</i><br/><br/>";
    }

    echo "<br/><br/>
    [<a href=\"?\" target=\"_self\">Aktualisieren</a>]
    [<a href=\"index.php?cbclose=1\" target=\"_top\">Schliessen</a>]";
}
