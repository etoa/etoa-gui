<?php

namespace EtoA\Legacy;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;

class LegacyProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->match('/{url}', 'etoa.legacy.controller:catchAllAction')->bind('legacy.catchall')->assert('url', '^(.)*$');

        return $controllers;
    }

    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['etoa.legacy.controller'] = $app->share(function ($app) {
            return new LegacyController();
        });
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function boot(Application $app)
    {
    }
}
