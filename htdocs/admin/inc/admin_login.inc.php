<?PHP

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\LogFacility;
use EtoA\Support\Mail\MailSenderService;
use Twig\Environment;

/** @var AdminUserRepository */
$adminUserRepo = $app[AdminUserRepository::class];

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var MailSenderService $mailSenderService */
$mailSenderService = $app[MailSenderService::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

if (isset($_GET['sendpass'])) {
    if (isset($_POST['sendpass_submit'])) {
        sendPassword($config, $adminUserRepo, $mailSenderService, $networkNameService, $twig);
    } else {
        sendPasswordForm($twig);
    }
} else if ($adminUserRepo->count() === 0) {
    if (isset($_POST['newuser_submit']) && $_POST['user_email'] != "" && $_POST['user_nick'] != "" && $_POST['user_password'] != '') {
        registerFirstUser($adminUserRepo, $twig);
    } else {
        registerFirstUserForm($twig);
    }
} else {
    loginForm($s, $twig);
}

function sendPassword(
    ConfigurationService $config,
    AdminUserRepository $adminUserRepo,
    MailSenderService $mailSenderService,
    NetworkNameService $networkNameService,
    Environment $twig
): void {
    $user = $adminUserRepo->findOneByNick($_POST['user_nick']);
    if ($user !== null) {
        // TODO: Do not generate password immediately, but send confirmation token

        $pw = generatePasswort();
        $adminUserRepo->setPassword($user, $pw, true);

        $msg = "Hallo " . $user->nick . ".\n\nDu hast für die Administration der " . $config->get('roundname') . " von EtoA ein neues Passwort angefordert.\n\n";
        $msg .= "Das neue Passwort lautet: $pw\n\n";
        $msg .= "Diese Anfrage wurde am " . date("d.m.Y") . " um " . date("H:i") . " Uhr vom Computer " . $networkNameService->getHost($_SERVER['REMOTE_ADDR']) . " aus in Auftrag gegeben.\nBitte denke daran, das Passwort nach dem ersten Login zu ändern!";
        $mailSenderService->send("Neues Administrationspasswort", $msg, $user->email);

        $msgStyle = 'color_ok';
        $statusMsg = 'Das Passwort wurde geändert und dir per Mail zugestellt!';
        $buttonMsg = 'Zum Login';
        $buttonTarget = '?';

        Log::add(LogFacility::ADMIN, Log::INFO,  "Der Administrator " . $user->nick . " (ID: " . $user->id . ") fordert per E-Mail (" . $user->email . ") von " . $_SERVER['REMOTE_ADDR'] . " aus ein neues Passwort an.");
    } else {
        $msgStyle = 'color_warn';
        $statusMsg = 'Dieser Benutzer existiert nicht!';
        $buttonMsg = 'Nochmals versuchen';
        $buttonTarget = '?sendpass=1';
    }

    echo $twig->render('admin/login/login-status.html.twig', [
        'title' => 'Passwort senden',
        'msgStyle' => $msgStyle,
        'statusMsg' => $statusMsg,
        'buttonMsg' => $buttonMsg,
        'buttonTarget' => $buttonTarget,
    ]);
}

function sendPasswordForm(Environment $twig): void
{
    echo $twig->render('admin/login/request-password.html.twig', []);
}

function registerFirstUser(AdminUserRepository $adminUserRepo, Environment $twig): void
{
    $newAdmin = new AdminUser();
    $newAdmin->email = $_POST['user_email'];
    $newAdmin->nick = $_POST['user_nick'];
    $newAdmin->name = $_POST['user_nick'];
    $newAdmin->roles = ['master'];
    $adminUserRepo->save($newAdmin);
    $adminUserRepo->setPassword($newAdmin, $_POST['user_password']);

    echo $twig->render('admin/login/login-status.html.twig', [
        'title' => 'Admin-User erstellen',
        'msgStyle' => 'color_ok',
        'statusMsg' => 'Benutzer wurde erstellt!',
        'buttonMsg' => 'Weiterfahren',
        'buttonTarget' => '?',
    ]);
}

function registerFirstUserForm(Environment $twig): void
{
    echo $twig->render('admin/login/login-newuser.html.twig', []);
}

function loginForm(AdminSession $s, Environment $twig): void
{
    $msg = null;
    $msgStyle = null;
    if ($s->lastError !== '' && $s->lastErrorCode !== 'nologin') {
        $msg = $s->lastError;
        $msgStyle = 'color_warn';
    }

    if ($s->lastErrorCode === 'tfa_challenge') {
        echo $twig->render('admin/login/tfa-challenge.html.twig', [
            'msg' => $msg,
            'msgStyle' => $msgStyle,
            'loginTarget' => '?' . $_SERVER['QUERY_STRING'],
        ]);
        return;
    }

    echo $twig->render('admin/login/login.html.twig', [
        'msg' => $msg,
        'msgStyle' => $msgStyle,
    ]);
}
