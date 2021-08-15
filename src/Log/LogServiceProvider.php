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

        $pimple[BattleLogRepository::class] = function (Container $pimple): BattleLogRepository {
            return new BattleLogRepository($pimple['db']);
        };

        $pimple[FleetLogRepository::class] = function (Container $pimple): FleetLogRepository {
            return new FleetLogRepository($pimple['db']);
        };

        $pimple[GameLogRepository::class] = function (Container $pimple): GameLogRepository {
            return new GameLogRepository($pimple['db']);
        };

        $pimple[AccessLogRepository::class] = function (Container $pimple): AccessLogRepository {
            return new AccessLogRepository($pimple['db']);
        };
    }
}
