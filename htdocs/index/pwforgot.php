<?PHP

//
// 	Topic: Passwort-Erneuerung
//

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

$errorMessage = null;
$successMessage = null;
try {
    if (isset($_POST['submit_pwforgot']) && checker_verify(0, 1, true)) {
        if ($_POST['user_nick'] && !stristr($_POST['user_nick'],"'") && $_POST['user_email_fix'] && !stristr($_POST['user_email_fix'],"'")) {
            $res = dbquery("
        SELECT
            user_id,
            user_registered
        FROM
            users
        WHERE
            LCASE(user_nick)='".strtolower($_POST['user_nick'])."'
            AND user_email_fix='".$_POST['user_email_fix']."'
        ;");
            if (mysql_num_rows($res)>0) {
                $arr = mysql_fetch_array($res);

                // Passwort generieren
                $pw = generatePasswort();

                // Email schreiben
                $email_text = 'Hallo ' . $_POST['user_nick'] . "\n\nDu hast ein neues Passwort angefordert.\nHier sind die neuen Daten:\n\nUniversum: " . $config->get('roundname') . "\n\nNick: " . $_POST['user_nick'] . "\nPasswort: " . $pw . "\n\nWeiterhin viel Spass...\nDas EtoA-Team";
                $mail = new Mail("Passwort-Anforderung",$email_text);
                $mail->send($_POST['user_email_fix']);

                // Passwort updaten
                dbquery("UPDATE
                users
            SET
                user_password='".saltPasswort($pw)."'
            WHERE
                user_nick='".$_POST['user_nick']."'
                AND user_email_fix='".$_POST['user_email_fix']."'
            ;");

                // Log hinzufügen
                Log::add(Log::F_USER,Log::INFO,'Der Benutzer ' . $_POST['user_nick'] . ' hat ein neues Passwort per E-Mail angefordert!');

                $_SESSION['pwforgot_success_msg'] = 'Deine Passwort-Anfrage war erfolgreich. Du solltest in einigen Minuten eine E-Mail mit dem neuen Passwort erhalten!';
                forward('?index='.$index);
                return;
            }

            $errorMessage = 'Es wurde kein entsprechender Datensatz gefunden!';
        } else {
            $errorMessage = 'Du hast keinen Benutzernamen oder keine E-Mail-Adresse eingegeben oder ein unerlaubtes Zeichen verwendet!';
        }
    }
} catch (\RuntimeException $e) {
    $errorMessage = $e->getMessage();
}

if (isset($_SESSION['pwforgot_success_msg'])) {
    $msg = $_SESSION['pwforgot_success_msg'];
    unset($_SESSION['pwforgot_success_msg']);
    $successMessage = $msg;
}

echo $twig->render('external/pwforgot.html.twig', [
    'roundName' => $config->get('roundname'),
    'checker' => checker_init(),
    'errorMessage' => $errorMessage,
    'successMessage' => $successMessage,
]);
