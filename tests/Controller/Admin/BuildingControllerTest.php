<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class BuildingControllerTest extends SymfonyWebTestCase
{
    public function testSearch(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/buildings/search');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testPoints(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/buildings/points');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testRequirements(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/buildings/requirements');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
