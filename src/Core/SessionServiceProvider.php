<?php declare(strict_types=1);

namespace EtoA\Core;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\ChatUser;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SessionServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $pimple): void
    {
    }

    public function boot(Application $app): void
    {
        $app->before(function (Request $request) use ($app): void {
            /** @var ConfigurationService */
            $config = $app[ConfigurationService::class];

            $session = \UserSession::getInstance($config);
            if (strpos($request->attributes->get('_route'), 'api.chat') === 0) {
                $currentUser = $this->validateChatUser($session);
            } else {
                $currentUser = $this->validateUser($session, $config);
            }

            $request->attributes->set('currentUser', $currentUser);
        });
    }

    private function validateChatUser(\UserSession $session): ChatUser
    {
        if (!$session->chatValidate()) {
            throw new AccessDeniedHttpException();
        }

        return new ChatUser($session->user_id, $session->user_nick);
    }

    private function validateUser(\UserSession $session, ConfigurationService $config): \User
    {
        if (!$session->validate(0)) {
            throw new AccessDeniedHttpException();
        }

        global $cu;
        if (isset($cu)) {
            $currentUser = $cu;
        } else {
            $currentUser = new \User(\UserSession::getInstance($config)->user_id);
        }

        if (!$currentUser->isValid) {
            throw new AccessDeniedHttpException();
        }

        return $currentUser;
    }
}
