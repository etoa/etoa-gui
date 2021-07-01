<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Quest\Initialization\QuestBuilder;
use EtoA\Quest\Initialization\QuestInitializer;
use EtoA\Quest\Log\QuestGameLog;
use EtoA\Quest\Log\QuestLogRepository;
use EtoA\Quest\Progress\ContainerAwareFunctionBuilder;
use EtoA\Quest\Progress\FunctionBuilder;
use EtoA\Quest\Reward\DefenseRewardCollector;
use EtoA\Quest\Reward\MissileRewardCollector;
use EtoA\Quest\Reward\ShipRewardCollector;
use EtoA\Ship\ShipRepository;
use EtoA\Universe\PlanetRepository;
use LittleCubicleGames\Quests\Progress\ProgressFunctionBuilder;
use LittleCubicleGames\Quests\Progress\StateFunctionBuilder;
use LittleCubicleGames\Quests\ServiceProvider;
use LittleCubicleGames\Quests\Storage\QuestStorageInterface;
use Pimple\Container;
use Silex\Api\BootableProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class QuestServiceProvider extends ServiceProvider implements ControllerProviderInterface, BootableProviderInterface
{
    public function connect(Application $app): ControllerCollection
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers
            ->put('/api/quests/{questId}/advance/{transition}', 'etoa.quest.controller:advanceAction')
            ->assert('questId', '\d+')
            ->bind('api.quest.advance');

        return $controllers;
    }

    public function register(Container $pimple): void
    {
        parent::register($pimple);

        $pimple['etoa.quests.enabled'] = function (Container $pimple): bool {
            try {
                /** @var ConfigurationService $config */
                $config = $pimple[ConfigurationService::class];

                return $config->getBoolean('quest_system_enable');
            } catch (\Throwable $e) {
                $pimple['logger']->warning('Failed to load quest config', [
                    'exception' => $e,
                ]);

                return false;
            }
        };

        $pimple['etoa.quest.controller'] = function (Container $pimple): QuestController {
            return new QuestController($pimple['cubicle.quests.advancer'], $pimple['etoa.quest.presenter'], $pimple['cubicle.quests.storage']);
        };

        $pimple['etoa.quest.repository'] = function (Container $pimple): QuestRepository {
            return new QuestRepository($pimple['db']);
        };

        $pimple['etoa.quest.log.repository'] = function (Container $pimple): QuestLogRepository {
            return new QuestLogRepository($pimple['db']);
        };

        $pimple['cubicle.quests.storage'] = function (Container $pimple): QuestStorageInterface {
            return $pimple['etoa.quest.repository'];
        };

        $pimple['cubicle.quests.logger'] = function (Container $pimple): array {
            return [
                $pimple['etoa.quest.log.repository'],
                new QuestGameLog(),
            ];
        };

        $pimple['cubicle.quests.initializer'] = function (Container $pimple): QuestInitializer {
            $initializer = new QuestInitializer($pimple['cubicle.quests.storage'], $pimple['cubicle.quests.listener.progress'], $pimple['cubicle.quests.slot.loader'], $pimple['cubicle.quests.initializer.queststarter'], $pimple['dispatcher']);
            $initializer->setIsQuestSystemOn($pimple['etoa.quests.enabled']);

            return $initializer;
        };

        $pimple['cubicle.quests.initializer.questbuilder'] = function (): QuestBuilder {
            return new QuestBuilder();
        };

        $pimple['etoa.quest.reward.shipcollector'] = function (Container $pimple): ShipRewardCollector {
            return new ShipRewardCollector($pimple[ShipRepository::class], $pimple[PlanetRepository::class]);
        };
        $pimple['etoa.quest.reward.defensecollector'] = function (Container $pimple): DefenseRewardCollector {
            return new DefenseRewardCollector($pimple[DefenseRepository::class], $pimple[PlanetRepository::class]);
        };
        $pimple['etoa.quest.reward.missilecollector'] = function (Container $pimple): MissileRewardCollector {
            return new MissileRewardCollector($pimple['etoa.missile.repository'], $pimple[PlanetRepository::class]);
        };

        $pimple['cubicle.quests.rewards.collectors'] = function (Container $pimple): array {
            return [
                $pimple['etoa.quest.reward.shipcollector'],
                $pimple['etoa.quest.reward.defensecollector'],
                $pimple['etoa.quest.reward.missilecollector'],
            ];
        };

        $pimple['cubicle.quests.progress.function.builder'] = function (Container $pimple): ProgressFunctionBuilder {
            return new ProgressFunctionBuilder([
                new StateFunctionBuilder(),
                new FunctionBuilder(),
                new ContainerAwareFunctionBuilder($pimple),
            ]);
        };

        $pimple['etoa.quest.presenter'] = function (Container $pimple): QuestPresenter {
            return new QuestPresenter(
                $pimple['cubicle.quests.registry'],
                $pimple['etoa.missile.datarepository'],
                $pimple['etoa.ship.datarepository'],
                $pimple['etoa.defense.datarepository']
            );
        };

        $pimple['etoa.quest.responselistener'] = function (Container $pimple): QuestResponseListener {
            return new QuestResponseListener($pimple['etoa.quest.presenter']);
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher): void
    {
        if ((bool) $app['etoa.quests.enabled']) {
            parent::subscribe($app, $dispatcher);

            $dispatcher->addSubscriber($app['etoa.quest.responselistener']);
        }
    }

    public function boot(Application $app): void
    {
        $app->before(function (Request $request, Application $app): void {
            $currentUser = $request->attributes->get('currentUser');
            if ($currentUser instanceof \CurrentUser && $currentUser->isSetup() && $app['etoa.tutorial.userprogressrepository']->hasFinishedTutorial($currentUser->getId())) {
                $app['cubicle.quests.initializer']->initialize($currentUser->getId());
            }
        });
    }
}
