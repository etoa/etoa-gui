<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\QrCode\QrCodeGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TfaSecurityController extends AbstractAdminController
{
    private AdminUserRepository $adminUserRepository;
    private LogRepository $logRepository;

    public function __construct(AdminUserRepository $adminUserRepository, LogRepository $logRepository)
    {
        $this->adminUserRepository = $adminUserRepository;
        $this->logRepository = $logRepository;
    }

    #[Route("/admin/tfa/enable", name: "admin.tfa.enable")]
    public function enableTwoFactorAuthAction(Request $req, TotpAuthenticatorInterface $authenticator, QrCodeGenerator $qrCodeGenerator): Response
    {
        $secret = $req->getSession()->get('tfa-secret', $authenticator->generateSecret());

        $user = $this->getUser();
        $user->getData()->tfaSecret = $secret;
        $req->getSession()->set('tfa-secret', $secret);

        if ($req->isMethod('POST')) {
            if ($authenticator->checkCode($user, $req->request->get('tfa_challenge'))) {
                $this->adminUserRepository->setTfaSecret($user->getData(), $secret);
                $req->getSession()->remove('tfa-secret');

                $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $user->getUsername() . ' aktiviert Zwei-Faktor-Authentifizierung');
                $this->addFlash('success', 'Two-factor authentication has been enabled.');

                return $this->redirect('/admin/?myprofile');
            }
        }

        $qrCode = $qrCodeGenerator->getTotpQrCode($user);

        return $this->render('admin/profile/tfa-activate.html.twig', [
            'secret' => $secret,
            'tfaQrCode' => $qrCode->writeDataUri(),
        ]);
    }

    #[Route("/admin/tfa/disable", name: "admin.tfa.disable")]
    public function disableTwoFactorAuthAction(Request $req, TotpAuthenticatorInterface $authenticator): Response
    {
        $user = $this->getUser();

        if ($req->isMethod('POST')) {
            if ($authenticator->checkCode($user, $req->request->get('code'))) {
                $this->adminUserRepository->setTfaSecret($user->getData(), '');

                $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $user->getUsername() . ' deaktiviert Zwei-Faktor-Authentifizierung');
                $this->addFlash('success', 'Two-factor authentication has been enabled.');

                return $this->redirect('/admin/?myprofile');
            }

            $this->addFlash('error', 'Der eigegebene Code ist ungütig! Bitte wiederhole den Vorgang!');
        }

        return $this->render('admin/profile/tfa-disable.html.twig');
    }
}
