<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Help\TicketSystem\TicketService;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CloseAssignedInactiveTicketsTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CloseAssignedInactiveTicketsHandler implements MessageHandlerInterface
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function __invoke(CloseAssignedInactiveTicketsTask $task): SuccessResult
    {
        $this->ticketService->closeAssignedInactive();

        return SuccessResult::create("Inaktive Tickets geschlossen");
    }
}
