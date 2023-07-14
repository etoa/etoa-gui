<?php

namespace EtoA\Controller\External;

use EtoA\Controller\AbstractLegacyShowController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractLegacyShowController
{
    protected ?string $pageTitle = 'Einloggen';

    #[Route('/login', name: 'external.login')]
    public function index(Request $request): Response
    {
        $loginUrl = $this->config->get('loginurl');
        if ($loginUrl) {
            return $this->redirect($loginUrl);
        }

        return $this->handle(function () use ($loginUrl, $request) {

            // Login if requested
            if ($request->request->has('login')) {
                $loginNick = trim($request->request->get('nickname', ''));
                $loginPassword = trim($request->request->get('password', ''));
                if (!$this->userSession->login($loginNick, $loginPassword)) {
                    $this->addFlash('error', $this->userSession->getLastError());
                    return $this->redirectToRoute('external.login');
                }
                return $this->redirect('/'); // TODO
            }

            // TODO CSRF token

            return $this->render('external/login.html.twig', [
                'loginUrl' => $loginUrl,
                'roundName' => $this->config->get('roundname'),
            ]);
        });
    }
}