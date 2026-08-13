<?php

namespace EtoA\Controller\External;

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EtoA\Support\ValidationUtils;

class LoginController extends AbstractController
{
    protected ?string $pageTitle = 'Einloggen';

    public function __construct(ConfigurationService $config)
    {
    }

    #[Route('/login', name: 'external.login')]
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('game.login');
        $loginUrl = $this->config->get('loginurl');
        if (ValidationUtils::filled($loginUrl)) {
            return $this->redirect($loginUrl);
        }

        return $this->handle(function () use ($request) {

            // Login if requested
            if ($request->request->has('login')) {
                $loginNick = trim($request->request->get('nickname', ''));
                $loginPassword = trim($request->request->get('password', ''));
                if (!$this->userSession->login($loginNick, $loginPassword)) {
                    $this->addFlash('error', $this->userSession->getLastError());
                    return $this->redirectToRoute('external.login');
                }
                return $this->redirectToRoute('legacy.index');
            }

            // TODO CSRF token

            return $this->render('external/login.html.twig', [
                'roundName' => $this->config->get('roundname'),
            ]);
        });
    }
}