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

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testSend(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/messages/send');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testReports(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/messages/reports');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
