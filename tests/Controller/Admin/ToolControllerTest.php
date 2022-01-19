<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class ToolControllerTest extends SymfonyWebTestCase
{
    public function testIndex(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/tools/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testFilesharing(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/tools/filesharing');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testAccessLog(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/tools/accesslog/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testIpResolver(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/tools/ip-resolver');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
