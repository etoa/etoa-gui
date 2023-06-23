<?php

declare(strict_types=1);

namespace EtoA\Text;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TextServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[TextRepository::class] = function (Container $pimple): TextRepository {
            return new TextRepository($pimple['db']);
        };
    }
}
