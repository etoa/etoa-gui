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
		$this->ticketService = $app['etoa.help.ticket.service'];
	}

	function run()
	{
		$this->ticketService->closeAssignedInactive();
		return "Inaktive Tickets geschlossen";
	}

	function getDescription()
	{
		return "Inaktive Tickets schliessen welche von einem Admin beantwortet wurden";
	}
}
