<?php declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RankingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.rankings.points.service'] = function (Container $pimple): PointsService {
            return new PointsService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[AllianceRepository::class]
            );
        };
    }
}
