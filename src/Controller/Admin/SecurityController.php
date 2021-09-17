<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin/login", methods={"GET"}, name="admin.login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('admin/login/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'lastUsername' => $authenticationUtils->getLastUsername(),
            'ajaxJs' => null,
            'bodyTopStuff' => null,
        ]);
    }

    /**
     * @Route("/admin/login/check", methods={"POST"}, name="admin.login.check")
     */
    public function loginCheck(): void
    {
        //
    }

    /**
     * @Route("/admin/logout", name="admin.logout")
     */
    public function logout(): void
    {
        //
    }
}
