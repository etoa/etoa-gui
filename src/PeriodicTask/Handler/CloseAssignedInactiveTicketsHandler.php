<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Help\TicketSystem\TicketService;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CloseAssignedInactiveTicketsTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CloseAssignedInactiveTicketsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly TicketService $ticketService
    )
    {}

    public function __invoke(CloseAssignedInactiveTicketsTask $task): SuccessResult
    {
        $this->ticketService->closeAssignedInactive();

        return SuccessResult::create("Inaktive Tickets geschlossen");
    }
}
