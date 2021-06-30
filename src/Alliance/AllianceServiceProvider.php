<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Message\MessageRepository;
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
                $pimple[AllianceBoardRepository::class],
                $pimple[AllianceApplicationRepository::class],
                $pimple[AllianceBuildingRepository::class],
                $pimple[AllianceTechnologyRepository::class],
                $pimple[AlliancePaymentRepository::class],
                $pimple[AllianceNewsRepository::class],
                $pimple[AlliancePointRepository::class],
                $pimple[AlliancePollRepository::class],
                $pimple[UserRepository::class],
                $pimple[UserLogRepository::class],
                $pimple['etoa.config.service'],
                $pimple[MessageRepository::class],
                $pimple[FleetRepository::class],
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

        $pimple[AllianceBoardRepository::class] = function (Container $pimple): AllianceBoardRepository {
            return new AllianceBoardRepository($pimple['db']);
        };

        $pimple[AllianceApplicationRepository::class] = function (Container $pimple): AllianceApplicationRepository {
            return new AllianceApplicationRepository($pimple['db']);
        };

        $pimple[AlliancePaymentRepository::class] = function (Container $pimple): AlliancePaymentRepository {
            return new AlliancePaymentRepository($pimple['db']);
        };

        $pimple[AllianceNewsRepository::class] = function (Container $pimple): AllianceNewsRepository {
            return new AllianceNewsRepository($pimple['db']);
        };

        $pimple[AlliancePointRepository::class] = function (Container $pimple): AlliancePointRepository {
            return new AlliancePointRepository($pimple['db']);
        };

        $pimple[AlliancePollRepository::class] = function (Container $pimple): AlliancePollRepository {
            return new AlliancePollRepository($pimple['db']);
        };

        $pimple[AllianceBuildingRepository::class] = function (Container $pimple): AllianceBuildingRepository {
            return new AllianceBuildingRepository($pimple['db']);
        };

        $pimple['etoa.alliance.building.repository'] = function (Container $pimple): AllianceBuildingRepository {
            return $pimple[AllianceBuildingRepository::class];
        };

        $pimple[AllianceTechnologyRepository::class] = function (Container $pimple): AllianceTechnologyRepository {
            return new AllianceTechnologyRepository($pimple['db']);
        };

        $pimple['etoa.alliance.technology.repository'] = function (Container $pimple): AllianceTechnologyRepository {
            return $pimple[AllianceTechnologyRepository::class];
        };
    }
}
