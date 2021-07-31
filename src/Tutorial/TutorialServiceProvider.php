<?php declare(strict_types=1);

namespace EtoA\Tutorial;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class TutorialServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    public function connect(Application $app): ControllerCollection
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers
            ->put('/api/tutorials/{tutorialId}/close', 'etoa.tutorial.controller:closeAction')
            ->assert('tutorialId', '\d+')
            ->bind('api.tutorial.close');

        return $controllers;
    }

    public function register(Container $pimple): void
    {
        $pimple[TutorialUserProgressRepository::class] = function (Container $pimple): TutorialUserProgressRepository {
            return new TutorialUserProgressRepository($pimple['db']);
        };

        $pimple[TutorialManager::class] = function (Container $pimple): TutorialManager {
            return new TutorialManager($pimple['db']);
        };

        $pimple['etoa.tutorial.controller'] = function (Container $pimple): TutorialController {
            return new TutorialController($pimple[TutorialUserProgressRepository::class]);
        };
    }
}
