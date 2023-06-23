<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class DefenseControllerTest extends SymfonyWebTestCase
{
    public function testSearch(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/defense/search');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testQueue(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/defense/queue');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testPoints(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/defense/points');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testRequirements(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/defense/requirements');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
