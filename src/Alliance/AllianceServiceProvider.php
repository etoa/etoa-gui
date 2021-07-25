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

        $pimple[AllianceApplicationRepository::class] = function (Container $pimple): AllianceApplicationRepository {
            return new AllianceApplicationRepository($pimple['db']);
        };

        $pimple[AllianceSpendRepository::class] = function (Container $pimple): AllianceSpendRepository {
            return new AllianceSpendRepository($pimple['db']);
        };

        $pimple[AllianceNewsRepository::class] = function (Container $pimple): AllianceNewsRepository {
            return new AllianceNewsRepository($pimple['db']);
        };

        $pimple[AlliancePollRepository::class] = function (Container $pimple): AlliancePollRepository {
            return new AlliancePollRepository($pimple['db']);
        };

        $pimple[AllianceRankRepository::class] = function (Container $pimple): AllianceRankRepository {
            return new AllianceRankRepository($pimple['db']);
        };
    }
}
