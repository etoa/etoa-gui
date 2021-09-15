<?php declare(strict_types=1);

namespace EtoA\Core;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ParamConverterListener implements EventSubscriberInterface
{
    private ConfigurationService $configurationService;
    private UserRepository $userRepository;

    public function __construct(ConfigurationService $configurationService, UserRepository $userRepository)
    {
        $this->configurationService = $configurationService;
        $this->userRepository = $userRepository;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $request = $event->getRequest();
        $r = new \ReflectionMethod($controller[0], $controller[1]);
        // automatically apply conversion for non-configured objects
        foreach ($r->getParameters() as $param) {
            if (!$param->getClass() instanceof \ReflectionClass || $param->getClass()->isInstance($request)) {
                continue;
            }

            $class = $param->getClass()->getName();
            $name = $param->getName();
            if (TokenContext::class === $class) {
                if (!$request->attributes->has('currentUser')) {
                    $userId = \UserSession::getInstance($this->configurationService)->user_id;
                    if (!(bool) $userId) {
                        throw new AccessDeniedHttpException();
                    }

                    $user = $this->userRepository->getUser($userId);
                    if ($user === null) {
                        throw new AccessDeniedHttpException();
                    }

                    $request->attributes->set('currentUser', new \User($user));
                }

                $value = new TokenContext($request->attributes->get('currentUser'));
            } else {
                continue;
            }

            $request->attributes->set($name, $value);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'onKernelController'];
    }
}
