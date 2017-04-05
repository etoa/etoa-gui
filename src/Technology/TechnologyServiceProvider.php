<?php

namespace EtoA\Technology;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TechnologyServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['etoa.technology.techlistrepository'] = function (Container $pimple) {
            return new TechListRepository($pimple['db']);
        };
    }
}
