<?php declare(strict_types=1);

namespace EtoA\Core;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpKernel\KernelEvents;

class ParamConverterServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[ParamConverterListener::class] = function (Container $pimple): ParamConverterListener {
            return new ParamConverterListener($pimple[ConfigurationService::class], $pimple[UserRepository::class]);
        };
    }

    public function boot(Application $app): void
    {
        $app->on(KernelEvents::CONTROLLER, [$app[ParamConverterListener::class], 'onKernelController']);
    }
}
