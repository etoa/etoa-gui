<?php declare(strict_types=1);

namespace EtoA\Specialist;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SpecialistServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.specialist.datarepository'] = function (Container $pimple): SpecialistDataRepository {
            return new SpecialistDataRepository($pimple['db']);
        };
    }
}
