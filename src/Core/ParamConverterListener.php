<?php declare(strict_types=1);

namespace EtoA\Core;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ParamConverterListener
{
    public function onKernelController(FilterControllerEvent $event): void
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        } else {
            $r = new \ReflectionFunction($controller);
        }

        // automatically apply conversion for non-configured objects
        foreach ($r->getParameters() as $param) {
            if (!$param->getClass() instanceof \ReflectionClass || $param->getClass()->isInstance($request)) {
                continue;
            }

            $class = $param->getClass()->getName();
            $name = $param->getName();
            if (TokenContext::class === $class) {
                $value = new TokenContext($request->attributes->get('currentUser'));
            } else {
                continue;
            }

            $request->attributes->set($name, $value);
        }
    }
}
