<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\AdminUser;
use EtoA\Form\Type\Admin\FirstAdminUserType;
use EtoA\Form\Type\Admin\ResetPasswordType;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Security\Admin\CurrentAdmin;
use EtoA\Support\Mail\MailSenderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly AdminUserRepository  $adminUserRepository,
        private readonly ConfigurationService $config,
        private readonly MailSenderService    $mailer,
        private readonly LogRepository        $logRepository,
        private readonly NetworkNameService   $networkNameService
    )
    {
    }

    #[Route("/admin/login", name: "admin.login", methods: ['GET'])]
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

    #[Route("/admin/login/reset", name: "admin.login.reset", methods: ['GET', 'POST'])]
    public function resetPassword(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        Environment                 $twig,
    ): Response
    {
        // TODO: Use instead https://symfony.com/doc/current/security/reset_password.html

        $admin = new AdminUser();
        $form = $this->createForm(ResetPasswordType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->adminUserRepository->findOneByNickAndEmail($admin->getNick(), $admin->getEmail());
            if ($user === null) {
                $this->addFlash('error', 'Dieser Benutzer existiert nicht!');
                return $this->redirectToRoute('admin.login.reset');
            }

            $newPassword = generatePasswort();
            $this->adminUserRepository->setPassword($user, $passwordHasher->hashPassword(new CurrentAdmin($user), $newPassword), true);

            $emailText = $twig->render('email/admin/new-password.txt.twig', [
                'user' => $user,
                'roundName' => $this->config->get('roundname'),
                'newPassword' => $newPassword,
                'hostname' => $this->networkNameService->getHost($request->getClientIp()),
            ]);
            $this->mailer->send("Neues Administrationspasswort", $emailText, $user->getEmail());

            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $user->getNick() . " (ID: " . $user->getId() . ") fordert per E-Mail (" . $user->getEmail() . ") von " . $_SERVER['REMOTE_ADDR'] . " aus ein neues Passwort an.");

            $this->addFlash('success', 'Das Passwort wurde geändert und dir per Mail zugestellt!');
            return $this->redirectToRoute('admin.login');
        }

        return $this->render('admin/login/request-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/login/setup", name: "admin.login.setup", methods: ['GET', 'POST'])]
    public function setupFirstUser(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->adminUserRepository->count() !== 0) {
            return $this->redirectToRoute('admin.login');
        }

        $newAdmin = new AdminUser();
        $form = $this->createForm(FirstAdminUserType::class, $newAdmin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newAdmin->setNick($newAdmin->getName());
            $newAdmin->setRoles(['master']);
            $this->adminUserRepository->save($newAdmin);

            $hashPassword = $passwordHasher->hashPassword(new CurrentAdmin($newAdmin), $newAdmin->getPasswordString());
            $this->adminUserRepository->setPassword($newAdmin, $hashPassword);

            $this->addFlash('success', 'User erstellt');
            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/login/login-newuser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/login/check", name: "admin.login.check", methods: ['POST'])]
    public function loginCheck(): void
    {
        // Dummy method. Request handled by symfony security
    }

    #[Route("/admin/logout", name: "admin.logout")]
    public function logout(): void
    {
        // Dummy method. Request handled by symfony security
    }
}
