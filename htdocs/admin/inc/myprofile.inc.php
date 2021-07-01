<?PHP

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use Twig\Environment;

/** @var AdminUserRepository */
$adminUserRepo = $app[AdminUserRepository::class];

if (isset($_POST['submitPassword'])) {
    submitPassword($cu, $adminUserRepo, $twig);
}
if (isset($_POST['submitProfile'])) {
    submitProfile($cu, $adminUserRepo, $twig);
}
profileIndex($cu, $twig);

function submitPassword(AdminUser $cu, AdminUserRepository $adminUserRepo, Environment $twig)
{
    try {
        if (!$cu->checkEqualPassword($_POST['user_password_old'])) {
            throw new \Exception('Das alte Passwort stimmt nicht mit dem gespeicherten Wert überein!');
        }
        if (!($_POST['user_password'] == $_POST['user_password2'] && $_POST['user_password_old'] != $_POST['user_password'])) {
            throw new \Exception('Die Kennwortwiederholung stimmt nicht oder das alte und das neue Passwort sind gleich!');
        }
        if (strlen($_POST['user_password']) < PASSWORD_MINLENGHT) {
            throw new \Exception('Das Passwort ist zu kurz! Es muss mindestens ' . PASSWORD_MINLENGHT . ' Zeichen lang sein!');
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

function profileIndex(AdminUser $cu, Environment $twig)
{
    echo $twig->render('admin/profile/profile.html.twig', [
        'user' => $cu,
        'users' => Users::getArray(),
    ]);
    exit();
}
