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

        $pimple['etoa.alliance.repository'] = function (Container $pimple): AllianceRepository {
            return $pimple[AllianceRepository::class];
        };

        $pimple[AlliancePointsRepository::class] = function (Container $pimple): AlliancePointsRepository {
            return new AlliancePointsRepository($pimple['db']);
        };

        $pimple['etoa.alliance.building.repository'] = function (Container $pimple): AllianceBuildingRepository {
            return new AllianceBuildingRepository($pimple['db']);
        };
        $pimple['etoa.alliance.technology.repository'] = function (Container $pimple): AllianceTechnologyRepository {
            return new AllianceTechnologyRepository($pimple['db']);
        };
    }
}
