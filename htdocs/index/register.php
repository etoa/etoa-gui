<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];
/** @var \EtoA\User\UserRepository $userRepository */
$userRepository = $app[\EtoA\User\UserRepository::class];

function getRegisterParams(ConfigurationService $config, \EtoA\User\UserRepository $userRepository): array
{
    // Load user count
    $userCount = $userRepository->count();

    return [
        'maxPlayerCount' => $userCount,
        'registrationNotEnabled' => !$config->getBoolean('enable_register'),
        'registrationLater' => ($config->getBoolean('enable_register') && $config->param1Int('enable_register') > time()) ? new \DateTime('@' . $config->param1Int('enable_register')) : null,
        'registrationFull' => $config->param2Int('enable_register') <= $userCount,
        'userName' => $_SESSION['REGISTER']['register_user_name'] ?? '',
        'userNick' => $_SESSION['REGISTER']['register_user_nick'] ??'',
        'userEmail' => $_SESSION['REGISTER']['register_user_email'] ?? '',
        'userPassword' => $_SESSION['REGISTER']['register_user_password'] ?? '',
        'roundName' => $config->get('roundname'),
        'appName' => APP_NAME,
        'nameMaxLength' => NAME_MAXLENGTH,
        'nickMaxLength' => NICK_MAXLENGHT,
        'rulesUrl' => RULES_URL,
        'privacyUrl' => PRIVACY_URL,
    ];
}

//
// Handle registration submit
//
if (($_POST['register_submit'] ?? false) && $config->getBoolean('enable_register')) {
    $_SESSION['REGISTER']=$_POST;

    try {
        $newUser = User::register(
            $_POST['register_user_name'],
            $_POST['register_user_email'],
            $_POST['register_user_nick'],
            $_POST['register_user_password']
        );
        Log::add(3,Log::INFO,"Der Benutzer ".$newUser->nick." (".$newUser->realName.", ".$newUser->email.") hat sich registriert!");

        $verificationRequired = $config->getBoolean('email_verification_required');
        $verificationUrl = null;
        if ($verificationRequired) {
            $newUser->setVerified(false);
            $verificationUrl = $config->get('roundurl').'/show.php?index=verifymail&key='.$newUser->verificationKey;
        } else {
            $newUser->setVerified(true);
        }

        $email_text = "Hallo ".$newUser->nick."\n\nDu hast dich erfolgreich beim Sci-Fi Browsergame Escape to Andromeda für die ".$config->get('roundname')." registriert.\nHier nochmals deine Daten:\n\n";
        $email_text.= "Name: ".$newUser->realName."\n";
        $email_text.= "E-Mail: ".$newUser->email."\n";
        $email_text.= "Nick: ".$newUser->nick."\n\n";
        if ($verificationRequired) {
            $email_text.= "Klicke auf den folgenden Link um deine E-Mail Adresse zu bestätigen\n\n";
            $email_text.= $verificationUrl."\n\n";
        }
        $email_text.= "WICHTIG: Gib dein Passwort an niemanden weiter. Gib dein Passwort auch auf keiner Seite ausser unserer Loginseite ein. Ein Game-Admin oder Entwickler wird dich auch nie nach dem Passwort fragen!\n";
        $email_text.= "Desweiteren solltest du dich mit den Regeln (".RULES_URL.") bekannt machen, da ein Regelverstoss eine (zeitweilige) Sperrung deines Accounts zur Folge haben kann!\n\n";
        $email_text.= "Viel Spass beim Spielen!\nDas EtoA-Team";

        $mail = new Mail("Account-Registrierung", $email_text);
        $mail->send($newUser->email);

        $successMessage = 'Es wurde eine Bestätigungsnachricht an <b>' .$_POST['register_user_email']. '</b> verschickt.';
        if ($verificationRequired) {
            $successMessage .= ' Klicke auf den Link in der Nachricht um deinen Account zu bestätigen!';
        }
        $successMessage .= '<br/><br/>Solltest du innerhalb der n&auml;chsten 5 Minuten keine E-Mail erhalten, pr&uuml;fe zun&auml;chst dein Spam-Verzeichnis.<br/><br/>Melde dich bei einem <a href="?index=contact">Admin</a>, falls du keine E-Mail erh&auml;ltst oder andere Anmeldeprobleme auftreten.';

        $_SESSION['REGISTER']=Null;

        echo $twig->render('external/register-success.html.twig', [
            'successMessage' => $successMessage,
        ]);
        return;
    } catch (Exception $e) {
        echo $twig->render('external/register.html.twig', array_merge(getRegisterParams($config, $userRepository), [
            'errorMessage' => 'Die Registration hat leider nicht geklappt: ' . $e->getMessage(),
        ]));
        return;
    }
}

echo $twig->render('external/register.html.twig', getRegisterParams($config, $userRepository));
