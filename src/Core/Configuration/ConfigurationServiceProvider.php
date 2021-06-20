<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigurationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.config.repository'] = function (Container $pimple): ConfigurationRepository {
            return new ConfigurationRepository($pimple['db']);
        };

        $pimple['etoa.config.service'] = function (Container $pimple): ConfigurationService {
            return new ConfigurationService($pimple['etoa.config.repository']);
        };
    }
}

