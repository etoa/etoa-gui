<?PHP

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;
use Twig\Environment;

/** @var AdminUserRepository */
$adminUserRepo = $app[AdminUserRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

if (isset($_POST['submitPassword'])) {
    submitPassword($cu, $adminUserRepo, $config, $twig);
}
if (isset($_POST['submitProfile'])) {
    submitProfile($cu, $adminUserRepo, $twig);
}
profileIndex($cu, $twig, $userRepository);

function submitPassword(
    AdminUser $cu,
    AdminUserRepository $adminUserRepo,
    ConfigurationService $config,
    Environment $twig
): void {
    try {
        if (!$cu->checkEqualPassword($_POST['user_password_old'])) {
            throw new \Exception('Das alte Passwort stimmt nicht mit dem gespeicherten Wert überein!');
        }
        if (!($_POST['user_password'] == $_POST['user_password2'] && $_POST['user_password_old'] != $_POST['user_password'])) {
            throw new \Exception('Die Kennwortwiederholung stimmt nicht oder das alte und das neue Passwort sind gleich!');
        }
        if (strlen($_POST['user_password']) < $config->getInt('password_minlength')) {
            throw new \Exception('Das Passwort ist zu kurz! Es muss mindestens ' . $config->getInt('password_minlength') . ' Zeichen lang sein!');
        }

        $adminUserRepo->setPassword($cu, $_POST['user_password']);

        $twig->addGlobal('successMessage', 'Das Passwort wurde geändert!');

        Log::add(8, Log::INFO,  $cu->id . " ändert sein Passwort");
    } catch (\Exception $ex) {
        $twig->addGlobal('errorMessage', $ex->getMessage());
    }
}

function submitProfile(AdminUser $cu, AdminUserRepository $adminUserRepo, Environment $twig)
{
    $cu->name = $_POST['user_name'];
    $cu->email = $_POST['user_email'];
    $cu->boardUrl = $_POST['user_board_url'];
    $cu->userTheme = $_POST['user_theme'] ?? '';
    $cu->ticketEmail = $_POST['ticketmail'];
    $cu->playerId = $_POST['player_id'];

    $adminUserRepo->save($cu);

    $twig->addGlobal('successMessage', 'Die Daten wurden geändert!');

    Log::add(8, Log::INFO, $cu->nick . " ändert seine Daten");
}

function profileIndex(AdminUser $cu, Environment $twig, UserRepository $userRepository)
{
    echo $twig->render('admin/profile/profile.html.twig', [
        'user' => $cu,
        'users' => $userRepository->getUserNicknames(),
    ]);
    exit();
}
