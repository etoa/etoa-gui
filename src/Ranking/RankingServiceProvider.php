<?php declare(strict_types=1);

namespace EtoA\Ranking;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RankingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.rankings.points.service'] = function (Container $pimple): PointsService {
            return new PointsService(
                $pimple['etoa.config.service'],
                $pimple['etoa.user.repository'],
                $pimple['etoa.alliance.repository'],
                $pimple['etoa.log.service']
            );
        };
    }
}
