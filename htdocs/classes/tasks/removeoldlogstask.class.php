<?PHP

use EtoA\Core\Logging\BattleLog;
use EtoA\Core\Logging\FleetLog;
use EtoA\Core\Logging\GameLog;
use EtoA\Core\Logging\Log;
use Pimple\Container;

/**
 * Remove old logs
 */
class RemoveOldLogsTask implements IPeriodicTask
{
    private Log $log;
    private GameLog $gameLog;
    private BattleLog $battleLog;
    private FleetLog $fleetLog;

    function __construct(Container $app)
    {
        $this->log = $app['etoa.log.service'];
        $this->gameLog = $app['etoa.log.game.service'];
        $this->battleLog = $app['etoa.log.battle.service'];
        $this->fleetLog = $app['etoa.log.fleet.service'];
    }

    function run()
    {
        $nr = $this->log->removeOld();
        $nr += $this->gameLog->removeOld();
        $nr += $this->battleLog->removeOld();
        $nr += $this->fleetLog->removeOld();
        return "$nr alte Logs gelöscht";
    }

    function getDescription()
    {
        return "Alte Logs löschen";
    }
}
