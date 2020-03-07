<?PHP

$success = false;
$errorMessage = null;
if (!empty($_GET['key'])) {
    $user = User::findFirstByVerificationKey($_GET['key']);
    if ($user) {
        $user->setVerified(true);
        $success = true;
    } else {
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
