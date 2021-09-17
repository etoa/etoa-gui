<?PHP

use EtoA\Admin\AdminNotesRepository;
use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUserRepository;
use EtoA\Backend\EventHandlerManager;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;
use Twig\Environment;

ob_start();

require_once __DIR__ . '/../../vendor/autoload.php';

require __DIR__ . '/inc/includer.inc.php';
$app = require __DIR__ . '/../../src/app.php';

$twig->addGlobal('ajaxJs', $xajax->getJavascript());
$twig->addGlobal('pageTitle', getGameIdentifier() . ' Administration');
$twig->addGlobal('bodyTopStuff', getInitTT());


// Login if requested
if (isset($_POST['login_submit'])) {
    if (!$s->login($_POST)) {
        include __DIR__ . '/inc/admin_login.inc.php';
        return;
    }

    if ($_SERVER['QUERY_STRING']) {
        forward("?" . $_SERVER['QUERY_STRING']);
    } else {
        forward(".");
    }
}

// Perform logout if requested
if (isset($_GET['logout']) && $_GET['logout'] != null) {
    $s->logout();
    forward('.', "Logout");
}

// Validate session
if (!$s->validate()) {
    include __DIR__ . '/inc/admin_login.inc.php';
} else {
    /** @var AdminUserRepository $adminUserRepo */
    $adminUserRepo = $app[AdminUserRepository::class];

    /** @var UserRepository $userRepo */
    $userRepo = $app[UserRepository::class];

    /** @var UserSessionRepository $userSessionRepo */
    $userSessionRepo = $app[UserSessionRepository::class];

    /** @var AdminNotesRepository $notesRepo */
    $notesRepo = $app[AdminNotesRepository::class];

    /** @var AdminRoleManager $roleManager */
    $roleManager = $app[AdminRoleManager::class];

    /** @var AdminSessionRepository $sessionRepository */
    $sessionRepository = $app[AdminSessionRepository::class];

    /** @var DatabaseManagerRepository $databaseManager */
    $databaseManager = $app[DatabaseManagerRepository::class];

    /** @var TicketRepository $ticketRepo */
    $ticketRepo = $app[TicketRepository::class];

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];

    /** @var EventHandlerManager $eventHandlerManager */
    $eventHandlerManager = $app[EventHandlerManager::class];

    adminView(
        $s,
        $adminUserRepo,
        $userRepo,
        $userSessionRepo,
        $notesRepo,
        $roleManager,
        $sessionRepository,
        $databaseManager,
        $ticketRepo,
        $config,
        $eventHandlerManager,
        $twig
    );
}


function adminView(
    AdminSession $s,
    AdminUserRepository $adminUserRepo,
    UserRepository $userRepo,
    UserSessionRepository $userSessionRepo,
    AdminNotesRepository $notesRepo,
    AdminRoleManager $roleManager,
    AdminSessionRepository $sessionRepository,
    DatabaseManagerRepository $databaseManager,
    TicketRepository $ticketRepo,
    ConfigurationService $config,
    EventHandlerManager $eventHandlerManager,
    Environment $twig
) {
    global $page;
    global $sub;
    global $app;
    global $cu;

    // Load admin user data
    $cu = $adminUserRepo->find($s->user_id);

    $searchQuery = $_POST['search_query'] ?? '';
    $navMenu = fetchJsonConfig("admin-menu.conf");

    $numNotes = $notesRepo->countForAdmin($s->user_id);

    $numTickets = $ticketRepo->countAssigned($s->user_id) + $ticketRepo->countNew();

    $twig->addGlobal('searchQuery', $searchQuery);
    $twig->addGlobal('navMenu', $navMenu);
    $twig->addGlobal('page', $page);
    $twig->addGlobal('sub', $sub);
    $twig->addGlobal('numTickets', $numTickets);
    $twig->addGlobal('numTickets', $numTickets);
    $twig->addGlobal('numNotes', $numNotes);
    $twig->addGlobal('currentUserNick', $cu->nick);
    $twig->addGlobal('userRoles', $cu->roles);
    $twig->addGlobal('isUnix', isUnixOS());

    if (isUnixOS()) {
        $eventHandlerPid = $eventHandlerManager->checkDaemonRunning();
        exec("cat /proc/cpuinfo | grep processor | wc -l", $out);
        $load = sys_getloadavg();
        $systemLoad = round($load[2] / intval($out[0]) * 100, 2);

        $twig->addGlobal('sysLoad', $systemLoad);
        $twig->addGlobal('eventHandlerPid', $eventHandlerPid);
    }

    $twig->addGlobal('usersOnline', $userSessionRepo->countActiveSessions($config->getInt('user_timeout')));
    $twig->addGlobal('usersCount', $userRepo->count());
    $twig->addGlobal('usersAllowed', $config->getInt('enable_register'));
    $twig->addGlobal('adminsOnline', $sessionRepository->countActiveSessions($config->getInt('admin_timeout')));
    $twig->addGlobal('adminsCount', $adminUserRepo->count());
    $twig->addGlobal('dbSizeInMB', $databaseManager->getDatabaseSize());

    // Inhalt einbinden
    if (isset($_GET['adminlist'])) {
        require __DIR__ . '/inc/adminlist.inc.php';
    } elseif (isset($_GET['myprofile'])) {
        require __DIR__ . '/inc/myprofile.inc.php';
    } elseif (isset($_GET['tfa'])) {
        require __DIR__ . '/inc/tfa.inc.php';
    } else {
        // Check permissions
        $allow_inc = false;
        $found = false;

        foreach ($navMenu as $item) {
            if ($item['page'] == $page && $sub == "") {
                $found = true;
                if ($roleManager->checkAllowed($cu, $item['roles'])) {
                    $allow_inc = true;
                    break;
                }
            } else if (isset($item['children'])) {
                foreach ($item['children'] as $data) {
                    if ($item['page'] == $page && $data['sub'] == $sub) {
                        $found = true;
                        if ($roleManager->checkAllowed($cu, $data['roles'])) {
                            $allow_inc = true;
                            break;
                        }
                    }
                }
            }
        }

        if ($allow_inc || !$found) {
            if (preg_match('^[a-z\_]+$^', $page)  && strlen($page) <= 50) {
                $contentFile = __DIR__ . "/content/" . $page . ".php";
                if (is_file($contentFile)) {
                    include $contentFile;
                    logAccess($page, "admin", $sub);
                } else {
                    echo "<h1>Fehler</h1> Die Seite $page wurde nicht gefunden!";
                }
            } else {
                echo "<h1>Fehler</h1>Der Seitenname enthält unerlaubte Zeichen!<br><br><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
            }
        } else {
            echo "<h1>Kein Zugriff</h1> Du hast keinen Zugriff auf diese Seite!";
        }
    }

    // Write all changes of $s to the session variable
    $_SESSION[SESSION_NAME] = $s;

    echo $twig->render('admin/default.html.twig', [
        'content' => ob_get_clean(),
    ]);
}
