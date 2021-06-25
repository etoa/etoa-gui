<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.log.repository'] = function (Container $pimple): LogRepository {
            return new LogRepository($pimple['db']);
        };

        $pimple['etoa.log.service'] = function (Container $pimple): Log {
            return new Log(
                $pimple['etoa.log.repository'],
                $pimple['etoa.config.repository']
            );
        };

        $pimple['etoa.log.game.repository'] = function (Container $pimple): GameLogRepository {
            return new GameLogRepository($pimple['db']);
        };

        $pimple['etoa.log.game.service'] = function (Container $pimple): GameLog {
            return new GameLog(
                $pimple['etoa.log.game.repository'],
                $pimple['etoa.config.repository'],
                $pimple['etoa.log.repository']
            );
        };

        $pimple['etoa.log.battle.repository'] = function (Container $pimple): BattleLogRepository {
            return new BattleLogRepository($pimple['db']);
        };

        $pimple['etoa.log.battle.service'] = function (Container $pimple): BattleLog {
            return new BattleLog(
                $pimple['etoa.log.battle.repository'],
                $pimple['etoa.config.repository'],
                $pimple['etoa.log.repository']
            );
        };

        $pimple['etoa.log.fleet.repository'] = function (Container $pimple): FleetLogRepository {
            return new FleetLogRepository($pimple['db']);
        };

        $pimple['etoa.log.fleet.service'] = function (Container $pimple): FleetLog {
            return new FleetLog(
                $pimple['etoa.log.fleet.repository'],
                $pimple['etoa.config.repository'],
                $pimple['etoa.log.repository']
            );
        };
    }
}
