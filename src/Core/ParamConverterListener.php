<?php declare(strict_types=1);

namespace EtoA\Core;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ParamConverterListener implements EventSubscriberInterface
{
    private ConfigurationService $configurationService;
    private UserRepository $userRepository;
    private AuthenticationUtils $authenticationUtils;

    public function __construct(
        ConfigurationService $configurationService,
        UserRepository $userRepository,
        AuthenticationUtils $authenticationUtils
    )
    {
        $this->configurationService = $configurationService;
        $this->userRepository = $userRepository;
        $this->authenticationUtils = $authenticationUtils;
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
            if (!$param->getType() instanceof \ReflectionNamedType || $param->getType()->getName() === Request::class) {
                continue;
            }

            $class = $param->getType()->getName();
            $name = $param->getName();
            if (TokenContext::class === $class) {
                if (!$request->attributes->has('currentUser')) {
                    $user = $this->userRepository->getUserByNick($this->authenticationUtils->getLastUsername());
                    if (!$user) {
                        throw new AccessDeniedHttpException();
                    }

                    $request->attributes->set('currentUser', $user);
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
