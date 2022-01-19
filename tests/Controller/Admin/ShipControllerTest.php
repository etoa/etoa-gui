<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class ShipControllerTest extends SymfonyWebTestCase
{
    public function testSearch(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/ships/search');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testQueue(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/ships/queue');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testPoints(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/ships/points');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testRequirements(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/ships/requirements');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testXpCalculator(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/ships/xp-calculator');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
