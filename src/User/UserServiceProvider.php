<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Backend\BackendMessageService;
use EtoA\Bookmark\BookmarkRepository;
use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\BuddyList\BuddyListRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Fleet\FleetRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Log\LogRepository;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Notepad\NotepadRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Planet\PlanetTypeRepository;
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

        $pimple[UserRatingService::class] = function (Container $pimple): UserRatingService {
            return new UserRatingService($pimple[UserRatingRepository::class], $pimple[LogRepository::class]);
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

        $pimple[UserLoginFailureRepository::class] = function (Container $pimple): UserLoginFailureRepository {
            return new UserLoginFailureRepository($pimple['db']);
        };

        $pimple[UserStatRepository::class] = function (Container $pimple): UserStatRepository {
            return new UserStatRepository($pimple['db']);
        };

        $pimple[UserSessionManager::class] = function (Container $pimple): UserSessionManager {
            return new UserSessionManager(
                $pimple[UserSessionRepository::class],
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[LogRepository::class]
            );
        };

        $pimple[UserHolidayService::class] = function (Container $pimple): UserHolidayService {
            return new UserHolidayService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[BuildingRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[ShipQueueRepository::class],
                $pimple[DefenseQueueRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[FleetRepository::class],
                $pimple[BackendMessageService::class]
            );
        };

        $pimple[UserService::class] = function (Container $pimple): UserService {
            return new UserService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[UserRatingRepository::class],
                $pimple[UserPropertiesRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[BuildingRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[ShipQueueRepository::class],
                $pimple[DefenseQueueRepository::class],
                $pimple[MailSenderService::class],
                $pimple[PlanetService::class],
                $pimple[UserSittingRepository::class],
                $pimple[UserWarningRepository::class],
                $pimple[UserMultiRepository::class],
                $pimple[AllianceRepository::class],
                $pimple[AllianceApplicationRepository::class],
                $pimple[MarketAuctionRepository::class],
                $pimple[MarketResourceRepository::class],
                $pimple[MarketShipRepository::class],
                $pimple[NotepadRepository::class],
                $pimple[FleetRepository::class],
                $pimple[ShipRepository::class],
                $pimple[DefenseRepository::class],
                $pimple[MissileRepository::class],
                $pimple[BuddyListRepository::class],
                $pimple[TicketRepository::class],
                $pimple[BookmarkRepository::class],
                $pimple[FleetBookmarkRepository::class],
                $pimple[UserPointsRepository::class],
                $pimple[UserCommentRepository::class],
                $pimple[UserSurveillanceRepository::class],
                $pimple[BackendMessageService::class],
                $pimple[UserLogRepository::class],
                $pimple[UserToXml::class],
                $pimple[LogRepository::class]
            );
        };

        $pimple[UserToXml::class] = function (Container $pimple): UserToXml {
            return new UserToXml(
                $pimple[UserRepository::class],
                $pimple[AllianceRepository::class],
                $pimple[RaceDataRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[PlanetTypeRepository::class],
                $pimple[BuildingRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[TechnologyDataRepository::class],
                $pimple[ShipRepository::class],
                $pimple[ShipDataRepository::class],
                $pimple[FleetRepository::class],
                $pimple[DefenseRepository::class],
                $pimple[DefenseDataRepository::class],
                $pimple['app.cache_dir']
            );
        };

        $pimple[UserUniverseDiscoveryService::class] = function (Container $pimple): UserUniverseDiscoveryService {
            return new UserUniverseDiscoveryService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class]
            );
        };

        $pimple[UserStats::class] = function (Container $pimple): UserStats {
            return new UserStats(
                $pimple[UserOnlineStatsRepository::class],
                $pimple[PlanetRepository::class]
            );
        };

        $pimple[UserSetupService::class] = function (Container $pimple): UserSetupService {
            return new UserSetupService(
                $pimple[DefaultItemRepository::class],
                $pimple[BuildingRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[ShipRepository::class],
                $pimple[DefenseRepository::class],
            );
        };
    }
}
