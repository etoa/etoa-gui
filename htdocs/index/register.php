<?PHP

//////////////////////////////////////////////////
//		 	 ____    __           ______       			//
//			/\  _`\ /\ \__       /\  _  \      			//
//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
//																					 		//
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame				 		//
// Ein Massive-Multiplayer-Online-Spiel			 		//
// Programmiert von Nicolas Perrenoud				 		//
// www.nicu.ch | mail@nicu.ch								 		//
// als Maturaarbeit '04 am Gymnasium Oberaargau	//
//////////////////////////////////////////////////
//
// 	Dateiname: register.php
// 	Topic: Anmeldung
// 	Autor: Nicolas Perrenoud alias MrCage
// 	Erstellt: 01.12.2004
// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
// 	Bearbeitet am: 07.03.2006
// 	Kommentar:
//

function getRegisterParams(): array{
    $cfg = Config::getInstance();

    // Load user count
    $ucnt=mysql_fetch_row(dbquery("SELECT COUNT(user_id) FROM users;"));

    return [
        'maxPlayerCount' => $ucnt[0],
        'registrationNotEnabled' => $cfg->get('enable_register') == 0,
        'registrationLater' => ($cfg->get('enable_register') == 1 && $cfg->p1('enable_register') && $cfg->p1('enable_register') > time()) ? new \DateTime('@' . $cfg->p1('enable_register')) : null,
        'registrationFull' => $cfg->p2('enable_register') <= $ucnt[0],
        'userName' => $_SESSION['REGISTER']['register_user_name'] ?? '',
        'userNick' => $_SESSION['REGISTER']['register_user_nick'] ??'',
        'userEmail' => $_SESSION['REGISTER']['register_user_email'] ?? '',
        'userPassword' => $_SESSION['REGISTER']['register_user_password'] ?? '',
        'roundName' => Config::getInstance()->roundname->v,
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
if (($_POST['register_submit'] ?? false) && $cfg->get('enable_register') != 0) {
    $_SESSION['REGISTER']=$_POST;

    try {
        $newUser = User::register(
            $_POST['register_user_name'],
            $_POST['register_user_email'],
            $_POST['register_user_nick'],
            $_POST['register_user_password']
        );
        add_log(3,"Der Benutzer ".$newUser->nick." (".$newUser->realName.", ".$newUser->email.") hat sich registriert!");

        $verificationRequired = Config::getInstance()->email_verification_required->v;
        if ($verificationRequired) {
            $newUser->setVerified(false);
            $verificationUrl = Config::getInstance()->roundurl.'/show.php?index=verifymail&key='.$newUser->verificationKey;
        } else {
            $newUser->setVerified(true);
        }

        $email_text = "Hallo ".$newUser->nick."\n\nDu hast dich erfolgreich beim Sci-Fi Browsergame Escape to Andromeda für die ".Config::getInstance()->roundname->v." registriert.\nHier nochmals deine Daten:\n\n";
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
        echo $twig->render('external/register.html.twig', array_merge(getRegisterParams(), [
            'errorMessage' => 'Die Registration hat leider nicht geklappt: ' . $e->getMessage(),
        ]));
        return;
    }
}

echo $twig->render('external/register.html.twig', getRegisterParams());
