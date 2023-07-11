<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Admin\FirstAdminUserType;
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
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $user = $this->adminUserRepository->findOneByNick($_POST['user_nick']);
            if ($user !== null) {
                // TODO: Use instead https://symfony.com/doc/current/security/reset_password.html

                $pw = generatePasswort();
                $this->adminUserRepository->setPassword($user, $passwordHasher->hashPassword(new CurrentAdmin($user), $pw), true);

                $msg = "Hallo " . $user->nick . ".\n\nDu hast für die Administration der " . $this->config->get('roundname') . " von EtoA ein neues Passwort angefordert.\n\n";
                $msg .= "Das neue Passwort lautet: $pw\n\n";
                $msg .= "Diese Anfrage wurde am " . date("d.m.Y") . " um " . date("H:i") . " Uhr vom Computer " . $this->networkNameService->getHost($request->getClientIp()) . " aus in Auftrag gegeben.\nBitte denke daran, das Passwort nach dem ersten Login zu ändern!";
                $this->mailer->send("Neues Administrationspasswort", $msg, $user->email);

                $msgStyle = 'color_ok';
                $statusMsg = 'Das Passwort wurde geändert und dir per Mail zugestellt!';
                $buttonMsg = 'Zum Login';
                $buttonTarget = '?';

                $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $user->nick . " (ID: " . $user->id . ") fordert per E-Mail (" . $user->email . ") von " . $_SERVER['REMOTE_ADDR'] . " aus ein neues Passwort an.");
            } else {
                $msgStyle = 'color_warn';
                $statusMsg = 'Dieser Benutzer existiert nicht!';
                $buttonMsg = 'Nochmals versuchen';
                $buttonTarget = '?sendpass=1';
            }

            echo $this->render('admin/login/login-status.html.twig', [
                'title' => 'Passwort senden',
                'msgStyle' => $msgStyle,
                'statusMsg' => $statusMsg,
                'buttonMsg' => $buttonMsg,
                'buttonTarget' => $buttonTarget,
            ]);
        }

        return $this->render('admin/login/request-password.html.twig', []);
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
            $newAdmin->nick = $newAdmin->name;
            $newAdmin->roles = ['master'];
            $this->adminUserRepository->save($newAdmin);

            $hashPassword = $passwordHasher->hashPassword(new CurrentAdmin($newAdmin), $newAdmin->passwordString);
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
