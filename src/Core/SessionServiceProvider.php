<?php

namespace EtoA\Core;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SessionServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $pimple)
    {
    }

    public function boot(Application $app)
    {
        $app->before(function (Request $request) {
            $session = \UserSession::getInstance();
            if (!$session->validate(0)) {
                throw new AccessDeniedHttpException();
            }

            global $cu;
            if (isset($cu)) {
                $request->attributes->set('currentUser', $cu);
            } else {
                $request->attributes->set('currentUser', new \CurrentUser(\UserSession::getInstance()->user_id));
            }

        }, Application::EARLY_EVENT);
    }
}
