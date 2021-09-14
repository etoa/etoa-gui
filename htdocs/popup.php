<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Design\Design;
use EtoA\User\UserPropertiesRepository;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/inc/bootstrap.inc.php';
$app = require __DIR__ . '/../src/app.php';

$request = Request::createFromGlobals();

/** @var ConfigurationService $config */
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

$design = Design::DIRECTORY . '/official/' . $config->get('default_css_style');
if (isset($cu) && filled($properties->cssStyle)) {
    if (is_dir(Design::DIRECTORY . '/official/' . $properties->cssStyle)) {
        $design = Design::DIRECTORY . '/official/' . $properties->cssStyle;
    }
}
define('CSS_STYLE', $design);

$errorMessage = null;

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
    'xajaxJS' => $xajax->getJavascript(),
    'bodyTopStuff' => getInitTT(),
    'errorMessage' => $errorMessage,
    'content' => ob_get_clean(),
    'gameTitle' => getGameIdentifier(),
]);
