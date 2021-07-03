<?php

declare(strict_types=1);

namespace EtoA\UI;

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UIServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[ResourceBoxDrawer::class] = function (Container $pimple): ResourceBoxDrawer {
            return new ResourceBoxDrawer($pimple[ConfigurationService::class]);
        };
    }
}
