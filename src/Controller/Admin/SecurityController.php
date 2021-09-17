<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private AdminUserRepository $adminUserRepository;

    public function __construct(AdminUserRepository $adminUserRepository)
    {
        $this->adminUserRepository = $adminUserRepository;
    }

    /**
     * @Route("/admin/login", methods={"GET"}, name="admin.login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->adminUserRepository->count() === 0) {
            return $this->redirectToRoute('admin.login.setup');
        }

        return $this->render('admin/login/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'lastUsername' => $authenticationUtils->getLastUsername(),
        ]);
    }

    /**
     * @Route("/admin/login/setup", methods={"GET", "POST"}, name="admin.login.setup")
     */
    public function setupFirstUser(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->adminUserRepository->count() !== 0) {
            return $this->redirectToRoute('admin.login');
        }

        if ($request->isMethod('POST')) {
            $newAdmin = new AdminUser();
            $newAdmin->email = $request->request->get('user_email');
            $newAdmin->name = $newAdmin->nick = $request->request->get('user_nick');
            $newAdmin->roles = ['master'];
            $this->adminUserRepository->save($newAdmin);
            $this->adminUserRepository->setPassword($newAdmin, $passwordHasher->hashPassword(new CurrentAdmin($newAdmin), $request->request->get('user_password')));

            return $this->render('admin/login/login-status.html.twig', [
                'title' => 'Admin-User erstellen',
                'msgStyle' => 'color_ok',
                'statusMsg' => 'Benutzer wurde erstellt!',
                'buttonMsg' => 'Weiterfahren',
                'buttonTarget' => '/admin/',
            ]);
        }

        return $this->render('admin/login/login-newuser.html.twig', []);
    }

    /**
     * @Route("/admin/login/check", methods={"POST"}, name="admin.login.check")
     */
    public function loginCheck(): void
    {
        // Dummy method. Request handled by symfony security
    }

    /**
     * @Route("/admin/logout", name="admin.logout")
     */
    public function logout(): void
    {
        // Dummy method. Request handled by symfony security
    }
}
