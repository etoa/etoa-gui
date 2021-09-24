<?php

use EtoA\Help\TicketSystem\TicketService;
use Pimple\Container;

/**
 * Close open tickets that are answered by an admin and are inactive
 */
class CloseAssignedInactiveTicketsTask implements IPeriodicTask
{
    private TicketService $ticketService;

    function __construct(Container $app)
    {
        $this->ticketService = $app[TicketService::class];
    }

    function run()
    {
        $this->ticketService->closeAssignedInactive();
        return "Inaktive Tickets geschlossen";
    }

    public static function getDescription()
    {
        return "Inaktive Tickets schliessen welche von einem Admin beantwortet wurden";
    }
}
