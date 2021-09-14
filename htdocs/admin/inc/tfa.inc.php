<?php

use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

$tfa = new RobThree\Auth\TwoFactorAuth(AppName::NAME);
$errorMessage = null;
if (isset($_POST['tfa_activate'])) {
    if ($_POST['tfa_challenge'] && $tfa->verifyCode($_SESSION['tfa_activate_secret'], $_POST['tfa_challenge'])) {
        $cu->tfaSecret = $_SESSION['tfa_activate_secret'];
        $cu->save();
        unset($_SESSION['tfa_activate_secret']);
        $logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $cu->nick . ' aktiviert Zwei-Faktor-Authentifizierung');
        forward('?myprofile');
    }

    $secret = $_SESSION['tfa_activate_secret'];
    $errorMessage = 'Der eigegebene Code ist ungütig! Bitte wiederhole den Vorgang!';
}

if (isset($_POST['tfa_disable'])) {
    if ($_POST['tfa_challenge'] && $tfa->verifyCode($cu->tfaSecret, $_POST['tfa_challenge'])) {
        $cu->tfaSecret = '';
        $cu->save();
        unset($_SESSION['tfa_activate_secret']);
        $logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $cu->nick . ' deaktiviert Zwei-Faktor-Authentifizierung');
        forward('?myprofile');
    }

    $errorMessage = 'Der eigegebene Code ist ungütig! Bitte wiederhole den Vorgang!';
}

if ($cu->tfaSecret) {
    echo $twig->render('admin/profile/tfa-disable.html.twig', [
        'errMsg' => $errorMessage,
    ]);
    exit();
}

if (!isset($secret)) {
    $secret = $tfa->createSecret();
    $_SESSION['tfa_activate_secret'] = $secret;
}
$label = $config->get('roundname') . ' : ' . $cu->name;
echo $twig->render('admin/profile/tfa-activate.html.twig', [
    'tfaQrCode' => $tfa->getQRCodeImageAsDataUri($label, $secret),
    'errMsg' => $errorMessage,
]);
exit();
