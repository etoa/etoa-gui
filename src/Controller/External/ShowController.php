<?php

namespace EtoA\Controller\External;

use EtoA\Controller\AbstractLegacyShowController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractLegacyShowController
{
    #[Route('/show', name: 'legacy.show')]
    public function index(Request $request): Response
    {
        $index = $request->query->get('index');
        if ($index == 'login') {
            return $this->redirectToRoute('external.login');
        }
        if ($index == 'register') {
            return $this->redirectToRoute('external.register');
        }
        if ($index == 'pwforgot') {
            return $this->redirectToRoute('external.request-password');
        }
        if ($index == 'contact') {
            return $this->redirectToRoute('external.contact');
        }
        return $this->handle(function () use ($index) {
            return $this->render('external/404.html.twig', [
                'page' => $index,
            ]);
        });
    }
}