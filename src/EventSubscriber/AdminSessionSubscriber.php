<?php declare(strict_types=1);

namespace EtoA\EventSubscriber;

use EtoA\Admin\AdminSessionManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\AdminUser;
use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AdminSessionSubscriber implements EventSubscriberInterface
{
    private AdminSessionManager $adminSessionManager;
    private AdminSessionRepository $adminSessionRepository;
    private TokenStorageInterface $tokenStorage;
    private ConfigurationService $config;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(AdminSessionManager $adminSessionManager, AdminSessionRepository $adminSessionRepository, TokenStorageInterface $tokenStorage, ConfigurationService $config, UrlGeneratorInterface $urlGenerator)
    {
        $this->adminSessionManager = $adminSessionManager;
        $this->adminSessionRepository = $adminSessionRepository;
        $this->tokenStorage = $tokenStorage;
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    public function onSuccessfulLogin(LoginSuccessEvent $event): void
    {
        if ($event->getAuthenticatedToken()->getUser() instanceof CurrentAdmin) {
            $time = time();
            $session = $event->getRequest()->getSession();
            /** @var AdminUser $user */
            $user = $event->getAuthenticatedToken()->getUser()->getData();
            $this->adminSessionRepository->removeByUserOrId($session->getId(), $user);
            $this->adminSessionRepository->create(
                $session->getId(),
                $user,
                $event->getRequest()->getClientIp(),
                $event->getRequest()->headers->get('User-Agent'),
                $time,
            );

            $session->set('lastAction', $time);
        }
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $token = $this->tokenStorage->getToken();
        if ($token !== null && $token->getUser() instanceof CurrentAdmin) {
            /** @var CurrentAdmin $user */
            $user = $token->getUser();

            $session = $event->getRequest()->getSession();

            $time = time();
            $lastAction = $session->get('lastAction');
            $timeout = $time - $this->config->getInt('admin_timeout');
            $userAgent = (string) $event->getRequest()->headers->get('User-Agent');
            if ($lastAction === null || $lastAction > $timeout) {
                $this->migrateSession($event, $user, $userAgent);

                if ($this->adminSessionRepository->exists($session->getId(), $user->getId(), $userAgent)) {
                    $this->adminSessionRepository->update($session->getId(), $user->getId(), $time, $event->getRequest()->getClientIp());
                    $session->set('lastAction', $time);

                    return;
                }
            }

            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('admin.logout'), Response::HTTP_TEMPORARY_REDIRECT));
        }
    }

    /**
     * Symfony migrates the session id whenever another firewall authenticates in the
     * same browser - logging into the game next to the admin area does exactly that.
     * The session row still belongs to this admin, it only carries the previous id, so
     * move it over. A row that is really gone (kicked, timed out) is left alone and the
     * caller logs the admin out.
     */
    private function migrateSession(RequestEvent $event, CurrentAdmin $user, string $userAgent): void
    {
        $session = $event->getRequest()->getSession();
        if ($this->adminSessionRepository->exists($session->getId(), $user->getId(), $userAgent)) {
            return;
        }

        $previous = $this->adminSessionRepository->findForUser($user->getId());
        if ($previous === null || $previous->getUserAgent() !== $userAgent) {
            return;
        }

        $this->adminSessionRepository->removeByUserOrId($session->getId(), $user->getData());
        $this->adminSessionRepository->create(
            $session->getId(),
            $user->getData(),
            (string) $event->getRequest()->getClientIp(),
            $userAgent,
            (int) $previous->getTimeLogin(),
        );
    }

    public function onLogout(LogoutEvent $event): void
    {
        if ($event->getToken() !== null && $event->getToken()->getUser() instanceof CurrentAdmin) {
            $event->getRequest()->getSession()->remove('lastAction');
            $this->adminSessionManager->unregisterSession($event->getRequest()->getSession()->getId(), true);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => ['onSuccessfulLogin', -512],
            LogoutEvent::class => ['onLogout', 512],
            RequestEvent::class => ['onKernelRequest', -512],
        ];
    }
}
