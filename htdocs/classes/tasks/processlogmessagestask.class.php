<?PHP

use EtoA\Core\Logging\BattleLog;
use EtoA\Core\Logging\FleetLog;
use EtoA\Core\Logging\GameLog;
use EtoA\Core\Logging\Log;
use Pimple\Container;

/**
    * Process log messages
    */
    class ProcessLogMessagesTask implements IPeriodicTask
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
            $nr = $this->log->processQueue();
            $nr+= $this->gameLog->processQueue();
            $nr+= $this->battleLog->processQueue();
            $nr+= $this->fleetLog->processQueue();
            return "$nr Log Nachrichten verarbeitet";
        }

        function getDescription() {
        return "Log-Nachrichten verarbeiten";
        }
    }
