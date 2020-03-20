<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

/**
 * Alternative main file for out of game viewing of specific pages
 *
 * @author MrCage mrcage@etoa.ch
 * @copyright Copyright (c) 2004 EtoA Gaming, www.etoa.ch
 */

$indexpage = [
    'login' => [
        'url'=>'?index=login',
        'label'=>'Einloggen'
    ],
    'register' => [
        'url'=>'?index=register',
        'label'=>'Registrieren'
    ],
    'pwforgot' => [
        'url'=>'?index=pwforgot',
        'label'=>'Passwort'
    ],
    'contact' => [
        'url'=>'?index=contact',
        'label'=>'Kontakt'
    ]
];

require_once __DIR__ . '/inc/bootstrap.inc.php';

$loggedIn = false;
if ($s->validate(0)) {
    $cu = new CurrentUser($s->user_id);
    $loggedIn = (bool) $cu->isValid;
}

$design = DESIGN_DIRECTORY . '/official/' . $cfg->value('default_css_style');
if (isset($cu) && $cu->properties->cssStyle) {
    if (is_dir(DESIGN_DIRECTORY . '/custom/' . $cu->properties->cssStyle)) {
        $design = DESIGN_DIRECTORY . '/custom/' . $cu->properties->cssStyle;
    } else if (is_dir(DESIGN_DIRECTORY . '/official/' . $cu->properties->cssStyle)) {
        $design = DESIGN_DIRECTORY . '/official/' . $cu->properties->cssStyle;
    }
}
define('CSS_STYLE', $design);
if (isset($cu) && $cu->properties->imageUrl && $cu->properties->imageExt) {
    define('IMAGE_PATH', $cu->properties->imageUrl);
    define('IMAGE_EXT', $cu->properties->imageExt);
} else {
    define('IMAGE_PATH', $cfg->default_image_path->v);
    define('IMAGE_EXT', 'png');
}

$additionalCss = [];
if (file_exists(CSS_STYLE . '/external.css')) {
    $additionalCss[] = CSS_STYLE . '/external.css';
} else {
    $additionalCss[] = 'web/css/external.css';
}
$twig->addGlobal('gameTitle', getGameIdentifier().(isset($indexpage[$index]) ? ' - '.$indexpage[$index]['label'] : ''));
$twig->addGlobal('templateDir', CSS_STYLE);
$twig->addGlobal('additionalCss', $additionalCss);
$twig->addGlobal('xajaxJS', $xajax->getJavascript(XAJAX_DIR));
$twig->addGlobal('bodyTopStuff', getInitTT());

//
// Page content
//
try {
    $show = true;
    $invalidKey = false;
    // Handle case if outgame key is set
    if ($cfg->register_key->v) {
        if (isset($_POST['reg_key_auth_submit'])) {
            if ($_POST['reg_key_auth_value'] === $cfg->register_key->v) {
                $s->reg_key_auth = $cfg->register_key->v;
            } else {
                $invalidKey = true;
            }
        }

        if ($s->reg_key_auth !== $cfg->register_key->v) {
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
            if (!preg_match('^[a-z\_]+$^',$index) || strlen($index) > 50) {
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
} catch (DBException $ex) {
    echo $twig->render('external/content.html.twig', [
        'content' => $ex,
    ]);
}
