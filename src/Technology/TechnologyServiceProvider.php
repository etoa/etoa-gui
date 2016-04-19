<?php

namespace EtoA\Technology;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TechnologyServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple)
    {
        $pimple['etoa.technology.datarepository'] = function (Container $pimple) {
            return new TechnologyDataRepository($pimple['db.querybuilder']);
        };
    }
}
