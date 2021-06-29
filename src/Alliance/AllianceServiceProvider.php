<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\User\UserLogRepository;
use EtoA\User\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AllianceServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple): void
    {
        $pimple[AllianceManagementService::class] = function (Container $pimple): AllianceManagementService {
            return new AllianceManagementService(
                $pimple[AllianceRepository::class],
                $pimple[AllianceHistoryRepository::class],
                $pimple[UserRepository::class],
                $pimple[UserLogRepository::class],
                $pimple['dispatcher']
            );
        };

        $pimple[AllianceRepository::class] = function (Container $pimple): AllianceRepository {
            return new AllianceRepository($pimple['db']);
        };

        $pimple['etoa.alliance.repository'] = function (Container $pimple): AllianceRepository {
            return $pimple[AllianceRepository::class];
        };

        $pimple[AlliancePointsRepository::class] = function (Container $pimple): AlliancePointsRepository {
            return new AlliancePointsRepository($pimple['db']);
        };

        $pimple[AllianceHistoryRepository::class] = function (Container $pimple): AllianceHistoryRepository {
            return new AllianceHistoryRepository($pimple['db']);
        };

        $pimple['etoa.alliance.building.repository'] = function (Container $pimple): AllianceBuildingRepository {
            return new AllianceBuildingRepository($pimple['db']);
        };
        $pimple['etoa.alliance.technology.repository'] = function (Container $pimple): AllianceTechnologyRepository {
            return new AllianceTechnologyRepository($pimple['db']);
        };
    }
}
