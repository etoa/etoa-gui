<?PHP

use EtoA\User\UserRepository;

$success = false;
$errorMessage = null;
if (isset($_GET['key'])) {
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $success = $userRepository->markVerifiedByVerificationKey($_GET['key']);
    if (!$success) {
        $errorMessage = 'Der Verifikationscode ist ungültig!';
    }
} else {
    $errorMessage = 'Kein Verifikationscode angegeben!';
}

echo $twig->render('external/verify-email.html.twig', [
    'success' => $success,
    'errorMessage' => $errorMessage,
    'loginUrl' => getLoginUrl(),
]);
