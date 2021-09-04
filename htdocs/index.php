<?PHP

/**
 * Main game file, provides the template and includes all pages
 */

use EtoA\BuddyList\BuddyListRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Notepad\NotepadRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;

//
// Basics
//
require_once __DIR__ . '/../vendor/autoload.php';

// Render time measurement
$watch = new \Symfony\Component\Stopwatch\Stopwatch();
$watch->start('render');
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

// Funktionen und Config einlesen
try {
    require_once __DIR__ . '/inc/bootstrap.inc.php';
} catch (DBException $ex) {
    require_once __DIR__ . '/../src/minimalapp.php';
    echo $app['twig']->render('layout/empty.html.twig', [
        'content' => $ex,
    ]);
    exit;
}

// Set no-cache header
header("Cache-Control: no-cache, must-revalidate");

//
// User and session checks
//

// Login if requested
if (isset($_POST['login'])) {
    if (!$s->login($_POST)) {
        forward(getLoginUrl(['page' => 'err', 'err' => $s->lastErrorCode]), 'Loginfehler', $s->lastError);
    }

    forward('.');
}

// Check for modified etoa tool by pain
if ($_GET['ttool'] ?? false) {
    file_put_contents('cache/log/paintool.log', sprintf("[%s] Pain's modified tool used by %s (%s) from %s on %s\n", date('d.m.Y, H:i:s'), $_POST['login_nick'], $s->user_id, $_SERVER['REMOTE_ADDR'], $_GET['page'] ?? 'index'), FILE_APPEND);
}


// Perform logout if requested
if ($_GET['logout'] ?? false) {
    $s->logout();
    forward(getLoginUrl(['page' => 'logout']), 'Logout');
}

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

// Validate session
if (!$s->validate()) {

    if (!$config->get('loginurl')) {
        forward(getLoginUrl());
    } else {
        forward(getLoginUrl(['page' => 'err', 'err' => 'nosession']), 'Ungültige Session', $s->lastError);
    }
}

// Load user data
$cu = new User($s->user_id);

// Check if it is valid user
if (!$cu->isValid) {
    forward(getLoginUrl(['page' => 'err', 'err' => 'usernotfound']), 'Benutzer nicht mehr vorhanden');
}

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

//
// Design / layout properties
//

if (!defined('CSS_STYLE')) {
    $design = DESIGN_DIRECTORY . "/official/" . $config->get('default_css_style');
    if (filled($properties->cssStyle)) {
        if (is_dir(DESIGN_DIRECTORY . "/custom/" . $properties->cssStyle)) {
            $design = DESIGN_DIRECTORY . "/custom/" . $properties->cssStyle;
        } else if (is_dir(DESIGN_DIRECTORY . "/official/" . $properties->cssStyle)) {
            $design = DESIGN_DIRECTORY . "/official/" . $properties->cssStyle;
        }
    }
    define('CSS_STYLE', $design);
}

//
// Page content
//

// Referers prüfen
$referer_allow = false;
if (isset($_SERVER["HTTP_REFERER"])) {
    // Referers
    $referers = explode("\n", $config->get('referers'));
    foreach ($referers as $k => &$v) {
        $referers[$k] = trim($v);
    }
    unset($v);
    $referers[] = 'http://' . $_SERVER['HTTP_HOST'];
    foreach ($referers as &$rfr) {
        if (substr($_SERVER["HTTP_REFERER"], 0, strlen($rfr)) === $rfr) {
            $referer_allow = true;
        }
    }
    unset($rfr);
}

try {
    ob_start();

    // Spiel ist generell gesperrt (ausser fŸr erlaubte IP's)
    $allowed_ips = explode("\n", $config->get('offline_ips_allow'));

    if ($config->getBoolean('offline') && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips, true)) {
        iBoxStart('Spiel offline', 750);
        echo "<img src=\"images/maintenance.jpg\" alt=\"maintenance\" /><br/><br/>";
        if (filled($config->get('offline_message'))) {
            echo BBCodeUtils::toHTML($config->get('offline_message')) . "<br/><br/>";
        } else {
            echo "Das Spiel ist aufgrund von Wartungsarbeiten momentan offline! Schaue sp&auml;ter nochmals vorbei!<br/><br/>";
        }
        echo button("Zur Startseite", getLoginUrl());
        iBoxEnd();
    }

    // Login ist gesperrt
    elseif (!$config->getBoolean('enable_login') && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips, true)) {
        iBoxStart("Login geschlossen", 750);
        echo "<img src=\"images/keychain.png\" alt=\"maintenance\" /><br/><br/>";
        echo "Der Login momentan geschlossen!<br/><br/>";
        echo button("Zur Startseite", getLoginUrl());
        iBoxEnd();
    }

    // Login ist erlaubt aber noch zeitlich zu frŸh
    elseif ($config->getBoolean('enable_login') && $config->param1Int('enable_login') > time() && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips, true)) {
        iBoxStart("Login noch geschlossen", 750);
        echo "<img src=\"images/keychain.png\" alt=\"maintenance\" /><br/><br/>";
        echo "Das Spiel startet am " . date("d.m.Y", $config->param1Int('enable_login')) . " ab " . date("H:i", $config->param1Int('enable_login')) . "!<br/><br/>";
        echo button("Zur Startseite", getLoginUrl());
        iBoxEnd();
    }

    // Zugriff von anderen als eigenem Server bzw Login-Server sperren
    elseif (!$referer_allow && isset($_SERVER["HTTP_REFERER"])) {
        echo "<div style=\"text-align:center;\">
        <h1>Falscher Referer</h1>
        Der Zugriff auf das Spiel ist nur anderen internen Seiten aus m&ouml;glich! Ein externes Verlinken direkt in das Game hinein ist nicht gestattet! Dein Referer: " . $_SERVER["HTTP_REFERER"] . "<br/><br/>
        <a href=\"" . getLoginUrl() . "\">Hauptseite</a></div>";
    }

    // Zugriff erlauben und Inhalt anzeigen
    else {
        if ($s->firstView && $properties->startUpChat == 1) {
            echo "<script type=\"text/javascript\">" . CHAT_ONCLICK . "</script>";
        }

        if ($cu->isSetup()) {
            /** @var PlanetRepository $planetRepository */
            $planetRepository = $app[PlanetRepository::class];
            $userPlanets = $planetRepository->getUserPlanets((int) $cu->id);
            $planets = [];
            $mainplanet = 0;
            foreach ($userPlanets as $planet) {
                $planets[] = $planet->id;
                if ($planet->mainPlanet) {
                    $mainplanet = $planet->id;
                }
            }
            // Todo: check if mainplanet is still 0

            // Wenn eine ID angegeben wurde (Wechsel des Planeten) wird diese ŸberprŸft
            //if (!isset($s->echng_key))
            //	$s->echng_key = mt_rand(100,9999999);

            $eid = isset($_GET['change_entity']) ? (int) $_GET['change_entity'] : 0;
            if ($eid > 0 && in_array($eid, $planets, true)) {
                $cpid = $eid;
                $s->cpid = $cpid;
            } elseif (isset($s->cpid) && in_array((int) $s->cpid, $planets, true)) {
                $cpid = $s->cpid;
            } else {
                $cpid = $mainplanet;
                $s->cpid = $cpid;
            }

            $cp = Planet::getById($cpid);
            $pm = new PlanetManager($planets);
        } else {
            $cu->setNotSetup();
        }
    }

    // Count Messages
    /** @var MessageRepository $messageRepository */
    $messageRepository = $app[MessageRepository::class];
    $newMessages = $messageRepository->countNewForUser($cu->id);

    // Check new reports
    /** @var ReportRepository $reportRepository */
    $reportRepository = $app[ReportRepository::class];
    $newReports = $reportRepository->countUserUnread($cu->getId());

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $userCount = $userRepository->count();

    /** @var UserSessionRepository $userSessionRepository */
    $userSessionRepository = $app[UserSessionRepository::class];
    $usersOnline = $userSessionRepository->count();

    // Count notes
    /** @var NotepadRepository $notepadRepository */
    $notepadRepository = $app[NotepadRepository::class];
    $numNotes = $notepadRepository->count($cu->id);

    // Number of player's own fleets
    /** @var FleetRepository $fleetRepository */
    $fleetRepository = $app[FleetRepository::class];
    $ownFleetCount = $fleetRepository->count(FleetSearch::create()->user($cu->getId()));

    if (isset($cp, $pm)) {
        $currentPlanetData = [
            'currentPlanetName' => $cp,
            'currentPlanetImage' => $cp->imagePath('m'),
            'planetList' => $pm->getLinkList($s->cpid, $page, $mode),
            'nextPlanetId' => $pm->nextId($s->cpid),
            'prevPlanetId' => $pm->prevId($s->cpid),
            'selectField' => $pm->getSelectField($s->cpid),
        ];
    } else {
        $currentPlanetData = [
            'currentPlanetName' => 'Unbekannt',
            'planetList' => [],
            'nextPlanetId' => 0,
            'prevPlanetId' => 0,
            'selectField' => null,
        ];
    }

    $gameMenu = new GameMenu('game-menu.conf');

    if (ADD_BANNER == "") {
        $twig->addGlobal('adds', false);
    } elseif ($properties->showAdds || FORCE_ADDS == 1) {
        $twig->addGlobal('adds', true);
    } else {
        $twig->addGlobal('adds', false);
    }

    /** @var TextRepository $textRepo */
    $textRepo = $app[TextRepository::class];
    $infoText = $textRepo->find('info');

    /** @var BuddyListRepository $buddyListRepository */
    $buddyListRepository = $app[BuddyListRepository::class];

    $globals = array_merge($currentPlanetData, [
        'design' => strtolower(str_replace('designs/official/', '', CSS_STYLE)),
        'addBanner' => ADD_BANNER,
        'gameTitle' => getGameIdentifier(),
        'templateDir' => CSS_STYLE,
        'xajaxJS' => $xajax->getJavascript(XAJAX_DIR),
        'bodyTopStuff' => getInitTT(),
        'ownFleetCount' => $ownFleetCount,
        'messages' => $newMessages,
        'newreports' => $newReports,
        'blinkMessages' => $properties->msgBlink,
        'buddys' => $buddyListRepository->countFriendsOnline($cu->getId()),
        'buddyreq' => $buddyListRepository->hasPendingFriendRequest($cu->getId()),
        'fleetAttack' => check_fleet_incomming($cu->id),
        'enableKeybinds' => $properties->enableKeybinds,
        'isAdmin' => $cu->admin,
        'usersOnline' => $usersOnline,
        'usersTotal' => $userCount,
        'notes' => $numNotes,
        'userPoints' => StringUtils::formatNumber($cu->points),
        'userNick' => $cu->nick,
        'page' => $page,
        'mode' => $mode,
        'topNav' => $gameMenu->getTopNav(),
        'mainNav' => $gameMenu->getMainNav(),
        'renderTime' => $watch->stop('render')->getDuration() / 1000,
        'infoText' => $infoText->isEnabled() ? $infoText->content : null,
        'helpBox' => $properties->helpBox,
        'noteBox' => $properties->noteBox,
    ]);
    foreach ($globals as $key => $value) {
        $twig->addGlobal($key, $value);
    }

    // Include content
    require __DIR__ . '/inc/content.inc.php';

    echo $twig->render('layout/game.html.twig', [
        'content' => ob_get_clean(),
    ]);
} catch (DBException $e) {
    ob_clean();
    echo $twig->render('layout/empty.html.twig', [
        'content' => $e,
    ]);
} catch (\Throwable $exception) {
    throw $exception;
} finally {
    $_SESSION['lastpage'] = $page;

    DBManager::getInstance()->close();
}
