<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserPropertiesRepository;

$indexpage = [
    'login' => [
        'url' => '?index=login',
        'label' => 'Einloggen'
    ],
    'register' => [
        'url' => '?index=register',
        'label' => 'Registrieren'
    ],
    'pwforgot' => [
        'url' => '?index=pwforgot',
        'label' => 'Passwort'
    ],
    'contact' => [
        'url' => '?index=contact',
        'label' => 'Kontakt'
    ]
];

require_once __DIR__ . '/inc/bootstrap.inc.php';

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

$loggedIn = false;
if ($s->validate(0)) {
    $cu = new User($s->user_id);
    $loggedIn = (bool) $cu->isValid;
}

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

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

$additionalCss = [];
if (file_exists(CSS_STYLE . '/external.css')) {
    $additionalCss[] = CSS_STYLE . '/external.css';
} else {
    $additionalCss[] = 'web/css/external.css';
}
$twig->addGlobal('gameTitle', getGameIdentifier() . (isset($indexpage[$index]) ? ' - ' . $indexpage[$index]['label'] : ''));
$twig->addGlobal('templateDir', CSS_STYLE);
$twig->addGlobal('additionalCss', $additionalCss);
$twig->addGlobal('xajaxJS', $xajax->getJavascript());
$twig->addGlobal('bodyTopStuff', getInitTT());

//
// Page content
//

$show = true;
$invalidKey = false;
// Handle case if outgame key is set
if ($config->filled('register_key')) {
    if (isset($_POST['reg_key_auth_submit'])) {
        if ($_POST['reg_key_auth_value'] === $config->get('register_key')) {
            $s->reg_key_auth = $config->get('register_key');
        } else {
            $invalidKey = true;
        }
    }

    if ($s->reg_key_auth !== $config->get('register_key')) {
        $show = false;
    }
}

if ($loggedIn) {
    $show = true;
}

if ($show) {
    if ($index) {
        $index = $index === 'stats' ? 'ladder' : $index;
        $sub = 'index/';
        if (!preg_match('^[a-z\_]+$^', $index) || strlen($index) > 50) {
            echo $twig->render('external/invalid-page.html.twig', []);
            return;
        }

        $fileName = __DIR__ . '/' . $sub . $index . '.php';
        if (file_exists($fileName)) {
            $popup = true;
            include $fileName;
            logAccess($index, 'public');
            return;
        }

        echo $twig->render('external/404.html.twig', [
            'page' => $index,
        ]);
        return;
    }

    if ($page && $loggedIn && $page !== DEFAULT_PAGE) {
        ob_start();
        $popup = true;
        require('inc/content.inc.php');
        echo $twig->render('external/content.html.twig', [
            'content' => ob_get_clean(),
        ]);
        return;
    }

    echo $twig->render('external/index.html.twig', [
        'indexPages' => $indexpage,
    ]);
    return;
}

echo $twig->render('external/key-required.html.twig', [
    'page' => $_GET['index'],
    'invalidKey' => $invalidKey,
]);
return;
