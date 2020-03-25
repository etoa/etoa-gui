<?PHP

require_once __DIR__ . '/inc/bootstrap.inc.php';
$app = require __DIR__ . '/../src/app.php';

$loggedIn = false;
if ($s->validate(0)) {
    $cu = new CurrentUser($s->user_id);
    if ($cu->isValid) {
        $loggedIn = true;
    }
}

$design = DESIGN_DIRECTORY. '/official/' . $cfg->value('default_css_style');
if (isset($cu) && $cu->properties->cssStyle) {
    if (is_dir(DESIGN_DIRECTORY . '/custom/' . $cu->properties->cssStyle)) {
        $design = DESIGN_DIRECTORY . '/custom/' . $cu->properties->cssStyle;
    } else if (is_dir(DESIGN_DIRECTORY . '/official/' . $cu->properties->cssStyle)) {
        $design = DESIGN_DIRECTORY . '/official/' . $cu->properties->cssStyle;
    }
}
define('CSS_STYLE', $design);
if (isset($cu) && $cu->properties->imageUrl && $cu->properties->imageExt) {
    define('IMAGE_PATH',$cu->properties->imageUrl);
    define('IMAGE_EXT',$cu->properties->imageExt);
} else {
    define('IMAGE_PATH', $cfg->default_image_path->v);
    define('IMAGE_EXT', 'png');
}

$errorMessage = null;
try {
    ob_start();
    if ($loggedIn) {
        if ($page && $page !== DEFAULT_PAGE) {
            $popup = true;
            require __DIR__ . '/inc/content.inc.php';
        }
    } else {
        $errorMessage = 'Du bist nicht eingeloggt!';
    }

    echo $twig->render('layout/popup.html.twig', [
        'templateDir' => CSS_STYLE,
        'xajaxJS' => $xajax->getJavascript(XAJAX_DIR),
        'bodyTopStuff' => getInitTT(),
        'errorMessage' => $errorMessage,
        'content' => ob_get_clean(),
        'gameTitle' => getGameIdentifier(),
    ]);
} catch (DBException $ex) {
    ob_clean();
    echo $twig->render('layout/popup.html.twig', [
        'templateDir' => CSS_STYLE,
        'content' => $ex,
    ]);
} finally {
    dbclose();
}
