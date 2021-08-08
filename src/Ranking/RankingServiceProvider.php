<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceStatsRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Fleet\FleetRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserStatRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RankingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[PointsService::class] = function (Container $pimple): PointsService {
            return new PointsService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[AllianceRepository::class]
            );
        };

        $pimple[GameStatsGenerator::class] = function (Container $pimple): GameStatsGenerator {
            return new GameStatsGenerator(
                $pimple[PlanetTypeRepository::class],
                $pimple[SolarTypeRepository::class],
                $pimple[RaceDataRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[BuildingRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[ShipRepository::class],
                $pimple[DefenseRepository::class],
                $pimple[UserPropertiesRepository::class],
            );
        };

        $pimple[RankingService::class] = function (Container $pimple): RankingService {
            return new RankingService(
                $pimple[ConfigurationService::class],
                $pimple[RuntimeDataStore::class],
                $pimple[AllianceRepository::class],
                $pimple[AllianceStatsRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[BuildingRepository::class],
                $pimple[BuildingDataRepository::class],
                $pimple[BuildingPointRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[TechnologyDataRepository::class],
                $pimple[TechnologyPointRepository::class],
                $pimple[ShipRepository::class],
                $pimple[ShipDataRepository::class],
                $pimple[FleetRepository::class],
                $pimple[DefenseRepository::class],
                $pimple[DefenseDataRepository::class],
                $pimple[RaceDataRepository::class],
                $pimple[UserStatRepository::class],
                $pimple[UserRepository::class],
                $pimple[CellRepository::class],
                $pimple[EntityRepository::class],
            );
        };

        $pimple[UserBannerService::class] = function (Container $pimple): UserBannerService {
            return new UserBannerService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[AllianceRepository::class],
                $pimple[RaceDataRepository::class]
            );
        };

        $pimple[UserTitlesService::class] = function (Container $pimple): UserTitlesService {
            return new UserTitlesService(
                $pimple[ConfigurationService::class],
                $pimple[RaceDataRepository::class],
                $pimple[UserStatRepository::class],
                $pimple[UserRepository::class],
                $pimple[UserRatingRepository::class]
            );
        };
    }
}
