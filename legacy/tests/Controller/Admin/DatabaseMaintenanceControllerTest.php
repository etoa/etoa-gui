<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class DatabaseMaintenanceControllerTest extends SymfonyWebTestCase
{
    public function testBackups(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/db/');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
