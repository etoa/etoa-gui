<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Cell\CellRepository;
use EtoA\User\UserRepository;

global $app;

$errorMessage = null;
$successMessage = null;

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
$users = $userRepository->getUserNicknames();
if (count($users) === 0) {
    $errorMessage = 'Keine Benutzer vorhanden!';
}

$uid = null;
$user = null;
if (isset($_GET['user_id']) && $_GET['user_id'] > 0) {
    $uid = $_GET['user_id'];

    $user = new User($uid);

    $sx = 1;
    $sy = 1;
    $cx = 1;
    $cy = 1;
    $radius = 1;

    // Discover selected cell
    if (isset($_POST['discover_selected'])) {
        $sx = intval($_POST['sx']);
        $sy = intval($_POST['sy']);
        $cx = intval($_POST['cx']);
        $cy = intval($_POST['cy']);
        $radius = abs(intval($_POST['radius']));

        /** @var CellRepository $cellRepository */
        $cellRepository = $app[CellRepository::class];
        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        $cell = $cellRepository->getCellIdByCoordinates($sx, $sy, $cx, $cy);
        if ($cell !== null) {
            [$absX, $absY] = $cell->getAbsoluteCoordinates($config->param1Int('num_of_cells'), $config->param2Int('num_of_cells'));
            $user->setDiscovered($absX, $absY, $radius);
            $successMessage = 'Koordinaten erkundet!';
        } else {
            $errorMessage = 'Ungültige Koordinate!';
        }
    }

    // Reset discovered coordinates
    else if (isset($_POST['discover_reset'])) {
        $user->setDiscoveredAll(false);
        $successMessage = 'Erkundung zurückgesetzt!';
    }

    // Discover all coordinates
    else if (isset($_POST['discover_all'])) {
        $user->setDiscoveredAll(true);
        $successMessage = 'Alles erkundet!';
    }

    echo $twig->render('admin/galaxy/exploration.html.twig', [
        'successMessage' => $successMessage,
        'errorMessage' => $errorMessage,
        'users' => $users,
        'uid' => $uid,
        'user' => $user,
        'discoveredPercent' => $user->getDiscoveredPercent(),
        'sx' => $sx,
        'sy' => $sy,
        'cx' => $cx,
        'cy' => $cy,
        'radius' => $radius,
    ]);
    exit();
}

echo $twig->render('admin/galaxy/exploration.html.twig', [
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'users' => $users,
    'uid' => $uid,
]);
exit();
