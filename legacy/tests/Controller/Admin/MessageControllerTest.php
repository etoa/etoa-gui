<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class MessageControllerTest extends SymfonyWebTestCase
{
    public function testSearch(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/messages/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testSend(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/messages/send');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testReports(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/messages/reports');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
