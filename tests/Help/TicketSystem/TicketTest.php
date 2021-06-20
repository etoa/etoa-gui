<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\AbstractDbTestCase;

class TicketTest extends AbstractDbTestCase
{
    public function testGetIdString()
    {
        $ticket = new Ticket();
        $ticket->id = 123;

        $this->assertEquals('#000123', $ticket->getIdString());
    }
}
