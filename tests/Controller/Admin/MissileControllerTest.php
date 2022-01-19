<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class MissileControllerTest extends SymfonyWebTestCase
{
    public function testIndex(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/missiles/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testRequirements(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/missiles/requirements');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
