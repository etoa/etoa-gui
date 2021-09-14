<?PHP

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Text\TextRepository;

$baseUrl = $index != "" ? "?index=" . $index : "?page=" . $page;

/** @var TextRepository $textRepo */
$textRepo = $app[TextRepository::class];

$contactText = $textRepo->find('contact_message');
if ($contactText->isEnabled()) {
    iBoxStart();
    echo BBCodeUtils::toHTML($contactText->content);
    iBoxEnd();
}

/** @var AdminUserRepository $adminUserRepo */
$adminUserRepo = $app[AdminUserRepository::class];

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var MailSenderService $mailSenderService */
$mailSenderService = $app[MailSenderService::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

// List of admins
$admins = array_filter($adminUserRepo->findAll(), fn (AdminUser $admin) => $admin->isContact);
if (count($admins) > 0) {
    if (isset($_GET['rcpt']) && intval($_GET['rcpt']) > 0) {
        $rcpt = intval($_GET['rcpt']);
        $admins = array_filter($admins, fn (AdminUser $admin) => $admin->id == $rcpt);
        echo '<form action="' . $baseUrl . '&amp;rcpt=' . $rcpt . '" method="post"><div>';
        if (count($admins) > 0) {
            $admin = array_values($admins)[0];

            $showForm = true;
            $mail_subject = '';
            $mail_text = '';
            if (isset($_POST['submit'])) {
                $mail_subject = $_POST['mail_subject'];
                $mail_text = $_POST['mail_text'];

                if ($mail_subject && $mail_text) {
                    // Subject
                    $subject = "Kontakt-Anfrage: " . $mail_subject;

                    // Sender, receiver
                    $recipient = $admin->nick . '<' . $admin->email . '>';
                    if (isset($cu)) {
                        $sender = $cu->nick . "<" . $cu->email . ">";
                    } else {
                        $sender = $_POST['mail_sender'];
                    }

                    // Text
                    $text = "Kontakt-Anfrage " . AppName::NAME . " " . $config->get('roundname') . "\n----------------------\n\n";
                    if (isset($cu)) {
                        $text .= "Nick: " . $cu->nick . "\n";
                        $text .= "ID: " . $cu->id . "\n";
                    } else {
                        $text .= "E-Mail: " . $_POST['mail_sender'] . "\n";
                    }
                    $text .= "IP/Host: " . $_SERVER['REMOTE_ADDR'] . " (" . $networkNameService->getHost($_SERVER['REMOTE_ADDR']) . ")\n\n";
                    $text .= $mail_text;

                    // Send mail
                    $mailSenderService->send($subject, $text, $recipient, $sender);
                    success_msg('Vielen Dank! Deine Nachricht wurde gesendet!');
                    $showForm = false;
                } else {
                    error_msg("Titel oder Text fehlt!");
                }
            }

            if ($showForm) {
                tableStart('Nachricht an ' . $admin->nick . ' senden');
                if (isset($cu)) {
                    $sender = $cu->nick . '&lt;' . $cu->email . '&gt;';
                } else {
                    $sender = '';
                }
                echo '<tr><th>Absender E-Mail:</th><td><input type="text" name="mail_sender" value="' . $sender . '" size="50" />';
                echo '</td></tr>';
                echo '<tr><th>Titel:</th><td><input type="text" name="mail_subject" value="' . $mail_subject . '" size="50" /></td></tr>';
                echo '<tr><th>Text:</th><td><textarea name="mail_text" rows="6" cols="80">' . $mail_text . '</textarea></td></tr>';
                tableEnd();
                echo '<input type="submit" name="submit" value="Senden" /> &nbsp;';
            }
        } else {
            error_msg("Kontakt nicht vorhanden!");
        }
        echo '<input type="button" onclick="document.location=\'' . $baseUrl . '\'" value="Zurück" /></div></form>';
    } else {
        tableStart('Kontaktpersonen für die ' . $config->get('roundname'));
        echo '<tr>
                <th>Name</th>
                <th>Mail</th>
                <th>Kontaktformular</th>
                <th>Foren-Profil</th>
            </tr>';
        foreach ($admins as $admin) {
            $showMailAddress = preg_match('/' . AdminUser::CONTACT_REQUIRED_EMAIL_SUFFIX . '/i', $admin->email);

            echo '<tr><td>' . $admin->nick . '</td>';
            if ($showMailAddress) {
                echo '<td><a href="mailto:' . $admin->email . '">' . $admin->email . '</a></td>';
            } else {
                echo '<td>(nicht öffentlich)</td>';
            }
            echo '<td><a href="' . $baseUrl . '&amp;rcpt=' . $admin->id . '">Mail senden</a></td>';
            if ($admin->boardUrl != '') {
                echo '<td><a href="' . $admin->boardUrl . '" onclick="window.open(\'' . $admin->boardUrl . '\');return false;">Profil</a></td>';
            } else {
                echo '<td>-</td>';
            }
            echo '</tr>';
        }
        tableEnd();
    }
} else {
    echo "<i>Keine Kontaktpersonen vorhanden!</i>";
}
