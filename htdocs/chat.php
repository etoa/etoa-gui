<?PHP
/* fastchat von river */

define('RELATIVE_ROOT','');
include_once __DIR__ . '/inc/bootstrap.inc.php';

$login = false;
$chatColor = null;
$errorMessage = null;
if (isset($_SESSION['user_id'])) {
    $login = true;

    $res = dbquery("
        SELECT * FROM
            chat_banns
        WHERE
            user_id=" . $_SESSION['user_id'] . ";");
    if (!isset($res)) {
        $errorMessage = 'Irgend etwas lief schief. Versuche den Chat neu zu laden.';
    } elseif (mysql_num_rows($res) > 0) {
        $arr = mysql_fetch_assoc($res);
        $errorMessage = 'Du wurdest vom Chat gebannt!<br/><br/><b>Grund:</b> ' . $arr['reason'];
    } else {
        $cu = new CurrentUser($_SESSION['user_id']);
        $_SESSION['ccolor'] = $cu->properties->chatColor;
        $chatColor = $cu->properties->chatColor;
    }
}

// Select design
$design = DESIGN_DIRECTORY."/official/".$cfg->value('default_css_style');
if (isset($cu) && $cu->properties->cssStyle) {
    if (is_dir(DESIGN_DIRECTORY."/custom/".$cu->properties->cssStyle)) {
        $design = DESIGN_DIRECTORY."/custom/".$cu->properties->cssStyle;
    } else if (is_dir(DESIGN_DIRECTORY."/official/".$cu->properties->cssStyle)) {
        $design = DESIGN_DIRECTORY."/official/".$cu->properties->cssStyle;
    }
}
define('CSS_STYLE', $design);

// Chat design css
if (file_exists(CSS_STYLE."/chat.css")) {
$chatCss = CSS_STYLE."/chat.css";
} else {
$chatCss = 'web/css/chat.css';
}

echo $twig->render('layout/chat.html.twig', [
    'login' => $login,
    'chatCss' => $chatCss,
    'chatColor' => $chatColor,
    'errorMessage' => $errorMessage,

]);

dbclose();
