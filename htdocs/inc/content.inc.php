<?PHP

use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Quest\QuestResponseListener;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Text\TextRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\Tip\TipRepository;
use EtoA\Tutorial\TutorialManager;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserSurveillanceRepository;

/** @var RuntimeDataStore $runtimeDataStore */
$runtimeDataStore = $app[RuntimeDataStore::class];

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var MailSenderService $mailSenderService */
$mailSenderService = $app[MailSenderService::class];

/** @var TutorialManager $tutorialManager */
$tutorialManager = $app[TutorialManager::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

$time = time();

// Get tutorial
if (!$tutorialManager->hasReadTutorial($cu->id, 1)) {
    $twig->addGlobal('tutorial_id', 1);
} else if ($cu->isSetup() && !$tutorialManager->hasReadTutorial($cu->id, 2)) {
    $twig->addGlobal('tutorial_id', 2);
} elseif ($cu->isSetup() && $tutorialManager->hasReadTutorial($cu->id, 2) && $app['etoa.quests.enabled']) {
    $app['cubicle.quests.initializer']->initialize($cu->id);
}

// Go to user setup page if user wasn't set up correctly
if (!$cu->isSetup() && $page != "help" && $page != "contact") {
    require("inc/usersetup.inc.php");
} else {
    // Show tipps
    if (ENABLE_TIPS == 1 && $s->firstView) {
        /** @var TipRepository $tipRepository */
        $tipRepository = $app[TipRepository::class];
        $tipText = $tipRepository->getRandomTipText();

        if ($tipText !== null) {
            echo "<br/>";
            iBoxStart("<span style=\"color:#0f0;\">TIPP</span>");
            echo text2html($tipText);
            iBoxEnd();
        }
    }

    /** @var TextRepository $textRepo */
    $textRepo = $app[TextRepository::class];

    // SYSTEMNACHRICHT //
    $systemMessage = $textRepo->find('system_message');
    if ($systemMessage->isEnabled()) {
        echo "<br />";
        iBoxStart("<span style=\"color:red;\">WICHTIGE SYSTEMNACHRICHT</span>");
        echo text2html($systemMessage->content);
        iBoxEnd();
    }

    //Eventhandler //
    $backendStatus = $runtimeDataStore->get('backend_status');
    if ($backendStatus != null && $backendStatus == 0) {
        $infoText = $textRepo->find('backend_offline_message');
        if ($infoText->isEnabled()) {
            echo "<br />";
            iBoxStart("<span style=\"color:red;\">UPDATEDIENST</span>");
            echo text2html($infoText->content);
            iBoxEnd();
        }
    }

    // E-Mail verification
    if (!$cu->isVerified) {
        if (isset($_GET['resendverificationmail'])) {
            $verificationUrl = $config->get('roundurl') . '/show.php?index=verifymail&key=' . $cu->verificationKey;
            $email_text = "Hallo " . $cu->nick . "\n\n";
            $email_text .= "Damit du alle Funktionen von Escape to Andromeda benutzen kannst muss deine E-Mail Adresse verifiziert werden. Bitte klicke auf den folgenden Link, um die Verifikation für die " . $config->get('roundname') . " abzuschliessen:\n\n";
            $email_text .= $verificationUrl . "\n\n";
            $email_text .= "Viel Spass beim Spielen!\nDas EtoA-Team";
            $mailSenderService->send("Account-Bestätigung", $email_text, $cu->email);
            success_msg("Bestätigungsmail wurde gesendet!");
        } else {
            iBoxStart("Verifikation erforderlich");
            echo "Deine E-Mailadresse <b>" . $cu->email . "</b> muss bestätigt werden, damit du alle Funktionen benutzen kannst!<br/><br/>";
            echo '<a href="?page=' . $page . '&resendverificationmail">Bestätigungsmail nochmals versenden</a>';
            iBoxEnd();
        }
    }

    // Auf Löschung prüfen
    if (
        $cu->deleted > 0 &&
        $page != 'contact' &&
        $page != 'userconfig'
    ) {
        echo '<h1>Dein Account ist zut Löschung vorgeschlagen!</h1>';
        echo 'Die Löschung erfolgt frühestens um <b>' . StringUtils::formatDate($cu->deleted) . '</b>!<br/><br/>
        <input type="button" onclick="document.location=\'?page=userconfig&mode=misc\'" value="Löschung aufheben" />
        <input type="button" onclick="document.location=\'?page=contact\'" value="Admin kontaktieren" /> ';
    }

    // Auf Sperrung prüfen
    elseif (
        $cu->blocked_from > 0 &&
        $cu->blocked_from < $time &&
        $cu->blocked_to > $time &&
        $page != 'contact' &&
        $page != 'help'
    ) {
        echo '<h1>Dein Account ist gesperrt!</h1>
        <b>Grund:</b> ' . $cu->ban_reason . '.<br/>
        <b>Zeitraum:</b> <span style="color:#f90">' . date("d.m.Y, H:i", $cu->blocked_from) . '</span>
        bis <span style="color:#0f0">' . date("d.m.Y, H:i", $cu->blocked_to) . '</span><br/>
        <b>Gesamtdauer der Sperre:</b> ' . StringUtils::formatTimespan($cu->blocked_to - $cu->blocked_from) . '<br/>
        <b>Dauer:</b> ' . StringUtils::formatTimespan($cu->blocked_to - max(time(), $cu->blocked_from)) . '<br/>';

        /** @var AdminUserRepository $adminUserRepository */
        $adminUserRepository = $app[AdminUserRepository::class];
        $adminUser = $adminUserRepository->find((int) $cu->ban_admin_id);
        if ($adminUser !== null) {
            echo '<b>Gesperrt von:</b> ' . $adminUser->nick . ', <a href="mailto:' . $adminUser->email . '">' . $adminUser->email . '</a><br/>';
        }
        echo '<br/>Solltest du Fragen zu dieser Sperrung haben oder dich ungerecht behandelt fühlen,<br/>
        dann <a href="?page=contact">melde</a> dich bei einem Game-Administrator.';
    }

    // Aus Urlaub prüfen
    elseif (
        $cu->hmode_from > 0 &&
        $page != 'userconfig' &&
        $page != 'messages' &&
        $page != 'stats' &&
        $page != 'townhall' &&
        $page != 'buddylist' &&
        $page != 'userinfo' &&
        $page != 'contact' &&
        $page != 'help'
    ) {
        echo '<h1>Du befindest dich im Urlaubsmodus</h1>
        Der Urlaubsmodus dauert bis mindestens: <b>' . date("d.m.Y, H:i", $cu->hmode_to) . '</b><br/>';
        if ($cu->hmode_to < time()) {
            echo '<br/><span style="color:#0f0">Die Minimaldauer ist abgelaufen!</span><br/><br/>
            <input type="button" onclick="document.location=\'?page=userconfig&mode=misc\'" value="Einstellungen" /><br/>';
        } else {
            echo 'Zeit bis Deaktivierung möglich ist: <b>' . StringUtils::formatTimespan($cu->hmode_to - max(time(), $cu->hmode_from)) . '</b><br/>';
        }
        echo '<br/>Solltest du Fragen oder Probleme mit dem Urlaubsmodus haben,<br/>
        dann <a href="?page=contact">melde</a> dich bei einem Game-Administrator.';
    } elseif ($s->sittingActive && $s->falseSitter && $page != "userconfig") {
        echo '<h1>Sitting ist aktiv</h1>
        Dein Account wird gesitted bis <b>' . StringUtils::formatDate($s->sittingUntil) . '</b><br/><br/>';
        echo button("Einstellungen", "?page=userconfig&mode=sitting");
    }

    // Seite anzeigen
    else {
        // 1984
        if ($cu->monitored) {
            $req = "";
            foreach ($_GET as $k => $v) {
                if ($k != "page") {
                    $req .= "[b]" . $k . ":[/b] " . $v . "\n";
                }
            }
            $post = "";
            foreach ($_POST as $k => $v) {
                if (is_array($v)) {
                    $post .= "[b]" . $k . ":[/b] " . etoa_dump($v, 1);
                } else {
                    if ($k == $s->passwordField)
                        $post .= "[b]" . $k . ":[/b] *******\n";
                    else
                        $post .= "[b]" . $k . ":[/b] " . $v . "\n";
                }
            }

            /** @var UserSurveillanceRepository $userSurveillanceRepository */
            $userSurveillanceRepository = $app[UserSurveillanceRepository::class];
            $userSurveillanceRepository->addEntry($cu->getId(), $page, $req, $_SERVER['QUERY_STRING'], $post, $s->id);
        }

        // Change display mode (full/small) if requested
        if (isset($_GET['change_display_mode'])) {
            if ($_GET['change_display_mode'] == 'small') {
                $properties->itemShow = 'small';
                $userPropertiesRepository->storeProperties($cu->id, $properties);
            } elseif ($_GET['change_display_mode'] == 'full') {
                $properties->itemShow = 'full';
                $userPropertiesRepository->storeProperties($cu->id, $properties);
            }
            forward("?page=$page");
        }

        if (preg_match('/^[a-z\_]+$/', $page)  && strlen($page) <= 50) {
            // DEBUG
            $query_counter = 0;
            $queries = array();

            // Content includen
            $contentFile = "content/" . $page . ".php";
            if (!file_exists($contentFile) || !include($contentFile)) {
                echo '<h1>Fehler</h1>
                Die Seite <b>' . $page . '</b> existiert nicht!<br/><br/>
                <input type="button" onclick="history.back();" value="Zurück" />';
            }

            if (isset($_GET['sub']))
                $lasub = $_GET['sub'];
            elseif (isset($_GET['action']))
                $lasub = $_GET['action'];
            elseif (isset($_GET['site']))
                $lasub = $_GET['site'];
            else
                $lasub = "";

            logAccess($page, "ingame", $lasub);
        } else {
            echo '<h1>Fehler</h1>
            Der Seitenname enth&auml;lt unerlaubte Zeichen!<br/><br/>
            <input type="button" onclick="history.back();" value="Zurück" />';
        }
    }
}

if ($app['etoa.quests.enabled']) {
    $twig->addGlobal('quests', array_values($app[QuestResponseListener::class]->getQuests()));
}
