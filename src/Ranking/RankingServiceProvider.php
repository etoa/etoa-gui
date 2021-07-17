<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Race\RaceDataRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\User\UserRepository;
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
                $pimple[PlanetRepository::class]
            );
        };
    }
}
