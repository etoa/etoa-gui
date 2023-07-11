<?php

namespace EtoA\Controller\External;

use EtoA\Controller\AbstractLegacyShowController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractLegacyShowController
{
    #[Route('/login', name: 'external.login')]
    public function index(): Response
    {
        return $this->handle(function () {
            echo "hi";
        });
    }
}