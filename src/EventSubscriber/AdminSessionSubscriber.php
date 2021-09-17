<?php declare(strict_types=1);

namespace EtoA\EventSubscriber;

use EtoA\Admin\AdminSessionManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
            /** @var CurrentAdmin $user */
            $user = $event->getAuthenticatedToken()->getUser();
            $this->adminSessionRepository->removeByUserOrId($session->getId(), $user->getId());
            $this->adminSessionRepository->create(
                $session->getId(),
                $user->getId(),
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
            if ($lastAction + $this->config->getInt('admin_timeout') > $time) {
                if ($this->adminSessionRepository->update($session->getId(), $user->getId(), $time, $event->getRequest()->getClientIp())) {
                    $session->set('lastAction', $time);

                    return;
                }
            }

            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('admin.logout'), RedirectResponse::HTTP_TEMPORARY_REDIRECT));
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        if ($event->getToken() !== null && $event->getToken()->getUser() instanceof CurrentAdmin) {
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
