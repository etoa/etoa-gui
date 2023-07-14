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
use Symfony\Component\HttpFoundation\Request;
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
        Request           $request,
    ): Response
    {
        return $this->handle(function () use (
            $userRepository,
            $userService,
            $mailSenderService,
            $logRepository,
            $request,
        ) {
            //
            // Handle registration submit
            //
            if ($request->request->has('register_submit') && $this->config->getBoolean('enable_register')) {
                $_SESSION['REGISTER'] = $_POST;

                try {
                    $newUser = $userService->register(
                        name: $request->request->get('register_user_name'),
                        email: $request->request->get('register_user_email'),
                        nick: $request->request->get('register_user_nick'),
                        password: $request->request->get('register_user_password')
                    );
                    $logRepository->add(
                        LogFacility::USER,
                        LogSeverity::INFO,
                        "Der Benutzer " . $newUser->nick . " (" . $newUser->name . ", " . $newUser->email . ") hat sich registriert!"
                    );

                    $verificationRequired = filled($newUser->verificationKey);
                    $verificationUrl = null;
                    if ($verificationRequired) {
                        $verificationUrl = $this->config->get('roundurl') . '/show/?index=verifymail&key=' . $newUser->verificationKey;
                    }

                    $emailText = $this->twig->render('email/register.txt.twig', [
                        'newUser' => $newUser,
                        'roundName' => $this->config->get('roundname'),
                        'verificationUrl' => $verificationUrl,
                        'rulesUrl' => ExternalUrl::RULES,
                    ]);

                    $mailSenderService->send("Account-Registrierung", $emailText, $newUser->email);

                    $_SESSION['REGISTER'] = Null;

                    return $this->render('external/register-success.html.twig', [
                        'registerEmail' => $_POST['register_user_email'],
                        'verificationRequired' => $verificationRequired,
                    ]);
                } catch (Exception $e) {
                    $this->addFlash('error', 'Die Registration hat leider nicht geklappt: ' . $e->getMessage());
                }
            }

            return $this->render('external/register.html.twig',
                $this->getRegisterParams($this->config, $userRepository)
            );
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