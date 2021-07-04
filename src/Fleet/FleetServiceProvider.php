<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
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
                $pimple[FleetRepository::class]
            );
        };
    }
}
