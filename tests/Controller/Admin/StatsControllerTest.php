<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class StatsControllerTest extends SymfonyWebTestCase
{
    public function testUsers(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/stats/users');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testBattles(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/stats/battles');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testTrade(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/stats/trade');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testTitles(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/stats/titles');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
