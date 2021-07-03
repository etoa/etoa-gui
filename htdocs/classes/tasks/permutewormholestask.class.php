<?php

use EtoA\Universe\Wormhole\WormholeService;
use Pimple\Container;

/**
 * Permute wormholes
 */
class PermuteWormholesTask implements IPeriodicTask
{
    private WormholeService $wormholeService;

    function __construct(Container $app)
    {
        $this->wormholeService = $app[WormholeService::class];
    }

    function run()
    {
        $this->wormholeService->randomize();
        return "Wurmlöcher vertauscht";
    }

    function getDescription()
    {
        return "Wurmlöcher vertauschen";
    }
}
