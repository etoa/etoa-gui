<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\SymfonyWebTestCase;

class ShipControllerTest extends SymfonyWebTestCase
{
    public function testSearch(): void
    {
        $userId = 1;

        $client = self::createClient();

        $this->loginUser($userId);

        $client->request('GET', '/api/ships/search?q=Algo');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
