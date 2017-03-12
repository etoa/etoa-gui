<?php

namespace EtoA\Quest;

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
    }
}
