<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AllianceServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple): void
    {
        $pimple[AllianceRepository::class] = function (Container $pimple): AllianceRepository {
            return new AllianceRepository($pimple['db']);
        };

        $pimple[AlliancePointsRepository::class] = function (Container $pimple): AlliancePointsRepository {
            return new AlliancePointsRepository($pimple['db']);
        };

        $pimple[AllianceStatsRepository::class] = function (Container $pimple): AllianceStatsRepository {
            return new AllianceStatsRepository($pimple['db']);
        };

        $pimple[AllianceHistoryRepository::class] = function (Container $pimple): AllianceHistoryRepository {
            return new AllianceHistoryRepository($pimple['db']);
        };

        $pimple[AllianceBuildingRepository::class] = function (Container $pimple): AllianceBuildingRepository {
            return new AllianceBuildingRepository($pimple['db']);
        };
        $pimple[AllianceTechnologyRepository::class] = function (Container $pimple): AllianceTechnologyRepository {
            return new AllianceTechnologyRepository($pimple['db']);
        };
    }
}
