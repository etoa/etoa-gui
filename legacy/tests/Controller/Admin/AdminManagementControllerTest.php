<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class AdminManagementControllerTest extends SymfonyWebTestCase
{
    public function testList(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/admin-management/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testNew(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/admin-management/new');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testEdit(): void
    {
        $client = self::createClient();

        $admin = $this->loginAdmin($client);

        $client->request('GET', sprintf('/admin/admin-management/%s/edit', $admin->id));

        $this->assertStatusCode(200, $client->getResponse());
    }
}
