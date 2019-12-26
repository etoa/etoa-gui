<?php declare(strict_types=1);

namespace EtoA\Technology;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TechnologyServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.technology.repository'] = function (Container $pimple): TechnologyRepository {
            return new TechnologyRepository($pimple['db']);
        };
    }
}
