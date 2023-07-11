<?php

namespace EtoA\Controller\External;

use DateTime;
use EtoA\Controller\AbstractLegacyShowController;
use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\ExternalUrl;
use EtoA\Support\Mail\MailSenderService;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractLegacyShowController
{
    protected ?string $pageTitle = 'Registrieren';

    #[Route('/register', name: 'external.register')]
    public function index(
        UserRepository    $userRepository,
        UserService       $userService,
        MailSenderService $mailSenderService,
        LogRepository     $logRepository,
    ): Response
    {
        return $this->handle(function () use (
            $userRepository,
            $userService,
            $mailSenderService,
            $logRepository
        ) {
            //
            // Handle registration submit
            //
            if (($_POST['register_submit'] ?? false) && $this->config->getBoolean('enable_register')) {
                $_SESSION['REGISTER'] = $_POST;

                try {
                    $newUser = $userService->register(
                        $_POST['register_user_name'],
                        $_POST['register_user_email'],
                        $_POST['register_user_nick'],
                        $_POST['register_user_password']
                    );
                    $logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $newUser->nick . " (" . $newUser->name . ", " . $newUser->email . ") hat sich registriert!");

                    $verificationRequired = filled($newUser->verificationKey);
                    $verificationUrl = null;
                    if ($verificationRequired) {
                        $verificationUrl = $this->config->get('roundurl') . '/show/?index=verifymail&key=' . $newUser->verificationKey;
                    }

                    $email_text = "Hallo " . $newUser->nick . "\n\nDu hast dich erfolgreich beim Sci-Fi Browsergame Escape to Andromeda für die " . $this->config->get('roundname') . " registriert.\nHier nochmals deine Daten:\n\n";
                    $email_text .= "Name: " . $newUser->name . "\n";
                    $email_text .= "E-Mail: " . $newUser->email . "\n";
                    $email_text .= "Nick: " . $newUser->nick . "\n\n";
                    if ($verificationRequired) {
                        $email_text .= "Klicke auf den folgenden Link um deine E-Mail Adresse zu bestätigen\n\n";
                        $email_text .= $verificationUrl . "\n\n";
                    }
                    $email_text .= "WICHTIG: Gib dein Passwort an niemanden weiter. Gib dein Passwort auch auf keiner Seite ausser unserer Loginseite ein. Ein Game-Admin oder Entwickler wird dich auch nie nach dem Passwort fragen!\n";
                    $email_text .= "Desweiteren solltest du dich mit den Regeln (" . ExternalUrl::RULES . ") bekannt machen, da ein Regelverstoss eine (zeitweilige) Sperrung deines Accounts zur Folge haben kann!\n\n";
                    $email_text .= "Viel Spass beim Spielen!\nDas EtoA-Team";

                    $mailSenderService->send("Account-Registrierung", $email_text, $newUser->email);

                    $successMessage = 'Es wurde eine Bestätigungsnachricht an <b>' . $_POST['register_user_email'] . '</b> verschickt.';
                    if ($verificationRequired) {
                        $successMessage .= ' Klicke auf den Link in der Nachricht um deinen Account zu bestätigen!';
                    }
                    $successMessage .= '<br/><br/>Solltest du innerhalb der n&auml;chsten 5 Minuten keine E-Mail erhalten, pr&uuml;fe zun&auml;chst dein Spam-Verzeichnis.<br/><br/>Melde dich bei einem <a href="?index=contact">Admin</a>, falls du keine E-Mail erh&auml;ltst oder andere Anmeldeprobleme auftreten.';

                    $_SESSION['REGISTER'] = Null;

                    echo $this->twig->render('external/register-success.html.twig', [
                        'successMessage' => $successMessage,
                    ]);
                    return;
                } catch (Exception $e) {
                    echo $this->twig->render('external/register.html.twig', array_merge($this->getRegisterParams($this->config, $userRepository), [
                        'errorMessage' => 'Die Registration hat leider nicht geklappt: ' . $e->getMessage(),
                    ]));
                    return;
                }
            }

            echo $this->twig->render('external/register.html.twig', $this->getRegisterParams($this->config, $userRepository));
        });
    }

    private function getRegisterParams(ConfigurationService $config, UserRepository $userRepository): array
    {
        // Load user count
        $userCount = $userRepository->count();

        return [
            'maxPlayerCount' => $userCount,
            'registrationNotEnabled' => !$config->getBoolean('enable_register'),
            'registrationLater' => ($config->getBoolean('enable_register') && $config->param1Int('enable_register') > time())
                ? new DateTime('@' . $config->param1Int('enable_register'))
                : null,
            'registrationFull' => $config->param2Int('enable_register') <= $userCount,
            'userName' => $_SESSION['REGISTER']['register_user_name'] ?? '',
            'userNick' => $_SESSION['REGISTER']['register_user_nick'] ?? '',
            'userEmail' => $_SESSION['REGISTER']['register_user_email'] ?? '',
            'userPassword' => $_SESSION['REGISTER']['register_user_password'] ?? '',
            'roundName' => $config->get('roundname'),
            'appName' => AppName::NAME,
            'nameMaxLength' => $config->getInt('name_length'),
            'nickMaxLength' => $config->param2Int('nick_length'),
            'rulesUrl' => ExternalUrl::RULES,
            'privacyUrl' => ExternalUrl::PRIVACY,
        ];
    }
}