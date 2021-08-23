<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Race\RaceDataRepository;
use EtoA\Specialist\SpecialistService;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuildingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[BuildingRepository::class] = function (Container $pimple): BuildingRepository {
            return new BuildingRepository($pimple['db']);
        };

        $pimple[BuildingDataRepository::class] = function (Container $pimple): BuildingDataRepository {
            return new BuildingDataRepository($pimple['db']);
        };

        $pimple[BuildingTypeDataRepository::class] = function (Container $pimple): BuildingTypeDataRepository {
            return new BuildingTypeDataRepository($pimple['db']);
        };

        $pimple[BuildingPointRepository::class] = function (Container $pimple): BuildingPointRepository {
            return new BuildingPointRepository($pimple['db']);
        };

        $pimple[BuildingRequirementRepository::class] = function (Container $pimple): BuildingRequirementRepository {
            return new BuildingRequirementRepository($pimple['db']);
        };

        $pimple[BuildingService::class] = function (Container $pimple): BuildingService {
            return new BuildingService(
                $pimple[ConfigurationService::class],
                $pimple[SpecialistService::class],
                $pimple[RaceDataRepository::class],
                $pimple[PlanetTypeRepository::class],
                $pimple[SolarTypeRepository::class],
            );
        };
    }
}
