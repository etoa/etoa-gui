<?PHP

use EtoA\Market\MarketHandler;
use Pimple\Container;

/**
 * Update market resource rates
 */
class MarketrateUpdateTask implements IPeriodicTask
{
    private MarketHandler $marketHandler;

    public function __construct(Container $app)
    {
        $this->marketHandler = $app[MarketHandler::class];
    }

    function run()
    {
        $this->marketHandler->updateRates();

        return "Rohstoff-Raten im Markt aktualisiert";
    }

    function getDescription()
    {
        return "Markt-Ressourcen Verhältnisse aktualisieren";
    }
}
