<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Market\MarketHandler;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\MarketRateUpdateTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MarketRateUpdateHandler implements MessageHandlerInterface
{
    private MarketHandler $marketHandler;

    public function __construct(MarketHandler $marketHandler)
    {
        $this->marketHandler = $marketHandler;
    }

    public function __invoke(MarketRateUpdateTask $task): SuccessResult
    {
        $this->marketHandler->updateRates();

        return SuccessResult::create("Rohstoff-Raten im Markt aktualisiert");
    }
}
