<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserPropertiesRepository;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/inc/bootstrap.inc.php';
$app = require __DIR__ . '/../src/app.php';

$request = Request::createFromGlobals();

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$loggedIn = false;
if ($s->validate(0)) {
    $cu = new User($s->user_id);
    if ($cu->isValid) {
        $loggedIn = true;
    }
}

$properties = isset($cu) ? $userPropertiesRepository->getOrCreateProperties($cu->id) : null;

$design = DESIGN_DIRECTORY . '/official/' . $config->get('default_css_style');
if (isset($cu) && filled($properties->cssStyle)) {
    if (is_dir(DESIGN_DIRECTORY . '/custom/' . $properties->cssStyle)) {
        $design = DESIGN_DIRECTORY . '/custom/' . $properties->cssStyle;
    } else if (is_dir(DESIGN_DIRECTORY . '/official/' . $properties->cssStyle)) {
        $design = DESIGN_DIRECTORY . '/official/' . $properties->cssStyle;
    }
}
define('CSS_STYLE', $design);
if (isset($cu) && filled($properties->imageUrl) && filled($properties->imageExt)) {
    define('IMAGE_PATH', $properties->imageUrl);
    define('IMAGE_EXT', $properties->imageExt);
} else {
    define('IMAGE_PATH', $config->get('default_image_path'));
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
