<?PHP
/* fastchat von river */

use EtoA\Chat\ChatBanRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Design\Design;
use EtoA\User\UserPropertiesRepository;

include_once __DIR__ . '/inc/bootstrap.inc.php';

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

/** @var ChatBanRepository $chatBanRepository */
$chatBanRepository = $app[ChatBanRepository::class];

$login = false;
$chatColor = null;
$errorMessage = null;
$properties = null;
if (isset($_SESSION['user_id'])) {
    $login = true;

    $chatBan = $chatBanRepository->getUserBan((int) $_SESSION['user_id']);
    if ($chatBan !== null) {
        $errorMessage = 'Du wurdest vom Chat gebannt!<br/><br/><b>Grund:</b> ' . $chatBan->reason;
    } else {
        $cu = new User($_SESSION['user_id']);
        $properties = $userPropertiesRepository->getOrCreateProperties($cu->id);
        $_SESSION['ccolor'] = $properties->chatColor;
        $chatColor = $properties->chatColor;
    }
}

// Select design
$design = Design::DIRECTORY . "/official/" . $config->get('default_css_style');
if (isset($cu) && $properties !== null && filled($properties->cssStyle)) {
    if (is_dir(Design::DIRECTORY . "/official/" . $properties->cssStyle)) {
        $design = Design::DIRECTORY . "/official/" . $properties->cssStyle;
    }
}
define('CSS_STYLE', $design);

// Chat design css
if (file_exists(CSS_STYLE . "/chat.css")) {
    $chatCss = CSS_STYLE . "/chat.css";
} else {
    $chatCss = 'web/css/chat.css';
}

echo $twig->render('layout/chat.html.twig', [
    'login' => $login,
    'chatCss' => $chatCss,
    'chatColor' => $chatColor,
    'errorMessage' => $errorMessage,

]);
