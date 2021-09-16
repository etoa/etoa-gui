<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\SymfonyWebTestCase;

class ShipControllerTest extends SymfonyWebTestCase
{
    public function testSearch(): void
    {
        $client = self::createClient();

        $this->loginUser(1);

        $client->request('GET', '/api/ships/search?q=Algo');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testSearchInfo(): void
    {
        $client = self::createClient();

        $this->loginUser(1);

        $client->request('GET', '/api/ships/search-info?ship=1');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
