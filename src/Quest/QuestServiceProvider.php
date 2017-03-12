<?php

namespace EtoA\Quest;

use EtoA\Quest\Reward\DefenseRewardCollector;
use EtoA\Quest\Reward\MissileRewardCollector;
use EtoA\Quest\Reward\ShipRewardCollector;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QuestServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple)
    {
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
    }
}
