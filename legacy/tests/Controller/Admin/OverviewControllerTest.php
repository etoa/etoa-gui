<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\SymfonyWebTestCase;

class OverviewControllerTest extends SymfonyWebTestCase
{
    public function testIndex(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/overview/');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testChangelog(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/overview/changelog');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testGamestats(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/overview/gamestats');

        $this->assertStatusCode(200, $client->getResponse());
    }

    public function testSystemInfo(): void
    {
        $client = self::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/overview/sysinfo');

        $this->assertStatusCode(200, $client->getResponse());
    }
}
