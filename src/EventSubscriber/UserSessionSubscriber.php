<?php declare(strict_types=1);

namespace EtoA\EventSubscriber;

use EtoA\Entity\UserSession;
use EtoA\User\UserSessionManager;
use EtoA\User\UserSessionRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Security\Player\CurrentPlayer;
use EtoA\User\UserSittingRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class UserSessionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserSessionManager $userSessionManager,
        private readonly UserSessionRepository $userSessionRepository,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly ConfigurationService $config,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserSittingRepository $userSittingRepository,
    )
    {}

    public function onSuccessfulLogin(LoginSuccessEvent $event): void
    {
        if ($event->getAuthenticatedToken()->getUser() instanceof CurrentPlayer) {
            $time = time();
            $session = $event->getRequest()->getSession();
            /** @var CurrentPlayer $user */
            $user = $event->getAuthenticatedToken()->getUser();

            if($user->getData()->getSession()) {
                $this->userSessionManager->unregisterSession($user->getData(), false);
            }

            $session->set('chat',$user->getData()->getUserProperties()->isStartUpChat());

            $sessionModel = new UserSession();
            $sessionModel
                ->setId($session->getId())
                ->setUser($user->getData())
                ->setIpAddr($event->getRequest()->getClientIp())
                ->setUserAgent($event->getRequest()->headers->get('User-Agent'))
                ->setTimeLogin($time);

            $user->getData()->setSession($sessionModel);
            $this->userSessionRepository->save();

            $sittingEntry = $this->userSittingRepository->getActiveUserEntry($user->getData());
            if ($sittingEntry !== null) {
                $session->set('sittingActive', true);
                $session->set('sittingUntil', $sittingEntry->getDateTo());
            }

            $session->set('lastAction', $time);
        }
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $token = $this->tokenStorage->getToken();
        if ($token !== null && $token->getUser() instanceof CurrentPlayer) {
            /** @var CurrentPlayer $user */
            $user = $token->getUser();

            $session = $event->getRequest()->getSession();

            $this->userSessionManager->migrateSession($user->getData(), $session->getId());

            $time = time();
            $lastAction = $session->get('lastAction');
            $sittingActive = $session->has('sittingActive')?$session->get('sittingActive'):null;
            $sittingUntil = $session->has('sittingUntil')?$session->get('sittingUntil'):null;
            $botCount = (int) $session->get('botCount');
            $lastSpan = (int) $session->get('lastSpan');
            $timeout = $time - $this->config->getInt('user_timeout');

            if ($lastAction === null || $lastAction > $timeout) {
                $allows = false;
                $bot = false;

                if (($time - $lastAction) >= 5 && ($lastSpan >= $time - $lastAction - 1 && $lastSpan <= $time - $lastAction + 1)) {
                    $botCount++;
                    $bot = $botCount > $this->config->getInt('bot_max_count');
                } else {
                    $lastSpan = $time -  $lastAction;
                    $botCount = 0;
                }

                if ($sittingActive) {
                    if (time() < $sittingUntil) {
                        $activeSitting = $this->userSittingRepository->getActiveUserEntry($user->getData());

                        if ($activeSitting !== null) {
                            $allows = true;
                        }
                    }
                } else {
                    $allows = true;
                }

                if ($allows) {
                    if (!$bot) {
                        $this->userSessionRepository->update($session->getId(), $time, $botCount, $lastSpan, $event->getRequest()->getClientIp());
                        $session->set('lastAction', $time);
                        $session->set('botCount', $botCount);
                        $session->set('lastSpan', $lastSpan);
                        return;
                    }
                }
            }

            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('game.logout'), Response::HTTP_TEMPORARY_REDIRECT));
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        if ($event->getToken() !== null && $event->getToken()->getUser() instanceof CurrentPlayer) {
            $event->getRequest()->getSession()->remove('lastAction');
            $this->userSessionManager->unregisterSession($event->getToken()->getUser()->getData());
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
