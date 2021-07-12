<?php declare(strict_types=1);

namespace EtoA\Tip;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TipServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[TipRepository::class] = function (Container $pimple): TipRepository {
            return new TipRepository($pimple['db']);
        };
    }
}
