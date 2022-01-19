<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class TicketControllerTest extends SymfonyWebTestCase
{
    public function testActive(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/tickets/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testClosed(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/tickets/closed');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testNew(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/tickets/new');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
