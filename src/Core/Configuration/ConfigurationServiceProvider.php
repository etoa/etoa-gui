<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigurationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[ConfigurationRepository::class] = function (Container $pimple): ConfigurationRepository {
            return new ConfigurationRepository($pimple['db']);
        };

        $pimple[ConfigurationDefinitionsRepository::class] = function (): ConfigurationDefinitionsRepository {
            return new ConfigurationDefinitionsRepository();
        };

        $pimple[ConfigurationService::class] = function (Container $pimple): ConfigurationService {
            return new ConfigurationService(
                $pimple[ConfigurationRepository::class],
                $pimple[ConfigurationDefinitionsRepository::class]
            );
        };
    }
}
