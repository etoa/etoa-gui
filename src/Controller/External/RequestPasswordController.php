<?php

namespace EtoA\Controller\External;

use EtoA\Controller\AbstractLegacyShowController;
use EtoA\User\UserService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequestPasswordController extends AbstractLegacyShowController
{
    #[Route('/request-password', name: 'external.request-password')]
    public function index(
        UserService $userService,
    ): Response
    {
        return $this->handle(function () use (
            $userService
        ) {
            $errorMessage = null;
            $successMessage = null;
            try {
                if (isset($_POST['submit_pwforgot']) && checker_verify(0, 1, true)) {
                    if ($_POST['user_nick'] && !stristr($_POST['user_nick'], "'") && $_POST['user_email_fix'] && !stristr($_POST['user_email_fix'], "'")) {
                        try {
                            $userService->resetPassword($_POST['user_nick'], $_POST['user_email_fix']);
                        } catch (Exception $ex) {
                            $errorMessage = $ex->getMessage();
                        }

                        $_SESSION['pwforgot_success_msg'] = 'Deine Passwort-Anfrage war erfolgreich. Du solltest in einigen Minuten eine E-Mail mit dem neuen Passwort erhalten!';
                        // TODO
                        $this->redirectToRoute('external.login');
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

            echo $this->twig->render('external/pwforgot.html.twig', [
                'roundName' => $this->config->get('roundname'),
                'checker' => checker_init(),
                'errorMessage' => $errorMessage,
                'successMessage' => $successMessage,
            ]);
        });
    }
}