<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\PermuteWormholesTask;
use EtoA\Universe\Wormhole\WormholeService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PermuteWormholesHandler implements MessageHandlerInterface
{
    private WormholeService $wormholeService;

    public function __construct(WormholeService $wormholeService)
    {
        $this->wormholeService = $wormholeService;
    }

    public function __invoke(PermuteWormholesTask $task): SuccessResult
    {
        $this->wormholeService->randomize();

        return SuccessResult::create("Wurmlöcher vertauscht");
    }
}
