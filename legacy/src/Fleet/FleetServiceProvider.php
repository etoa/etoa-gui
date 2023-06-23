<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Message\MessageRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Specialist\SpecialistService;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FleetServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple): void
    {
        $pimple[FleetRepository::class] = function (Container $pimple): FleetRepository {
            return new FleetRepository($pimple['db']);
        };

        $pimple[FleetService::class] = function (Container $pimple): FleetService {
            return new FleetService(
                $pimple[PlanetRepository::class],
                $pimple[EntityRepository::class],
                $pimple[FleetRepository::class],
                $pimple[ShipRepository::class]
            );
        };

        $pimple[ForeignFleetLoader::class] = function (Container $pimple): ForeignFleetLoader {
            return new ForeignFleetLoader(
                $pimple[FleetRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[SpecialistService::class],
            );
        };

        $pimple[FleetScanService::class] = function (Container $pimple): FleetScanService {
            return new FleetScanService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[EntityRepository::class],
                $pimple[FleetRepository::class],
                $pimple[EntityService::class],
                $pimple[DefenseRepository::class],
                $pimple[TechnologyRepository::class],
                $pimple[ShipDataRepository::class],
                $pimple[MessageRepository::class],
                $pimple[AllianceBuildingRepository::class],
                $pimple[AllianceTechnologyRepository::class],
                $pimple[AllianceRepository::class],
                $pimple[AllianceHistoryRepository::class],
                $pimple[SpecialistDataRepository::class]
            );
        };
    }
}
