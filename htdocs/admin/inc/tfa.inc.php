<?php

$tfa = new RobThree\Auth\TwoFactorAuth(APP_NAME);
$errorMessage = null;
if (isset($_POST['tfa_activate'])) {
    if (!empty($_POST['tfa_challenge']) && $tfa->verifyCode($_SESSION['tfa_activate_secret'], $_POST['tfa_challenge'])) {
        $cu->tfaSecret = $_SESSION['tfa_activate_secret'];
        $cu->save();
        unset($_SESSION['tfa_activate_secret']);
        add_log(8,$cu->nick . ' aktiviert Zwei-Faktor-Authentifizierung');
        forward('?myprofile');
    }

    $secret = $_SESSION['tfa_activate_secret'];
    $errorMessage = 'Der eigegebene Code ist ungütig! Bitte wiederhole den Vorgang!';
}

if (isset($_POST['tfa_disable'])) {
    if (!empty($_POST['tfa_challenge']) && $tfa->verifyCode($cu->tfaSecret, $_POST['tfa_challenge'])) {
        $cu->tfaSecret = '';
        $cu->save();
        unset($_SESSION['tfa_activate_secret']);
        add_log(8,$cu->nick . ' deaktiviert Zwei-Faktor-Authentifizierung');
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
$label = Config::getInstance()->roundname->v . ' : ' . $cu->name;
echo $twig->render('admin/profile/tfa-activate.html.twig', [
    'tfaQrCode' => $tfa->getQRCodeImageAsDataUri($label, $secret),
    'errMsg' => $errorMessage,
]);
exit();
