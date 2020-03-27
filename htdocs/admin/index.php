<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

ob_start();

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    require __DIR__ .'/inc/includer.inc.php';
    $app = require __DIR__ . '/../../src/app.php';
} catch (DBException $ex) {
	ob_clean();
    require_once __DIR__ . '/../src/minimalapp.php';
    echo $app['twig']->render('layout/empty.html.twig', [
        'content' => $ex,
    ]);
    exit;
}

$twig->addGlobal('ajaxJs', $xajax->getJavascript(XAJAX_DIR));
$twig->addGlobal('cssTheme', $css_theme);
$twig->addGlobal('pageTitle', getGameIdentifier() . ' Administration');
$twig->addGlobal('bodyTopStuff', getInitTT());

try {
    // Login if requested
    if (isset($_POST['login_submit'])) {
        if (! $s->login($_POST)) {
            include __DIR__ .'/inc/admin_login.inc.php';
            return;
        }

        if (!empty($_SERVER['QUERY_STRING'])) {
            forward("?".$_SERVER['QUERY_STRING']);
        } else {
            forward(".");
        }
    }

    // Perform logout if requested
    if (isset($_GET['logout']) && $_GET['logout']!=null) {
        $s->logout();
        forward('.',"Logout");
    }

    // Validate session
    if (!$s->validate()) {
        include __DIR__ .'/inc/admin_login.inc.php';
    } else {
        // Load admin user data
        $cu = new AdminUser($s->user_id);

        // Zwischenablage
        if (isset($_GET['cbclose'])) {
            $s->clipboard = null;
        }
        $cb = isset ($s->clipboard) && $s->clipboard==1 ? true : false;


        $searchQuery = $_POST['search_query'] ?? '';
        $navmenu = fetchJsonConfig("admin-menu.conf");

        $nres = dbquery("select COUNT(*) from admin_notes where admin_id='".$s->user_id."'");
        $narr = mysql_fetch_row($nres);
        $numTickets = Ticket::countAssigned($s->user_id) + Ticket::countNew();

        $twig->addGlobal('searchQuery', $searchQuery);
        $twig->addGlobal('navMenu', $navmenu);
        $twig->addGlobal('page', $page);
        $twig->addGlobal('sub', $sub);
        $twig->addGlobal('numTickets', $numTickets);
        $twig->addGlobal('numTickets', $numTickets);
        $twig->addGlobal('numNotes', $narr[0]);
        $twig->addGlobal('currentUserNick', $cu->nick);
        $twig->addGlobal('userRoles', $cu->roles);
        $twig->addGlobal('isUnix', UNIX);

        if (UNIX) {
            $eventHandlerPid = EventHandlerManager::checkDaemonRunning(getAbsPath($cfg->daemon_pidfile));
            intval(exec("cat /proc/cpuinfo | grep processor | wc -l", $out));
            $load = sys_getloadavg();
            $systemLoad = round($load[2]/intval($out[0])*100, 2);

            $twig->addGlobal('sysLoad', $systemLoad);
            $twig->addGlobal('eventHandlerPid', $eventHandlerPid);
        }

        $ures=dbquery("SELECT count(*) FROM users;");
        $uarr=mysql_fetch_row($ures);

        $gres=dbquery("SELECT COUNT(*) FROM user_sessions WHERE time_action>".(time() - $cfg->user_timeout->v).";");
        $garr=mysql_fetch_row($gres);

        $a1res=dbquery("SELECT COUNT(*)  FROM admin_user_sessions WHERE time_action>".(time() - $cfg->admin_timeout->v).";");
        $a1arr=mysql_fetch_row($a1res);

        $adminsCount = AdminUser::countAll();
        $dbSize = DBManager::getInstance()->getDbSize();

        $twig->addGlobal('usersOnline', $garr[0]);
        $twig->addGlobal('usersCount', $uarr[0]);
        $twig->addGlobal('usersAllowed', $cfg->enable_register->p2);
        $twig->addGlobal('adminsOnline', $a1arr[0]);
        $twig->addGlobal('adminsCount', $adminsCount);
        $twig->addGlobal('dbSize', $dbSize);

        // Inhalt einbinden
        if (isset($_GET['adminlist'])) {
            require __DIR__ . '/inc/adminlist.inc.php';
        } elseif (isset($_GET['myprofile'])) {
            require __DIR__ . '/inc/myprofile.inc.php';
        } elseif (isset($_GET['tfa'])) {
            require __DIR__ . '/inc/tfa.inc.php';
        } else {
            // Check permissions
            $allow_inc=false;
            $found = false;
            $rm = new AdminRoleManager();

            foreach ($navmenu as $cat=> $item) {
                if ($item['page']==$page && $sub=="") {
                    $found = true;
                    if ($rm->checkAllowed($item['roles'], $cu->roles)) {
                        $allow_inc = true;
                        break;
                    }
                } else if (isset($item['children'])) {
                    foreach ($item['children'] as $title=> $data) {
                        if ($item['page']==$page && $data['sub']==$sub) {
                            $found = true;
                            if ($rm->checkAllowed($data['roles'], $cu->roles)) {
                                $allow_inc = true;
                                break;
                            }
                        }
                    }
                }
            }

            if ($allow_inc || !$found)	{
                if (preg_match('^[a-z\_]+$^',$page)  && strlen($page)<=50) {
                    $contentFile = "content/".$page.".php";
                    if (is_file($contentFile)) {
                        include($contentFile);
                        logAccess($page,"admin",$sub);
                    } else {
                        echo "<h1>Fehler</h1> Die Seite $page wurde nicht gefunden!";
                    }
                } else {
                    echo "<h1>Fehler</h1>Der Seitenname enth&auml;lt unerlaubte Zeichen!<br><br><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
                }
            } else {
                echo "<h1>Kein Zugriff</h1> Du hast keinen Zugriff auf diese Seite!";
            }
        }

        // Write all changes of $s to the session variable
        $_SESSION[SESSION_NAME]=$s;
        dbclose();

        echo $twig->render('admin/default.html.twig', [
            'content' => ob_get_clean(),
        ]);
    }
} catch (DBException $ex) {
    ob_clean();
    require_once __DIR__ . '/../src/minimalapp.php';
    echo $app['twig']->render('layout/empty.html.twig', [
        'content' => $ex,
    ]);
    exit;
}
