<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[UserRepository::class] = function (Container $pimple): UserRepository {
            return new UserRepository($pimple['db']);
        };

        $pimple[UserSessionRepository::class] = function (Container $pimple): UserSessionRepository {
            return new UserSessionRepository($pimple['db']);
        };

        $pimple[UserSurveillanceRepository::class] = function (Container $pimple): UserSurveillanceRepository {
            return new UserSurveillanceRepository($pimple['db']);
        };

        $pimple[UserPointsRepository::class] = function (Container $pimple): UserPointsRepository {
            return new UserPointsRepository($pimple['db']);
        };

        $pimple[UserPropertiesRepository::class] = function (Container $pimple): UserPropertiesRepository {
            return new UserPropertiesRepository($pimple['db']);
        };

        $pimple[UserCommentRepository::class] = function (Container $pimple): UserCommentRepository {
            return new UserCommentRepository($pimple['db']);
        };

        $pimple[UserRatingRepository::class] = function (Container $pimple): UserRatingRepository {
            return new UserRatingRepository($pimple['db']);
        };

        $pimple[UserMultiRepository::class] = function (Container $pimple): UserMultiRepository {
            return new UserMultiRepository($pimple['db']);
        };

        $pimple[UserLogRepository::class] = function (Container $pimple): UserLogRepository {
            return new UserLogRepository($pimple['db']);
        };

        $pimple[UserSittingRepository::class] = function (Container $pimple): UserSittingRepository {
            return new UserSittingRepository($pimple['db']);
        };

        $pimple[UserWarningRepository::class] = function (Container $pimple): UserWarningRepository {
            return new UserWarningRepository($pimple['db']);
        };

        $pimple[UserOnlineStatsRepository::class] = function (Container $pimple): UserOnlineStatsRepository {
            return new UserOnlineStatsRepository($pimple['db']);
        };

        $pimple[UserSessionManager::class] = function (Container $pimple): UserSessionManager {
            return new UserSessionManager(
                $pimple[UserSessionRepository::class],
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class]
            );
        };

        $pimple[UserService::class] = function (Container $pimple): UserService {
            return new UserService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[BuildingRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[ShipQueueRepository::class],
                $pimple[DefenseQueueRepository::class]
            );
        };
    }
}
