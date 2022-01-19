<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class FleetControllerTest extends SymfonyWebTestCase
{
    public function testSearch(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/fleets/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testNew(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/fleets/new');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testSendShips(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/fleets/send-ships');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testOptions(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/fleets/options');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
