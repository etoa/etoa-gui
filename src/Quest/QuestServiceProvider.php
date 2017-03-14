<?php

namespace EtoA\Quest;

use EtoA\Quest\Progress\FunctionBuilder;
use EtoA\Quest\Reward\DefenseRewardCollector;
use EtoA\Quest\Reward\MissileRewardCollector;
use EtoA\Quest\Reward\ShipRewardCollector;
use LittleCubicleGames\Quests\Progress\ProgressFunctionBuilder;
use LittleCubicleGames\Quests\Progress\StateFunctionBuilder;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QuestServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['etoa.quest.repository'] = function (Container $pimple) {
            return new QuestRepository($pimple['db']);
        };

        $pimple['cubicle.quests.storage'] = function (Container $pimple) {
            return $pimple['etoa.quest.repository'];
        };

        $pimple['etoa.quest.reward.shipcollector'] = function (Container $pimple) {
            return new ShipRewardCollector($pimple['etoa.ship.repository'], $pimple['etoa.planet.repository']);
        };
        $pimple['etoa.quest.reward.defensecollector'] = function (Container $pimple) {
            return new DefenseRewardCollector($pimple['etoa.defense.repository'], $pimple['etoa.planet.repository']);
        };
        $pimple['etoa.quest.reward.missilecollector'] = function (Container $pimple) {
            return new MissileRewardCollector($pimple['etoa.missile.repository'], $pimple['etoa.planet.repository']);
        };

        $pimple['cubicle.quests.rewards.collectors'] = function (Container $pimple) {
            return [
                $pimple['etoa.quest.reward.shipcollector'],
                $pimple['etoa.quest.reward.defensecollector'],
                $pimple['etoa.quest.reward.missilecollector'],
            ];
        };

        $pimple['cubicle.quests.progress.function.builder'] = function () {
            return new ProgressFunctionBuilder([
                new StateFunctionBuilder(),
                new FunctionBuilder(),
            ]);
        };
    }
}
