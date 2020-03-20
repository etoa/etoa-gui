<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

if (isset($_GET['sendpass'])) {
	if (isset($_POST['sendpass_submit'])) {
        $user = AdminUser::findByNick($_POST['user_nick']);
        if ($user) {
            // TODO: Do not generate password immediately, but send confirmation token

            $pw = generatePasswort();
            $user->setPassword($pw, true);

            $msg = "Hallo " . $user->nick . ".\n\nDu hast für die Administration der " . Config::getInstance()->roundname->v . " von EtoA ein neues Passwort angefordert.\n\n";
            $msg .= "Das neue Passwort lautet: $pw\n\n";
            $msg .= "Diese Anfrage wurde am " . date("d.m.Y") . " um " . date("H:i") . " Uhr vom Computer " . Net::getHost($_SERVER['REMOTE_ADDR']) . " aus in Auftrag gegeben.\nBitte denke daran, das Passwort nach dem ersten Login zu ändern!";
            $mail = new Mail("Neues Administrationspasswort", $msg);
            $mail->send($user->email);

            $msgStyle = 'color_ok';
            $statusMsg = 'Das Passwort wurde geändert und dir per Mail zugestellt!';
            $buttonMsg = 'Zum Login';
            $buttonTarget = '?';

            add_log(8, "Der Administrator " . $user->nick . " (ID: " . $user->id . ") fordert per E-Mail (" . $user->email . ") von " . $_SERVER['REMOTE_ADDR'] . " aus ein neues Passwort an.", time());
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
        return;
    }

    echo $twig->render('admin/login/request-password.html.twig', []);
    return;
}

if (AdminUser::countAll() === 0) {
    if (isset($_POST['newuser_submit']) && $_POST['user_email']!="" && $_POST['user_nick']!="" && $_POST['user_password']!='') {
        $nu = new AdminUser();
        $nu->email = $_POST['user_email'];
        $nu->nick = $_POST['user_nick'];
        $nu->name = $_POST['user_nick'];
        $nu->roles = array('master');
        $nu->save();
        $nu->setPassword($_POST['user_password']);

        echo $twig->render('admin/login/login-status.html.twig', [
            'title' => 'Admin-User erstellen',
            'msgStyle' => 'color_ok',
            'statusMsg' => 'Benutzer wurde erstellt!',
            'buttonMsg' => 'Weiterfahren',
            'buttonTarget' => '?',
        ]);
        return;
    }

    echo $twig->render('admin/login/login-newuser.html.twig', []);
    return;
}

$msg = null;
$msgStyle = null;
if ($s->lastError && $s->lastErrorCode !== 'nologin') {
    $msg = $s->lastError;
    $msgStyle = 'color_warn';
}

$loginTarget = '?' . $_SERVER['QUERY_STRING'];
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
return;


