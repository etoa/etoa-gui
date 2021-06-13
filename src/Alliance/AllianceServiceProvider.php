<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Alliance\AllianceRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AllianceServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple): void
    {
        $pimple['etoa.alliance.repository'] = function (Container $pimple): AllianceRepository {
            return new AllianceRepository($pimple['db']);
        };
    }
}
