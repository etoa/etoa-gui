<?php declare(strict_types=1);

namespace EtoA\Core;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpKernel\KernelEvents;

class ParamConverterServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.listener.param_converter'] = function (): ParamConverterListener {
            return new ParamConverterListener();
        };
    }

    public function boot(Application $app): void
    {
        $app->on(KernelEvents::CONTROLLER, [$app['etoa.listener.param_converter'], 'onKernelController']);
    }
}
