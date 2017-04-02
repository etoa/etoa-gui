<?php

namespace EtoA\Core;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpKernel\KernelEvents;

class ParamConverterServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['etoa.listener.param_converter'] = function () {
            return new ParamConverterListener();
        };
    }

    public function boot(Application $app)
    {
        $app->on(KernelEvents::CONTROLLER, [$app['etoa.listener.param_converter'], 'onKernelController']);
    }
}
