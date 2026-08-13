<?php

namespace EtoA\Controller\External;

use EtoA\Controller\AbstractLegacyShowController;
use EtoA\Support\Checker;
use EtoA\User\UserService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EtoA\Support\ValidationUtils;

class RequestPasswordController extends AbstractLegacyShowController
{
    protected ?string $pageTitle = 'Neues Passwort';

    #[Route('/request-password', name: 'external.request-password')]
    public function index(
        UserService $userService,
        Request     $request,
        Checker     $checker,
    ): Response
    {
        return $this->handle(function () use (
            $userService,
            $request,
            $checker,
        ) {
            if ($request->request->has('submit_pwforgot') && $checker->checker_verify()) {
                if (ValidationUtils::filled($request->request->get('user_nick')) && ValidationUtils::filled($request->request->get('user_email_fix'))) {
                    try {
                        $userService->resetPassword($request->request->get('user_nick'), $request->request->get('user_email_fix'));
                        $this->addFlash('success', 'Deine Passwort-Anfrage war erfolgreich. Du solltest in einigen Minuten eine E-Mail mit dem neuen Passwort erhalten!');
                        return $this->redirectToRoute('external.login');
                    } catch (Exception $ex) {
                        $this->addFlash('error', $ex->getMessage());
                    }
                } else {
                    $this->addFlash('error', 'Du hast keinen Benutzernamen oder keine E-Mail-Adresse eingegeben oder ein unerlaubtes Zeichen verwendet!');
                }
            }

            return $this->render('external/pwforgot.html.twig', [
                'roundName' => $this->config->get('roundname'),
                'checker' => $checker->checker_init(),
            ]);
        });
    }
}