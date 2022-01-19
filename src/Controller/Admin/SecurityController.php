<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
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
    private AdminUserRepository $adminUserRepository;
    private ConfigurationService $config;
    private MailSenderService $mailer;
    private LogRepository $logRepository;
    private NetworkNameService $networkNameService;

    public function __construct(AdminUserRepository $adminUserRepository, ConfigurationService $config, MailSenderService $mailer, LogRepository $logRepository, NetworkNameService $networkNameService)
    {
        $this->adminUserRepository = $adminUserRepository;
        $this->config = $config;
        $this->mailer = $mailer;
        $this->logRepository = $logRepository;
        $this->networkNameService = $networkNameService;
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
     * @Route("/admin/login/reset", methods={"GET", "POST"}, name="admin.login.reset")
     */
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
            $newAdmin->email = (string) $request->request->get('user_email');
            $newAdmin->name = $newAdmin->nick = (string) $request->request->get('user_nick');
            $newAdmin->roles = ['master'];
            $this->adminUserRepository->save($newAdmin);
            $this->adminUserRepository->setPassword($newAdmin, $passwordHasher->hashPassword(new CurrentAdmin($newAdmin), (string) $request->request->get('user_password')));

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
