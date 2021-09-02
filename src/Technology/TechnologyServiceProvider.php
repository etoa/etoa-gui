<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Race\RaceDataRepository;
use EtoA\Specialist\SpecialistService;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TechnologyServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[TechnologyRepository::class] = function (Container $pimple): TechnologyRepository {
            return new TechnologyRepository($pimple['db']);
        };

        $pimple[TechnologyDataRepository::class] = function (Container $pimple): TechnologyDataRepository {
            return new TechnologyDataRepository($pimple['db']);
        };

        $pimple[TechnologyTypeRepository::class] = function (Container $pimple): TechnologyTypeRepository {
            return new TechnologyTypeRepository($pimple['db']);
        };

        $pimple[TechnologyRequirementRepository::class] = function (Container $pimple): TechnologyRequirementRepository {
            return new TechnologyRequirementRepository($pimple['db']);
        };

        $pimple[TechnologyPointRepository::class] = function (Container $pimple): TechnologyPointRepository {
            return new TechnologyPointRepository($pimple['db']);
        };

        $pimple[TechnologyService::class] = function (Container $pimple): TechnologyService {
            return new TechnologyService(
                $pimple[ConfigurationService::class],
                $pimple[SpecialistService::class],
                $pimple[RaceDataRepository::class],
                $pimple[PlanetTypeRepository::class],
                $pimple[SolarTypeRepository::class],
            );
        };
    }
}
