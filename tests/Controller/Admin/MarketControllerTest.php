<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class MarketControllerTest extends SymfonyWebTestCase
{
    public function testIndex(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/market/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testResources(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/market/resources');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testShips(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/market/ships');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testAuctions(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/market/auctions');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
