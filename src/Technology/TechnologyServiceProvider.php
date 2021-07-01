<?php declare(strict_types=1);

namespace EtoA\Technology;

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
    }
}
