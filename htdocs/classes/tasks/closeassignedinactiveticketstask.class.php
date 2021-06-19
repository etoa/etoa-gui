<?php

use EtoA\Help\TicketSystem\TicketRepository;
use Pimple\Container;

/**
 * Close open tickets that are answered by an admin and are inactive
 */
class CloseAssignedInactiveTicketsTask implements IPeriodicTask
{
	private TicketRepository $ticketRepo;

	function __construct(Container $app)
	{
		$this->ticketRepo = $app['etoa.help.ticket.repository'];
	}

	function run()
	{
		$this->ticketRepo->closeAssignedInactive();
		return "Inaktive Tickets geschlossen";
	}

	function getDescription()
	{
		return "Inaktive Tickets schliessen welche von einem Admin beantwortet wurden";
	}
}
