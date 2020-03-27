<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

/**
* Main game file, provides the template and includes all pages
*
* @author MrCage <mrcage@etoa.ch>
* @copyright Copyright (c) 2004 EtoA Gaming, www.etoa.ch
*/

//
// Basics
//
require_once __DIR__ . '/../vendor/autoload.php';

// Render time measurement
$watch = new \Symfony\Component\Stopwatch\Stopwatch();
$watch->start('render');

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
        forward(getLoginUrl(['page'=>'err', 'err' => $s->lastErrorCode]), 'Loginfehler', $s->lastError);
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
    forward(getLoginUrl(['page'=>'logout']), 'Logout');
}

// Validate session
if (!$s->validate()) {
    if (empty(Config::getInstance()->loginurl->v)) {
        forward(getLoginUrl());
    } else {
        forward(getLoginUrl(['page'=>'err', 'err'=>'nosession']), 'Ungültige Session', $s->lastError);
    }
}

// Load user data
$cu = new CurrentUser($s->user_id);

// Check if it is valid user
if (!$cu->isValid) {
    forward(getLoginUrl(['page'=>'err', 'err'=>'usernotfound']), 'Benutzer nicht mehr vorhanden');
}

//
// Design / layout properties
//

// Design
defineImagePaths();

//
// Page content
//

// Referers prüfen
$referer_allow = false;
if (isset($_SERVER["HTTP_REFERER"])) {
    // Referers
    $referers = explode("\n", $cfg->referers->v);
    foreach ($referers as $k => &$v) {
        $referers[$k] = trim($v);
    }
    unset($v);
    $referers[] = 'http://'.$_SERVER['HTTP_HOST'];
    foreach ($referers as &$rfr) {
        if (substr($_SERVER["HTTP_REFERER"],0, strlen($rfr)) === $rfr) {
            $referer_allow=true;
        }
    }
    unset($rfr);
}

try {
    ob_start();

    // Spiel ist generell gesperrt (ausser fŸr erlaubte IP's)
    $allowed_ips = explode("\n", $cfg->value('offline_ips_allow'));

    if ($cfg->value('offline') == 1 && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips, true)) {
        iBoxStart('Spiel offline',750, 'margin:50px auto;text-align:center');
        echo "<img src=\"images/maintenance.jpg\" alt=\"maintenance\" /><br/><br/>";
        if ($cfg->value('offline_message')!="") {
            echo text2html($cfg->value('offline_ message'))."<br/><br/>";
        } else {
            echo "Das Spiel ist aufgrund von Wartungsarbeiten momentan offline! Schaue sp&auml;ter nochmals vorbei!<br/><br/>";
        }
        echo button("Zur Startseite", getLoginUrl());
        iBoxEnd();
    }

    // Login ist gesperrt
    elseif ($cfg->value('enable_login')==0 && !in_array($_SERVER['REMOTE_ADDR'],$allowed_ips)) {
        iBoxStart("Login geschlossen",750,"margin:50px auto;text-align:center");
        echo "<img src=\"images/keychain.png\" alt=\"maintenance\" /><br/><br/>";
        echo "Der Login momentan geschlossen!<br/><br/>";
        echo button("Zur Startseite", getLoginUrl());
        iBoxEnd();
    }

    // Login ist erlaubt aber noch zeitlich zu frŸh
    elseif ($cfg->value('enable_login')==1 && $cfg->value('enable_login')!="" && $cfg->param1('enable_login') > time() && !in_array($_SERVER['REMOTE_ADDR'],$allowed_ips)) {
        iBoxStart("Login noch geschlossen",750,"margin:50px auto;text-align:center");
        echo "<img src=\"images/keychain.png\" alt=\"maintenance\" /><br/><br/>";
        echo "Das Spiel startet am ".date("d.m.Y",$cfg->param1('enable_login'))." ab ".date("H:i",$cfg->param1('enable_login'))."!<br/><br/>";
        echo button("Zur Startseite", getLoginUrl());
        iBoxEnd();
    }

    // Zugriff von anderen als eigenem Server bzw Login-Server sperren
    elseif (!$referer_allow && isset($_SERVER["HTTP_REFERER"])) {
        echo "<div style=\"text-align:center;\">
        <h1>Falscher Referer</h1>
        Der Zugriff auf das Spiel ist nur anderen internen Seiten aus m&ouml;glich! Ein externes Verlinken direkt in das Game hinein ist nicht gestattet! Dein Referer: ".$_SERVER["HTTP_REFERER"]."<br/><br/>
        <a href=\"".getLoginUrl() ."\">Hauptseite</a></div>";
    }

    // Zugriff erlauben und Inhalt anzeigen
    else {
        if ($s->firstView && $cu->properties->startUpChat == 1) {
            echo "<script type=\"text/javascript\">".CHAT_ONCLICK."</script>";
        }

        if ($cu->isSetup()) {
            //
            // Load current planet
            //
            $res = dbquery("
                SELECT
                    id,
                    planet_user_main
                FROM
                    planets
                WHERE
                    planet_user_id=".$cu->id."
                ORDER BY
                    planet_user_main DESC,
                    planet_name ASC
            ");
            $planets = [];
            $mainplanet = 0;
            if (mysql_num_rows($res)>0) {
                while ($arr=mysql_fetch_row($res)) {
                    $planets[] = $arr[0];
                    if ($arr[1]==1) {
                        $mainplanet = $arr[0];
                    }
                }
            }
            // Todo: check if mainplanet is still 0

            // Wenn eine ID angegeben wurde (Wechsel des Planeten) wird diese ŸberprŸft
            //if (!isset($s->echng_key))
            //	$s->echng_key = mt_rand(100,9999999);

            $eid=0;
            if (isset($_GET['change_entity'])) {
                $eid = intval($_GET['change_entity']);
            }
            if ($eid > 0 && in_array($eid, $planets)) {
                $cpid = $eid;
                $s->cpid = $cpid;
            } elseif (isset($s->cpid) && in_array($s->cpid,$planets)) {
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
    define('NEW_MESSAGES',Message::checkNew($cu->id));

    // Check new reports
    $newReports = Report::countNew($cu->id);

    // Count users
    $ucres = dbquery('SELECT COUNT(user_id) FROM users;');
    $ucarr = mysql_fetch_row($ucres);

    // Count online users
    $gres = dbquery('SELECT COUNT(user_id) FROM user_sessions;');
    $garr=mysql_fetch_row($gres);

    // Count notes
    $np = new Notepad($cu->id);
    $numNotes = $np->numNotes();
    unset($np);

    // Number of player's own fleets
    $fm = new FleetManager($cu->id, $cu->allianceId);
    $fm->loadOwn();
    $ownFleetCount = $fm->count();
    unset($fm);

    if (isset($cp)) {
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
    $tm = new TextManager();

    if (ADD_BANNER=="") {
        $twig->addGlobal('adds', false);
    } elseif ($cu->properties->showAdds==1 || FORCE_ADDS==1) {
        $twig->addGlobal('adds', true);
    } else {
        $twig->addGlobal('adds', false);
    }

    // Include content
    require __DIR__ . '/inc/content.inc.php';

    $infoText = $tm->getText('info');

    echo $twig->render('layout/game.html.twig', array_merge($currentPlanetData, [
        'design' => strtolower(str_replace('designs/official/', '', CSS_STYLE)),
        'addBanner' => ADD_BANNER,
        'gameTitle' => getGameIdentifier(),
        'templateDir' => CSS_STYLE,
        'xajaxJS' => $xajax->getJavascript(XAJAX_DIR),
        'bodyTopStuff' => getInitTT(),
        'ownFleetCount' => $ownFleetCount,
        'messages' => NEW_MESSAGES,
        'newreports' => $newReports,
        'blinkMessages' => $cu->properties->msgBlink,
        'buddys' => check_buddys_online($cu->id),
        'buddyreq' => check_buddy_req($cu->id),
        'fleetAttack' => check_fleet_incomming($cu->id),
        'enableKeybinds' => $cu->properties->enableKeybinds,
        'isAdmin' => $cu->admin,
        'usersOnline' => $garr[0],
        'usersTotal' => $ucarr[0],
        'notes' => $numNotes,
        'userPoints' => nf($cu->points),
        'userNick' => $cu->nick,
        'page' => $page,
        'mode' => $mode,
        'topNav' => $gameMenu->getTopNav(),
        'mainNav' => $gameMenu->getMainNav(),
        'renderTime' => $watch->stop('render')->getDuration() / 1000,
        'content' => ob_get_clean(),
        'infoText' => $infoText->enabled && !empty($infoText->content) ? $infoText->content : null,
        'helpBox' => $cu->properties->helpBox == 1,
        'noteBox' => $cu->properties->noteBox==1,
    ]));
} catch (DBException $e) {
    ob_clean();
    echo $twig->render('layout/empty.html.twig', [
        'content' => $e,
    ]);
} finally {
    $_SESSION['lastpage']=$page;

    dbclose();
}
