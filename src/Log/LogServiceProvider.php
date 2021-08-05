<?php declare(strict_types=1);

namespace EtoA\Log;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[LogRepository::class] = function (Container $pimple): LogRepository {
            return new LogRepository($pimple['db']);
        };

        $pimple[AccessLogRepository::class] = function (Container $pimple): AccessLogRepository {
            return new AccessLogRepository($pimple['db']);
        };
    }
}
