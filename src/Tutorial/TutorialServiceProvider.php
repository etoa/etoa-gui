<?php

namespace EtoA\Tutorial;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TutorialServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['etoa.tutorial.userprogressrepository'] = function (Container $pimple) {
            return new TutorialUserProgressRepository($pimple['db']);
        };
    }
}
