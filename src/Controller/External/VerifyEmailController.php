<?php

namespace EtoA\Controller\External;

use EtoA\Controller\AbstractLegacyShowController;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerifyEmailController extends AbstractLegacyShowController
{
    protected ?string $pageTitle = 'Bestätigung der E-Mail Adresse';

    #[Route('/verify-email/{key}', name: 'external.verify-email')]
    public function index(
        UserRepository $userRepository,
        string         $key,
    ): Response
    {
        return $this->handle(function () use (
            $userRepository,
            $key,
        ) {
            $success = $userRepository->markVerifiedByVerificationKey($key);
            if ($success) {
                $this->addFlash('success', 'Deine E-Mailadresse wurde erfolgreich bestätigt!');
            } else {
                $this->addFlash('error', 'Der Verifikationscode ist ungültig!');
            }

            return $this->render('external/verify-email.html.twig', [
                'success' => $success,
            ]);
        });
    }
}