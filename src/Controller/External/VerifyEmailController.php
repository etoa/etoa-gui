<?php

namespace EtoA\Controller\External;

use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerifyEmailController extends AbstractController
{
    protected ?string $pageTitle = 'Bestätigung der E-Mail Adresse';

    #[Route('/verify-email/{key}', name: 'external.verify-email')]
    public function index(
        UserRepository $userRepository,
        string         $key,
    ): Response
    {
        $success = $userRepository->markVerifiedByVerificationKey($key);
        if ($success) {
            $this->addFlash('success', 'Deine E-Mailadresse wurde erfolgreich bestätigt!');
        } else {
            $this->addFlash('error', 'Der Verifikationscode ist ungültig!');
        }

        return $this->render('external/verify-email.html.twig', [
            'success' => $success,
        ]);
    }
}