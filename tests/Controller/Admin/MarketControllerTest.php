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

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testResources(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/market/resources');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }
}
