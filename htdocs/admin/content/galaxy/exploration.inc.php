<?PHP

use EtoA\Universe\Cell\CellRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\Request;

global $app;

$errorMessage = null;
$successMessage = null;

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var UserUniverseDiscoveryService */
$userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];

/** @var CellRepository $cellRepository */
$cellRepository = $app[CellRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

$users = $userRepository->getUserNicknames();
if (count($users) === 0) {
    $errorMessage = 'Keine Benutzer vorhanden!';
}

if ($request->query->has('user_id') && $request->query->getInt('user_id') > 0) {
    $user = $userRepository->getUser($request->query->getInt('user_id'));

    $sx = 1;
    $sy = 1;
    $cx = 1;
    $cy = 1;
    $radius = 1;

    // Discover selected cell
    if ($request->request->has('discover_selected')) {
        $sx = $request->request->getInt('sx');
        $sy = $request->request->getInt('sy');
        $cx = $request->request->getInt('cx');
        $cy = $request->request->getInt('cy');
        $radius = abs($request->request->getInt('radius'));

        $cell = $cellRepository->getCellIdByCoordinates($sx, $sy, $cx, $cy);
        if ($cell !== null) {
            $userUniverseDiscoveryService->setDiscovered($user, $cell, $radius);
            $successMessage = 'Koordinaten erkundet!';
        } else {
            $errorMessage = 'Ungültige Koordinate!';
        }
    }

    // Reset discovered coordinates
    else if ($request->request->has('discover_reset')) {
        $userUniverseDiscoveryService->setDiscoveredAll($user, false);
        $successMessage = 'Erkundung zurückgesetzt!';
    }

    // Discover all coordinates
    else if ($request->request->has('discover_all')) {
        $userUniverseDiscoveryService->setDiscoveredAll($user, true);
        $successMessage = 'Alles erkundet!';
    }

    echo $twig->render('admin/galaxy/exploration.html.twig', [
        'successMessage' => $successMessage,
        'errorMessage' => $errorMessage,
        'users' => $users,
        'user' => $user,
        'discoveredPercent' => $userUniverseDiscoveryService->getDiscoveredPercent($user),
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
    'user' => null,
]);
exit();
