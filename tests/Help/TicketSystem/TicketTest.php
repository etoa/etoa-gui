<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    public function testGetIdString(): void
    {
        $ticket = new Ticket();
        $ticket->id = 123;

        $this->assertEquals('#000123', $ticket->getIdString());
    }
}
